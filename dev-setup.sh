#!/bin/bash

# 🚀 Script de Setup Automático - Sistema RRHH YPFB
# Bash script para macOS/Linux

echo "🚀 Configurando entorno de desarrollo Sistema RRHH YPFB..."

# Verificar Docker
echo "🐳 Verificando Docker..."
if command -v docker &> /dev/null; then
    echo "✅ Docker encontrado: $(docker --version)"
else
    echo "❌ Docker no instalado. Instalar desde: https://www.docker.com/products/docker-desktop/"
    exit 1
fi

# Verificar Docker Compose
if command -v docker-compose &> /dev/null; then
    echo "✅ Docker Compose encontrado: $(docker-compose --version)"
else
    echo "❌ Docker Compose no disponible"
    exit 1
fi

# Verificar Node.js
echo "📦 Verificando Node.js..."
if command -v node &> /dev/null && command -v npm &> /dev/null; then
    echo "✅ Node.js: $(node --version), npm: $(npm --version)"
else
    echo "❌ Node.js no instalado. Instalar desde: https://nodejs.org/"
    exit 1
fi

# Instalar dependencias npm
echo "📦 Instalando dependencias frontend..."
if npm ci; then
    echo "✅ Dependencias instaladas"
else
    echo "❌ Error instalando dependencias"
    exit 1
fi

# Verificar si Docker está corriendo
echo "🔍 Verificando si Docker está ejecutándose..."
if docker ps &> /dev/null; then
    echo "✅ Docker corriendo"
else
    echo "❌ Docker no está corriendo. Por favor iniciar Docker Desktop."
    echo "💡 Esperar hasta que Docker esté disponible"
    read -p "Presiona Enter cuando Docker esté listo..."
fi

# Levantar servicios
echo "🐳 Levantando servicios Docker..."
if docker-compose up -d; then
    echo "✅ Servicios levantados exitosamente"
else
    echo "❌ Error levantando servicios"
    exit 1
fi

# Esperar a que los servicios estén listos
echo "⏳ Esperando servicios (30s)..."
sleep 30

# Configurar Laravel
echo "🔧 Configurando Laravel..."
docker-compose exec -T app bash -c "
    if [ ! -f .env ]; then
        cp .env.example .env
        php artisan key:generate --force
    fi
    mkdir -p bootstrap/cache storage/framework/{cache,sessions,views}
    chmod -R 777 bootstrap/cache storage
"

# Verificar servicios
echo "✅ Verificando servicios..."
docker-compose ps

# Build frontend
echo "🎨 Compilando assets..."
if npm run build; then
    echo "✅ Assets compilados"
else
    echo "❌ Error compilando assets"
fi

# Resultado final
echo ""
echo "🎉 ¡Setup completado exitosamente!"
echo ""
echo "📍 URLs disponibles:"
echo "   🌐 Aplicación: http://localhost:8081"
echo "   📧 MailHog:    http://localhost:8025"
echo ""
echo "🛠️  Comandos útiles:"
echo "   Ver logs:      docker-compose logs -f app"
echo "   Parar:         docker-compose down"
echo "   Reiniciar:     docker-compose restart"
echo "   Laravel CLI:   docker-compose exec app php artisan"
echo ""
echo "📖 Ver TEAM-ONBOARDING.md para más detalles"