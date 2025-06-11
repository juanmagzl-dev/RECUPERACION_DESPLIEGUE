<?php
/**
 * API REST para MinesweeperMap
 * Maneja peticiones HTTP para generar, guardar y cargar mapas
 * Proyecto de Recuperación - Despliegue
 */

require_once 'MinesweeperMap.php';

// Configuración básica
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Headers para CORS
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Content-Type: application/json');

// Manejar preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

/**
 * Función para enviar respuesta JSON
 */
function sendJsonResponse($data, $status = 200) {
    http_response_code($status);
    echo json_encode($data, JSON_PRETTY_PRINT);
    exit;
}

/**
 * Función para enviar error JSON
 */
function sendError($message, $status = 400) {
    sendJsonResponse([
        'error' => true,
        'message' => $message,
        'timestamp' => date('Y-m-d H:i:s')
    ], $status);
}

try {
    $method = $_SERVER['REQUEST_METHOD'];
    $action = $_GET['action'] ?? '';
    
    switch ($method) {
        case 'POST':
            handlePostRequest($action);
            break;
            
        case 'GET':
            handleGetRequest($action);
            break;
            
        default:
            sendError("Método HTTP no soportado: {$method}", 405);
    }
    
} catch (Exception $e) {
    sendError($e->getMessage(), 500);
}

/**
 * Maneja peticiones POST
 */
function handlePostRequest($action) {
    $input = json_decode(file_get_contents('php://input'), true);
    
    if ($input === null) {
        sendError("JSON inválido en el cuerpo de la petición");
    }
    
    switch ($action) {
        case 'generate':
            generateMap($input);
            break;
            
        case 'generate_difficulty':
            generateMapFromDifficulty($input);
            break;
            
        case 'save':
            saveMap($input);
            break;
            
        case 'load':
            loadMap($input);
            break;
            
        default:
            sendError("Acción no válida: {$action}");
    }
}

/**
 * Maneja peticiones GET
 */
function handleGetRequest($action) {
    switch ($action) {
        case 'difficulties':
            getDifficulties();
            break;
            
        case 'info':
            getApiInfo();
            break;
            
        default:
            sendError("Acción GET no válida: {$action}");
    }
}

/**
 * Genera un mapa con parámetros personalizados
 */
function generateMap($input) {
    $rows = $input['rows'] ?? 0;
    $cols = $input['cols'] ?? 0;
    $mines = $input['mines'] ?? 0;
    
    if (!$rows || !$cols || !$mines) {
        sendError("Parámetros requeridos: rows, cols, mines");
    }
    
    try {
        $map = new MinesweeperMap($rows, $cols, $mines);
        $map->generateMap();
        
        sendJsonResponse([
            'success' => true,
            'message' => 'Mapa generado exitosamente',
            'data' => [
                'info' => $map->getMapInfo(),
                'map' => $map->getMap()
            ],
            'timestamp' => date('Y-m-d H:i:s')
        ]);
        
    } catch (Exception $e) {
        sendError("Error generando mapa: " . $e->getMessage());
    }
}

/**
 * Genera un mapa desde un nivel de dificultad
 */
function generateMapFromDifficulty($input) {
    $difficulty = $input['difficulty'] ?? '';
    
    if (!$difficulty) {
        sendError("Parámetro requerido: difficulty");
    }
    
    try {
        $map = MinesweeperMap::fromDifficulty($difficulty);
        $map->generateMap();
        
        sendJsonResponse([
            'success' => true,
            'message' => "Mapa generado exitosamente (dificultad: {$difficulty})",
            'data' => [
                'info' => $map->getMapInfo(),
                'map' => $map->getMap(),
                'difficulty' => $difficulty
            ],
            'timestamp' => date('Y-m-d H:i:s')
        ]);
        
    } catch (Exception $e) {
        sendError("Error generando mapa: " . $e->getMessage());
    }
}

/**
 * Guarda un mapa (devuelve el contenido para descarga)
 */
function saveMap($input) {
    $mapData = $input['mapData'] ?? null;
    $format = $input['format'] ?? 'json';
    
    if (!$mapData) {
        sendError("Parámetro requerido: mapData");
    }
    
    try {
        // Recrear el mapa desde los datos
        $map = new MinesweeperMap($mapData['rows'], $mapData['cols'], $mapData['mines']);
        $map->generateMap(); // Temporal, idealmente cargaríamos el mapa exacto
        
        $content = '';
        $mimeType = '';
        $extension = '';
        
        switch (strtolower($format)) {
            case 'json':
                $content = $map->toJson();
                $mimeType = 'application/json';
                $extension = 'json';
                break;
                
            case 'xml':
                $content = $map->toXml();
                $mimeType = 'application/xml';
                $extension = 'xml';
                break;
                
            default:
                sendError("Formato no soportado: {$format}");
        }
        
        sendJsonResponse([
            'success' => true,
            'message' => 'Contenido preparado para descarga',
            'data' => [
                'content' => $content,
                'filename' => 'minesweeper_map_' . date('Y-m-d_H-i-s') . '.' . $extension,
                'mimeType' => $mimeType
            ],
            'timestamp' => date('Y-m-d H:i:s')
        ]);
        
    } catch (Exception $e) {
        sendError("Error guardando mapa: " . $e->getMessage());
    }
}

/**
 * Carga un mapa desde contenido JSON/XML
 */
function loadMap($input) {
    $content = $input['content'] ?? '';
    $format = $input['format'] ?? 'json';
    
    if (!$content) {
        sendError("Parámetro requerido: content");
    }
    
    try {
        $map = null;
        
        switch (strtolower($format)) {
            case 'json':
                $map = MinesweeperMap::fromJson($content);
                break;
                
            case 'xml':
                $map = MinesweeperMap::fromXml($content);
                break;
                
            default:
                sendError("Formato no soportado: {$format}");
        }
        
        sendJsonResponse([
            'success' => true,
            'message' => 'Mapa cargado exitosamente',
            'data' => [
                'info' => $map->getMapInfo(),
                'map' => $map->getMap()
            ],
            'timestamp' => date('Y-m-d H:i:s')
        ]);
        
    } catch (Exception $e) {
        sendError("Error cargando mapa: " . $e->getMessage());
    }
}

/**
 * Devuelve los niveles de dificultad disponibles
 */
function getDifficulties() {
    sendJsonResponse([
        'success' => true,
        'data' => [
            'easy' => ['rows' => 9, 'cols' => 9, 'mines' => 10, 'name' => 'Fácil'],
            'medium' => ['rows' => 16, 'cols' => 16, 'mines' => 40, 'name' => 'Medio'],
            'expert' => ['rows' => 30, 'cols' => 16, 'mines' => 99, 'name' => 'Experto'],
            'custom' => ['name' => 'Personalizado']
        ],
        'timestamp' => date('Y-m-d H:i:s')
    ]);
}

/**
 * Información de la API
 */
function getApiInfo() {
    sendJsonResponse([
        'success' => true,
        'message' => 'API MinesweeperMap funcionando correctamente',
        'version' => '1.0.0',
        'endpoints' => [
            'POST /api.php?action=generate' => 'Genera mapa personalizado',
            'POST /api.php?action=generate_difficulty' => 'Genera mapa por dificultad',
            'POST /api.php?action=save' => 'Prepara mapa para descarga',
            'POST /api.php?action=load' => 'Carga mapa desde contenido',
            'GET /api.php?action=difficulties' => 'Lista niveles de dificultad',
            'GET /api.php?action=info' => 'Información de la API'
        ],
        'timestamp' => date('Y-m-d H:i:s')
    ]);
}
?> 