@echo off
echo ====================================
echo   MINESWEEPER MAP GENERATOR SERVER
echo ====================================
echo.

REM Verificar si PHP está disponible
php --version >nul 2>&1
if %errorlevel% neq 0 (
    echo [ERROR] PHP no está instalado o no está en el PATH del sistema.
    echo.
    echo Para usar esta aplicación necesitas instalar PHP:
    echo 1. Descarga PHP desde: https://www.php.net/downloads
    echo 2. O instala XAMPP desde: https://www.apachefriends.org/
    echo 3. Agrega PHP al PATH del sistema
    echo.
    pause
    exit /b 1
)

echo [INFO] PHP detectado correctamente
php --version
echo.

echo [INFO] Iniciando servidor web en puerto 8000...
echo [INFO] Proyecto: Generador de Mapas Buscaminas
echo [INFO] URL Frontend: http://localhost:8000/src/frontend/
echo [INFO] URL API: http://localhost:8000/src/backend/api.php
echo.
echo [INFO] Presiona Ctrl+C para detener el servidor
echo ====================================
echo.

REM Iniciar servidor PHP con router
php -S localhost:8000 router.php

echo.
echo [INFO] Servidor detenido
pause 