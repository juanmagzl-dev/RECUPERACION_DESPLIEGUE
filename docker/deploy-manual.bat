@echo off
REM Script de despliegue manual para Minesweeper Map Generator (Windows)
REM Usa comandos Docker individuales en lugar de docker-compose

echo 🚀 Desplegando Minesweeper Map Generator (modo manual)...

REM Verificar si Docker está instalado
docker --version >nul 2>&1
if %errorlevel% neq 0 (
    echo ❌ Docker no está instalado!
    echo Por favor instala Docker Desktop desde: https://www.docker.com/products/docker-desktop/
    pause
    exit /b 1
)

REM Detener y eliminar contenedor existente si existe
echo 🛑 Deteniendo contenedor existente...
docker stop minesweeper-map-generator >nul 2>&1
docker rm minesweeper-map-generator >nul 2>&1

REM Construir la imagen
echo 🏗️ Construyendo imagen Docker...
docker build -t minesweeper-map-generator:latest .

if %errorlevel% equ 0 (
    echo ✅ Imagen construida exitosamente!
    
    REM Ejecutar el contenedor
    echo 🚀 Iniciando contenedor...
    docker run -d -p 8080:443 -p 8000:80 --name minesweeper-map-generator --restart unless-stopped minesweeper-map-generator:latest
    
    if %errorlevel% equ 0 (
        echo ✅ Despliegue completado exitosamente!
        echo.
        echo 🌐 Aplicación disponible en:
        echo - HTTPS: https://localhost:8080
        echo - HTTP (redirige a HTTPS): http://localhost:8000
        echo.
        echo 🔐 Autenticación para gestión de mapas:
        echo - Usuario: mapmanager
        echo - Contraseña: mapmanager123
        echo.
        echo 📊 Para verificar estado:
        echo docker ps
        echo.
        echo 📝 Para ver logs:
        echo docker logs minesweeper-map-generator
        echo.
        echo 🛑 Para detener:
        echo docker stop minesweeper-map-generator
        echo.
        pause
    ) else (
        echo ❌ Error al iniciar el contenedor!
        pause
        exit /b 1
    )
) else (
    echo ❌ Error en la construcción de la imagen!
    pause
    exit /b 1
) 