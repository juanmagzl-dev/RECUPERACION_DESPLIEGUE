# Despliegue - Minesweeper Map Generator

## 📋 Requisitos

- Docker y Docker Compose
- Apache 2.4+ (para despliegue nativo)
- PHP 8.1+
- OpenSSL para certificados SSL

## 🐳 Despliegue con Docker (Recomendado)

### Construcción y Despliegue Rápido

```bash
# Clonar el repositorio
git clone https://github.com/tu-usuario/minesweeper-map-generator.git
cd minesweeper-map-generator

# Construcción con script
chmod +x docker/build.sh
./docker/build.sh

# Despliegue con script
chmod +x docker/deploy.sh
./docker/deploy.sh
```

### Despliegue Manual con Docker Compose

```bash
# Construir y ejecutar
docker-compose up -d --build

# Ver logs
docker-compose logs -f

# Detener
docker-compose down
```

### Despliegue Manual sin Docker Compose

Si no tienes docker-compose instalado, puedes usar los scripts alternativos:

**En Windows:**
```bash
# Ejecutar script batch
docker/deploy-manual.bat
```

**En Linux/Mac:**
```bash
# Hacer ejecutable y ejecutar
chmod +x docker/deploy-manual.sh
./docker/deploy-manual.sh
```

**Comandos manuales:**
```bash
# Construir imagen
docker build -t minesweeper-map-generator:latest .

# Ejecutar contenedor
docker run -d \
  -p 8080:443 \
  -p 8000:80 \
  --name minesweeper-map-generator \
  --restart unless-stopped \
  minesweeper-map-generator:latest
```

### Comandos Docker Individuales

```bash
# Construir imagen
docker build -t minesweeper-map-generator:latest .

# Ejecutar contenedor
docker run -d \
  -p 8080:443 \
  -p 8000:80 \
  --name minesweeper-web \
  minesweeper-map-generator:latest
```

## 🌐 Acceso a la Aplicación

- **HTTPS**: https://localhost:8080
- **HTTP** (redirige a HTTPS): http://localhost:8000

### Dominios Configurados

Para usar los dominios configurados, añade a tu archivo `/etc/hosts` (Linux/Mac) o `C:\Windows\System32\drivers\etc\hosts` (Windows):

```
127.0.0.1 www.minesweepermapgenerator.com
127.0.0.1 www.minesweepermapgenerator.es
```

Luego accede mediante:
- https://www.minesweepermapgenerator.com:8080
- https://www.minesweepermapgenerator.es:8080

## 🔐 Autenticación

Para las funcionalidades de carga y guardado de mapas:

- **Usuario**: `mapmanager`
- **Contraseña**: `mapmanager123`

## ⚙️ Configuración Apache

### Características Implementadas

- ✅ **HTTPS obligatorio** (HTTP redirige a HTTPS)
- ✅ **Indexado desactivado** (`Options -Indexes`)
- ✅ **Autenticación por grupo** para carga de mapas
- ✅ **Dominio**: www.minesweepermapgenerator.com / www.minesweepermapgenerator.es
- ✅ **Raíz del sitio**: /var/www/https
- ✅ **Sin XAMPP** (Apache nativo en Ubuntu)

### Estructura de Archivos Apache

```
apache/
├── sites-available/
│   └── minesweeper-ssl.conf    # Configuración SSL
├── ssl/
│   └── generate-ssl-cert.sh    # Script generación SSL
└── setup-auth.sh               # Script configuración autenticación
```

## 🛠️ Despliegue Nativo en Ubuntu

```bash
# Instalar dependencias
sudo apt update
sudo apt install apache2 php8.1 libapache2-mod-php8.1 openssl

# Copiar configuración
sudo cp apache/sites-available/minesweeper-ssl.conf /etc/apache2/sites-available/

# Habilitar módulos
sudo a2enmod rewrite ssl headers expires

# Habilitar sitio
sudo a2ensite minesweeper-ssl.conf
sudo a2dissite 000-default.conf

# Crear directorio web
sudo mkdir -p /var/www/https
sudo cp -r src/* /var/www/https/
sudo cp .htaccess /var/www/https/
sudo chown -R www-data:www-data /var/www/https

# Generar certificados SSL
sudo chmod +x apache/ssl/generate-ssl-cert.sh
sudo ./apache/ssl/generate-ssl-cert.sh

# Configurar autenticación
sudo chmod +x apache/setup-auth.sh
sudo ./apache/setup-auth.sh

# Reiniciar Apache
sudo systemctl restart apache2
```

## 📊 Verificación del Despliegue

### Health Check

```bash
# Docker
docker-compose ps
curl -k https://localhost:8080/api/info

# Nativo
systemctl status apache2
curl -k https://www.minesweepermapgenerator.com/api/info
```

### Logs

```bash
# Docker
docker-compose logs -f

# Nativo
sudo tail -f /var/log/apache2/minesweeper_access.log
sudo tail -f /var/log/apache2/minesweeper_error.log
```

## 🔒 Certificados SSL

### Producción

Para producción, reemplaza los certificados autofirmados con certificados válidos:

```bash
# Copiar certificados reales
sudo cp tu-certificado.crt /etc/ssl/certs/minesweeper.crt
sudo cp tu-clave-privada.key /etc/ssl/private/minesweeper.key

# Verificar permisos
sudo chmod 644 /etc/ssl/certs/minesweeper.crt
sudo chmod 600 /etc/ssl/private/minesweeper.key

# Reiniciar Apache
sudo systemctl restart apache2
```

### Let's Encrypt (Recomendado para Producción)

```bash
sudo apt install certbot python3-certbot-apache
sudo certbot --apache -d www.minesweepermapgenerator.com -d www.minesweepermapgenerator.es
```

## 🎯 URLs de la Aplicación

- **Página Principal**: `/`
- **API Info**: `/api/info`
- **Generar Mapa**: `POST /api/generate`
- **Guardar Mapa**: `POST /api/save` (requiere autenticación)
- **Cargar Mapa**: `POST /api/load` (requiere autenticación)
- **Dificultades**: `GET /api/difficulties`

## 🔧 Solución de Problemas

### Puerto 8080 en Uso

```bash
# Ver qué proceso usa el puerto
sudo netstat -tulnp | grep :8080
# o
sudo lsof -i :8080

# Cambiar puerto en docker-compose.yml
ports:
  - "8081:443"  # Cambiar 8080 por 8081
```

### Error de Certificados

```bash
# Regenerar certificados
sudo ./apache/ssl/generate-ssl-cert.sh
sudo systemctl restart apache2
```

### Problemas de Autenticación

```bash
# Verificar archivo de usuarios
sudo cat /etc/apache2/.htpasswd

# Regenerar autenticación
sudo ./apache/setup-auth.sh
```

## 📝 Notas de Seguridad

- Los certificados SSL incluidos son autofirmados (solo para desarrollo)
- La contraseña de autenticación está en texto plano (cambiar en producción)
- Los headers de seguridad están configurados para máxima protección
- Directory listing está deshabilitado globalmente 