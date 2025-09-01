# Multi-stage Dockerfile para Laravel + SQL Server
FROM node:20-alpine AS frontend-builder

WORKDIR /app
COPY package*.json ./
RUN npm install
COPY resources/ resources/
COPY tailwind.config.js postcss.config.js vite.config.js ./
RUN npm run build

FROM php:8.3-fpm AS php-base

# Instalar dependencias del sistema
RUN apt-get update && apt-get install -y \
    curl \
    gnupg2 \
    unixodbc-dev \
    git \
    zip \
    unzip \
    && rm -rf /var/lib/apt/lists/*

# Instalar Microsoft SQL Server drivers
RUN curl https://packages.microsoft.com/keys/microsoft.asc | gpg --dearmor --yes --output /usr/share/keyrings/microsoft-prod.gpg \
    && echo "deb [arch=amd64,arm64,armhf signed-by=/usr/share/keyrings/microsoft-prod.gpg] https://packages.microsoft.com/debian/12/prod bookworm main" > /etc/apt/sources.list.d/mssql-release.list \
    && apt-get update \
    && ACCEPT_EULA=Y apt-get install -y msodbcsql18 mssql-tools18 \
    && echo 'export PATH="$PATH:/opt/mssql-tools18/bin"' >> ~/.bashrc

# Instalar extensiones PHP incluyendo SQL Server
RUN pecl install sqlsrv pdo_sqlsrv redis \
    && docker-php-ext-enable sqlsrv pdo_sqlsrv redis \
    && docker-php-ext-install pdo

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Copiar composer files y instalar dependencias
COPY composer.json artisan ./
COPY bootstrap/ bootstrap/
COPY routes/ routes/
RUN mkdir -p bootstrap/cache && chmod 775 bootstrap/cache
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Copiar código fuente
COPY . .
COPY --from=frontend-builder /app/public/build public/build

# Crear directorios y permisos
RUN mkdir -p storage/logs storage/framework/{cache,sessions,views} bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

# Script de inicialización
COPY docker/init-app.sh /usr/local/bin/init-app.sh
RUN chmod +x /usr/local/bin/init-app.sh

EXPOSE 9000

CMD ["bash", "/usr/local/bin/init-app.sh"]