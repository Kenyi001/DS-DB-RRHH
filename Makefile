# 🚀 Makefile - Sistema RRHH YPFB
# Comandos simplificados para desarrollo

.DEFAULT_GOAL := help
.PHONY: help setup up down restart logs clean install build dev lint test deploy

# 📋 Mostrar ayuda
help: ## Mostrar comandos disponibles
	@echo "🚀 Sistema RRHH YPFB - Comandos disponibles:"
	@echo ""
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "  \033[36m%-15s\033[0m %s\n", $$1, $$2}'
	@echo ""

# 🔧 Setup inicial completo
setup: ## Configurar entorno completo (primera vez)
	@echo "🚀 Configurando entorno de desarrollo..."
	@docker --version || (echo "❌ Docker no instalado" && exit 1)
	@npm ci
	@docker-compose up -d
	@sleep 30
	@docker-compose exec -T app bash -c "cp .env.example .env && php artisan key:generate --force"
	@npm run build
	@echo "✅ Setup completado - http://localhost:8081"

# ▶️ Levantar servicios
up: ## Levantar todos los servicios
	@echo "▶️ Levantando servicios..."
	@docker-compose up -d

# ⏹️ Parar servicios
down: ## Parar todos los servicios
	@echo "⏹️ Parando servicios..."
	@docker-compose down

# 🔄 Reiniciar servicios
restart: ## Reiniciar todos los servicios
	@echo "🔄 Reiniciando servicios..."
	@docker-compose restart

# 📊 Estado de servicios
status: ## Ver estado de servicios
	@docker-compose ps

# 📋 Ver logs
logs: ## Ver logs de la aplicación
	@docker-compose logs -f app

# 🧹 Limpiar sistema
clean: ## Limpiar containers, imágenes y volúmenes
	@echo "🧹 Limpiando sistema..."
	@docker-compose down -v
	@docker system prune -f
	@echo "✅ Sistema limpio"

# 📦 Instalar dependencias
install: ## Instalar dependencias (npm + composer)
	@echo "📦 Instalando dependencias..."
	@npm ci
	@docker-compose exec -T app composer install

# 🏗️ Build frontend
build: ## Compilar assets frontend
	@echo "🏗️ Compilando assets..."
	@npm run build

# 🔥 Desarrollo con watch
dev: ## Compilar assets en modo desarrollo con watch
	@echo "🔥 Iniciando modo desarrollo..."
	@npm run dev

# 🧹 Linting
lint: ## Ejecutar linters (PHP + JS)
	@echo "🧹 Ejecutando linters..."
	@npm run lint:check
	@docker-compose exec -T app ./vendor/bin/pint --test

# ✅ Tests
test: ## Ejecutar tests
	@echo "✅ Ejecutando tests..."
	@docker-compose exec -T app php artisan test

# 🗄️ Base de datos
db-migrate: ## Ejecutar migraciones
	@echo "🗄️ Ejecutando migraciones..."
	@docker-compose exec -T app php artisan migrate

db-fresh: ## Recrear base de datos
	@echo "🗄️ Recreando base de datos..."
	@docker-compose exec -T app php artisan migrate:fresh --seed

db-seed: ## Ejecutar seeders
	@echo "🌱 Ejecutando seeders..."
	@docker-compose exec -T app php artisan db:seed

# 🔧 Laravel Artisan
artisan: ## Ejecutar comando artisan (usar: make artisan CMD="make:controller Test")
	@docker-compose exec app php artisan $(CMD)

# 🐚 Shell del contenedor
shell: ## Acceder al bash del contenedor Laravel
	@docker-compose exec app bash

# 🚀 Deploy
deploy: ## Preparar para deploy (build + optimizar)
	@echo "🚀 Preparando deploy..."
	@npm run build
	@docker-compose exec -T app php artisan config:cache
	@docker-compose exec -T app php artisan route:cache
	@docker-compose exec -T app php artisan view:cache
	@echo "✅ Listo para deploy"

# 📊 Información del sistema
info: ## Mostrar información del sistema
	@echo "📊 Información del sistema:"
	@echo "Docker: $$(docker --version)"
	@echo "Docker Compose: $$(docker-compose --version)"
	@echo "Node.js: $$(node --version)"
	@echo "NPM: $$(npm --version)"
	@echo ""
	@echo "🌐 URLs:"
	@echo "  Aplicación: http://localhost:8081"
	@echo "  MailHog:    http://localhost:8025"