#!/bin/bash

# Script to set up Apache authentication for Minesweeper Map Generator

# Create the users file with a sample user
# Password: mapmanager123
htpasswd -cb /etc/apache2/.htpasswd mapmanager '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'

# Add more users if needed
# htpasswd -b /etc/apache2/.htpasswd user2 password2

# Create the groups file
cat > /etc/apache2/.htgroups << 'EOF'
mapmanagers: mapmanager
EOF

# Set proper permissions
chown root:www-data /etc/apache2/.htpasswd
chown root:www-data /etc/apache2/.htgroups
chmod 640 /etc/apache2/.htpasswd
chmod 640 /etc/apache2/.htgroups

echo "Authentication setup completed!"
echo "User: mapmanager"
echo "Password: mapmanager123"
echo "Group: mapmanagers" 