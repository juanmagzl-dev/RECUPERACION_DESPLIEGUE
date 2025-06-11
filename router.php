<?php
/**
 * Router para el servidor PHP integrado
 * Maneja las rutas del frontend y backend
 */

$uri = $_SERVER['REQUEST_URI'];
$path = parse_url($uri, PHP_URL_PATH);

// Limpiar la ruta
$path = rtrim($path, '/');

// Rutas de la API (backend)
if (strpos($path, '/src/backend/api.php') !== false) {
    // Incluir la API
    include_once __DIR__ . '/src/backend/api.php';
    return true;
}

// Ruta del frontend principal
if ($path === '/src/frontend' || $path === '/src/frontend/') {
    $indexFile = __DIR__ . '/src/frontend/index.html';
    if (is_file($indexFile)) {
        header('Content-Type: text/html');
        readfile($indexFile);
        return true;
    }
}

// Archivos estáticos del frontend
if (strpos($path, '/src/frontend/') !== false) {
    $file = __DIR__ . $path;
    
    if (is_file($file)) {
        // Determinar el tipo MIME
        $ext = pathinfo($file, PATHINFO_EXTENSION);
        switch ($ext) {
            case 'css':
                header('Content-Type: text/css');
                break;
            case 'js':
                header('Content-Type: application/javascript');
                break;
            case 'html':
                header('Content-Type: text/html');
                break;
            case 'png':
                header('Content-Type: image/png');
                break;
            case 'jpg':
            case 'jpeg':
                header('Content-Type: image/jpeg');
                break;
            case 'gif':
                header('Content-Type: image/gif');
                break;
            case 'ico':
                header('Content-Type: image/x-icon');
                break;
        }
        
        readfile($file);
        return true;
    }
}

// Ruta raíz - mostrar página de bienvenida en lugar de redireccionar
if ($path === '' || $path === '/') {
    ?>
    <!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Minesweeper Map Generator</title>
        <style>
            body {
                font-family: Arial, sans-serif;
                text-align: center;
                padding: 50px;
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                color: white;
                min-height: 100vh;
                margin: 0;
                display: flex;
                flex-direction: column;
                justify-content: center;
            }
            .container {
                max-width: 600px;
                margin: 0 auto;
                background: rgba(255,255,255,0.1);
                padding: 2rem;
                border-radius: 10px;
                backdrop-filter: blur(10px);
            }
            h1 { color: #fff; font-size: 2.5rem; margin-bottom: 1rem; }
            p { font-size: 1.2rem; margin-bottom: 2rem; }
            .btn {
                display: inline-block;
                padding: 15px 30px;
                background: #28a745;
                color: white;
                text-decoration: none;
                border-radius: 5px;
                font-size: 1.1rem;
                margin: 0 10px;
                transition: background 0.3s;
            }
            .btn:hover { background: #218838; }
            .btn.secondary { background: #17a2b8; }
            .btn.secondary:hover { background: #138496; }
            .info {
                margin-top: 2rem;
                font-size: 0.9rem;
                opacity: 0.8;
            }
        </style>
    </head>
    <body>
        <div class="container">
            <h1>🎮 Minesweeper Map Generator</h1>
            <p>Proyecto de Recuperación - Despliegue</p>
            <p>Generador de mapas de buscaminas con diferentes niveles de dificultad</p>
            
            <a href="/src/frontend/" class="btn">🎮 Jugar Ahora</a>
            <a href="/src/backend/api.php?action=info" class="btn secondary">🔧 Info API</a>
            
            <div class="info">
                <p><strong>Funcionalidades:</strong></p>
                <ul style="text-align: left; display: inline-block;">
                    <li>Generación de mapas: Fácil, Medio, Experto y Personalizado</li>
                    <li>Guardado y carga de mapas en formato JSON</li>
                    <li>Visualización interactiva con CSS Grid</li>
                    <li>API REST completa con PHP</li>
                    <li>Tests automatizados con PHPUnit</li>
                </ul>
            </div>
        </div>
    </body>
    </html>
    <?php
    return true;
}

// 404 para rutas no encontradas
http_response_code(404);
echo "<!DOCTYPE html>
<html>
<head>
    <title>404 - No encontrado</title>
    <style>
        body { font-family: Arial, sans-serif; text-align: center; padding: 50px; }
        h1 { color: #e74c3c; }
        p { color: #666; }
        a { color: #3498db; text-decoration: none; }
        a:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <h1>404 - Página No Encontrada</h1>
    <p>La ruta solicitada no existe.</p>
    <p><a href='/src/frontend/'>← Volver al juego</a></p>
    <hr>
    <p><strong>Rutas disponibles:</strong></p>
    <ul style='text-align: left; display: inline-block;'>
        <li><a href='/src/frontend/'>🎮 Juego Buscaminas</a></li>
        <li><a href='/src/backend/api.php?action=info'>🔧 Info API</a></li>
    </ul>
</body>
</html>";
return true;
?> 