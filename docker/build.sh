#!/bin/bash

# Build script for Minesweeper Map Generator Docker image

echo "🚀 Building Minesweeper Map Generator Docker image..."

# Build the Docker image
docker build -t minesweeper-map-generator:latest .

if [ $? -eq 0 ]; then
    echo "✅ Build completed successfully!"
    echo "📦 Image: minesweeper-map-generator:latest"
    echo ""
    echo "To run the container:"
    echo "docker run -d -p 8080:443 -p 8000:80 --name minesweeper-web minesweeper-map-generator:latest"
    echo ""
    echo "Or use docker-compose:"
    echo "docker-compose up -d"
    echo ""
    echo "🌐 Access the application at:"
    echo "- HTTPS: https://localhost:8080"
    echo "- HTTP (redirects to HTTPS): http://localhost:8000"
else
    echo "❌ Build failed!"
    exit 1
fi 