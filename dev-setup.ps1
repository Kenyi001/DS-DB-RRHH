# 🚀 Script de Setup Automático - Sistema RRHH YPFB
# PowerShell script para configurar entorno de desarrollo

Write-Host "🚀 Configurando entorno de desarrollo Sistema RRHH YPFB..." -ForegroundColor Green

# Verificar Docker
Write-Host "🐳 Verificando Docker..." -ForegroundColor Yellow
try {
    $dockerVersion = docker --version
    Write-Host "✅ Docker encontrado: $dockerVersion" -ForegroundColor Green
} catch {
    Write-Host "❌ Docker no instalado. Instalar desde: https://www.docker.com/products/docker-desktop/" -ForegroundColor Red
    exit 1
}

# Verificar Docker Compose
try {
    $composeVersion = docker-compose --version
    Write-Host "✅ Docker Compose encontrado: $composeVersion" -ForegroundColor Green
} catch {
    Write-Host "❌ Docker Compose no disponible" -ForegroundColor Red
    exit 1
}

# Verificar Node.js
Write-Host "📦 Verificando Node.js..." -ForegroundColor Yellow
try {
    $nodeVersion = node --version
    $npmVersion = npm --version
    Write-Host "✅ Node.js: $nodeVersion, npm: $npmVersion" -ForegroundColor Green
} catch {
    Write-Host "❌ Node.js no instalado. Instalar desde: https://nodejs.org/" -ForegroundColor Red
    exit 1
}

# Instalar dependencias npm
Write-Host "📦 Instalando dependencias frontend..." -ForegroundColor Yellow
npm ci
if ($LASTEXITCODE -eq 0) {
    Write-Host "✅ Dependencias instaladas" -ForegroundColor Green
} else {
    Write-Host "❌ Error instalando dependencias" -ForegroundColor Red
    exit 1
}

# Verificar si Docker Desktop está corriendo
Write-Host "🔍 Verificando si Docker está ejecutándose..." -ForegroundColor Yellow
try {
    docker ps | Out-Null
    Write-Host "✅ Docker Desktop corriendo" -ForegroundColor Green
} catch {
    Write-Host "❌ Docker Desktop no está corriendo. Por favor iniciar Docker Desktop." -ForegroundColor Red
    Write-Host "💡 Esperar hasta que muestre 'Docker Desktop is running'" -ForegroundColor Cyan
    Read-Host "Presiona Enter cuando Docker Desktop esté listo"
}

# Levantar servicios
Write-Host "🐳 Levantando servicios Docker..." -ForegroundColor Yellow
docker-compose up -d
if ($LASTEXITCODE -eq 0) {
    Write-Host "✅ Servicios levantados exitosamente" -ForegroundColor Green
} else {
    Write-Host "❌ Error levantando servicios" -ForegroundColor Red
    exit 1
}

# Esperar a que los servicios estén listos
Write-Host "⏳ Esperando servicios (30s)..." -ForegroundColor Yellow
Start-Sleep -Seconds 30

# Configurar Laravel
Write-Host "🔧 Configurando Laravel..." -ForegroundColor Yellow
docker-compose exec -T app bash -c "
    if [ ! -f .env ]; then
        cp .env.example .env
        php artisan key:generate --force
    fi
    mkdir -p bootstrap/cache storage/framework/{cache,sessions,views}
    chmod -R 777 bootstrap/cache storage
"

# Verificar servicios
Write-Host "✅ Verificando servicios..." -ForegroundColor Yellow
docker-compose ps

# Build frontend
Write-Host "🎨 Compilando assets..." -ForegroundColor Yellow
npm run build
if ($LASTEXITCODE -eq 0) {
    Write-Host "✅ Assets compilados" -ForegroundColor Green
} else {
    Write-Host "❌ Error compilando assets" -ForegroundColor Red
}

# Resultado final
Write-Host ""
Write-Host "🎉 ¡Setup completado exitosamente!" -ForegroundColor Green
Write-Host ""
Write-Host "📍 URLs disponibles:" -ForegroundColor Cyan
Write-Host "   🌐 Aplicación: http://localhost:8081" -ForegroundColor White
Write-Host "   📧 MailHog:    http://localhost:8025" -ForegroundColor White
Write-Host ""
Write-Host "🛠️  Comandos útiles:" -ForegroundColor Cyan
Write-Host "   Ver logs:      docker-compose logs -f app" -ForegroundColor White
Write-Host "   Parar:         docker-compose down" -ForegroundColor White
Write-Host "   Reiniciar:     docker-compose restart" -ForegroundColor White
Write-Host "   Laravel CLI:   docker-compose exec app php artisan" -ForegroundColor White
Write-Host ""
Write-Host "📖 Ver TEAM-ONBOARDING.md para más detalles" -ForegroundColor Cyan