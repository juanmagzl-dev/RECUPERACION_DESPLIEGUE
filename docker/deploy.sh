#!/bin/bash

# Deployment script for Minesweeper Map Generator

echo "🚀 Deploying Minesweeper Map Generator..."

# Check if docker-compose is available
if ! command -v docker-compose &> /dev/null; then
    echo "❌ docker-compose is not installed!"
    exit 1
fi

# Stop existing containers
echo "🛑 Stopping existing containers..."
docker-compose down

# Build and start the services
echo "🏗️ Building and starting services..."
docker-compose up -d --build

if [ $? -eq 0 ]; then
    echo "✅ Deployment completed successfully!"
    echo ""
    echo "🌐 Application is available at:"
    echo "- HTTPS: https://localhost:8080"
    echo "- HTTP (redirects to HTTPS): http://localhost:8000"
    echo ""
    echo "🔐 Authentication for map management:"
    echo "- User: mapmanager"
    echo "- Password: mapmanager123"
    echo ""
    echo "📊 To check status:"
    echo "docker-compose ps"
    echo ""
    echo "📝 To view logs:"
    echo "docker-compose logs -f"
    echo ""
    echo "🛑 To stop:"
    echo "docker-compose down"
else
    echo "❌ Deployment failed!"
    exit 1
fi 