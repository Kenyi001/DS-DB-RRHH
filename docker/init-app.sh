#!/bin/bash
set -e

echo "🚀 Inicializando aplicación Laravel..."

# Esperar a que SQL Server esté disponible
echo "⏳ Esperando SQL Server..."
until timeout 10 bash -c '</dev/tcp/sqlserver/1433' > /dev/null 2>&1; do
    echo "SQL Server no disponible, reintentando en 5s..."
    sleep 5
done
echo "✅ SQL Server conectado"

# Esperar a que Redis esté disponible
echo "⏳ Esperando Redis..."
until redis-cli -h redis ping > /dev/null 2>&1; do
    echo "Redis no disponible, reintentando en 2s..."
    sleep 2
done
echo "✅ Redis conectado"

# Generar key si no existe
if [ ! -f .env ]; then
    echo "📝 Copiando .env desde .env.example..."
    cp .env.example .env
fi

echo "🔑 Generando application key..."
php artisan key:generate --force

# Crear enlace simbólico para storage
echo "🔗 Creando storage link..."
php artisan storage:link || true

# Ejecutar migraciones
echo "🗄️  Ejecutando migraciones..."
php artisan migrate --force

# Ejecutar seeders
echo "🌱 Ejecutando seeders..."
php artisan db:seed --force || echo "⚠️  Seeders fallaron - continuando..."

# Optimizar para producción
if [ "$APP_ENV" = "production" ]; then
    echo "⚡ Optimizando para producción..."
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache
fi

echo "✅ Aplicación inicializada correctamente"

# Iniciar PHP-FPM
exec php-fpm