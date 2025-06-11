#!/bin/bash

# Script to generate self-signed SSL certificate for Minesweeper Map Generator

# Create SSL directory if it doesn't exist
mkdir -p /etc/ssl/certs
mkdir -p /etc/ssl/private

# Generate private key
openssl genrsa -out /etc/ssl/private/minesweeper.key 2048

# Generate certificate signing request
openssl req -new -key /etc/ssl/private/minesweeper.key -out /tmp/minesweeper.csr -subj "/C=ES/ST=Madrid/L=Madrid/O=Minesweeper Map Generator/OU=IT Department/CN=www.minesweepermapgenerator.com/emailAddress=admin@minesweepermapgenerator.com"

# Generate self-signed certificate
openssl x509 -req -days 365 -in /tmp/minesweeper.csr -signkey /etc/ssl/private/minesweeper.key -out /etc/ssl/certs/minesweeper.crt

# Set proper permissions
chmod 600 /etc/ssl/private/minesweeper.key
chmod 644 /etc/ssl/certs/minesweeper.crt

# Clean up
rm /tmp/minesweeper.csr

echo "SSL certificate generated successfully!"
echo "Certificate: /etc/ssl/certs/minesweeper.crt"
echo "Private key: /etc/ssl/private/minesweeper.key" 