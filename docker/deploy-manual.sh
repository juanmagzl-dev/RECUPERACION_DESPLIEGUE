#!/bin/bash

# Script de despliegue manual para Minesweeper Map Generator
# Usa comandos Docker individuales en lugar de docker-compose

echo "🚀 Desplegando Minesweeper Map Generator (modo manual)..."

# Verificar si Docker está instalado
if ! command -v docker &> /dev/null; then
    echo "❌ Docker no está instalado!"
    echo "Por favor instala Docker Desktop desde: https://www.docker.com/products/docker-desktop/"
    exit 1
fi

# Detener y eliminar contenedor existente si existe
echo "🛑 Deteniendo contenedor existente..."
docker stop minesweeper-map-generator 2>/dev/null || true
docker rm minesweeper-map-generator 2>/dev/null || true

# Construir la imagen
echo "🏗️ Construyendo imagen Docker..."
docker build -t minesweeper-map-generator:latest .

if [ $? -eq 0 ]; then
    echo "✅ Imagen construida exitosamente!"
    
    # Ejecutar el contenedor
    echo "🚀 Iniciando contenedor..."
    docker run -d \
        -p 8080:443 \
        -p 8000:80 \
        --name minesweeper-map-generator \
        --restart unless-stopped \
        minesweeper-map-generator:latest
    
    if [ $? -eq 0 ]; then
        echo "✅ Despliegue completado exitosamente!"
        echo ""
        echo "🌐 Aplicación disponible en:"
        echo "- HTTPS: https://localhost:8080"
        echo "- HTTP (redirige a HTTPS): http://localhost:8000"
        echo ""
        echo "🔐 Autenticación para gestión de mapas:"
        echo "- Usuario: mapmanager"
        echo "- Contraseña: mapmanager123"
        echo ""
        echo "📊 Para verificar estado:"
        echo "docker ps"
        echo ""
        echo "📝 Para ver logs:"
        echo "docker logs minesweeper-map-generator"
        echo ""
        echo "🛑 Para detener:"
        echo "docker stop minesweeper-map-generator"
    else
        echo "❌ Error al iniciar el contenedor!"
        exit 1
    fi
else
    echo "❌ Error en la construcción de la imagen!"
    exit 1
fi 