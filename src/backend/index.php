<?php
/**
 * Punto de entrada principal del backend
 * Proyecto de Recuperación - Despliegue
 */

// Configuración básica
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Headers para CORS si es necesario
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Respuesta inicial
echo json_encode([
    'status' => 'success',
    'message' => 'Backend API inicializado correctamente',
    'timestamp' => date('Y-m-d H:i:s')
]);
?> 