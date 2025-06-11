# Minesweeper Map Generator Docker Image
FROM ubuntu:22.04

# Maintainer information
LABEL maintainer="Minesweeper Map Generator Team"
LABEL version="1.0"
LABEL description="Dockerized Minesweeper Map Generator with Apache HTTPS"

# Set environment variables
ENV DEBIAN_FRONTEND=noninteractive
ENV APACHE_DOCUMENT_ROOT=/var/www/https
ENV APACHE_RUN_USER=www-data
ENV APACHE_RUN_GROUP=www-data
ENV APACHE_LOG_DIR=/var/log/apache2
ENV APACHE_LOCK_DIR=/var/lock/apache2
ENV APACHE_PID_FILE=/var/run/apache2/apache2.pid

# Install system dependencies
RUN apt-get update && apt-get install -y \
    apache2 \
    php8.1 \
    php8.1-cli \
    php8.1-common \
    php8.1-mysql \
    php8.1-xml \
    php8.1-xmlrpc \
    php8.1-curl \
    php8.1-gd \
    php8.1-imagick \
    php8.1-dev \
    php8.1-imap \
    php8.1-mbstring \
    php8.1-opcache \
    php8.1-soap \
    php8.1-zip \
    php8.1-intl \
    libapache2-mod-php8.1 \
    openssl \
    ca-certificates \
    curl \
    wget \
    unzip \
    apache2-utils \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Enable Apache modules
RUN a2enmod rewrite ssl headers expires php8.1

# Create web directory
RUN mkdir -p /var/www/https

# Copy application files
COPY . /var/www/https/

# Set proper permissions
RUN chown -R www-data:www-data /var/www/https \
    && chmod -R 755 /var/www/https

# Copy Apache configuration
COPY apache/sites-available/minesweeper-ssl.conf /etc/apache2/sites-available/
RUN a2ensite minesweeper-ssl.conf \
    && a2dissite 000-default.conf \
    && a2dissite default-ssl.conf

# Generate SSL certificate
COPY apache/ssl/generate-ssl-cert.sh /tmp/
RUN chmod +x /tmp/generate-ssl-cert.sh \
    && /tmp/generate-ssl-cert.sh \
    && rm /tmp/generate-ssl-cert.sh

# Set up authentication
COPY apache/setup-auth.sh /tmp/
RUN chmod +x /tmp/setup-auth.sh \
    && /tmp/setup-auth.sh \
    && rm /tmp/setup-auth.sh

# Install PHP dependencies if composer.json exists
WORKDIR /var/www/https
RUN if [ -f composer.json ]; then \
        composer install --no-dev --optimize-autoloader; \
    fi

# Create startup script
RUN echo '#!/bin/bash\n\
# Update hosts file for local development\n\
echo "127.0.0.1 www.minesweepermapgenerator.com" >> /etc/hosts\n\
echo "127.0.0.1 www.minesweepermapgenerator.es" >> /etc/hosts\n\
\n\
# Start Apache in foreground\n\
apache2ctl -D FOREGROUND' > /usr/local/bin/start-apache \
    && chmod +x /usr/local/bin/start-apache

# Expose HTTPS port (mapped to 8080 externally)
EXPOSE 443
EXPOSE 80

# Health check
HEALTHCHECK --interval=30s --timeout=10s --start-period=5s --retries=3 \
    CMD curl -f https://localhost/ -k || exit 1

# Start Apache
CMD ["/usr/local/bin/start-apache"] 