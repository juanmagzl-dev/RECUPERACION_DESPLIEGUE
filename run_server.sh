#!/bin/bash

echo "===================================="
echo "  MINESWEEPER MAP GENERATOR SERVER"
echo "===================================="
echo ""

# Verificar si PHP está disponible
if ! command -v php &> /dev/null; then
    echo "[ERROR] PHP no está instalado o no está en el PATH del sistema."
    echo ""
    echo "Para usar esta aplicación necesitas instalar PHP:"
    echo "- Ubuntu/Debian: sudo apt install php"
    echo "- CentOS/RHEL: sudo yum install php"
    echo "- macOS: brew install php"
    echo ""
    exit 1
fi

echo "[INFO] PHP detectado correctamente"
php --version
echo ""

echo "[INFO] Iniciando servidor web en puerto 8000..."
echo "[INFO] Proyecto: Generador de Mapas Buscaminas"
echo "[INFO] URL Frontend: http://localhost:8000/src/frontend/"
echo "[INFO] URL API: http://localhost:8000/src/backend/api.php"
echo ""
echo "[INFO] Presiona Ctrl+C para detener el servidor"
echo "===================================="
echo ""

# Iniciar servidor PHP con router
php -S localhost:8000 router.php

echo ""
echo "[INFO] Servidor detenido" 