# Guía de Desarrollo - Sistema RRHH YPFB-Andina

## Setup Inicial (< 15 minutos)

### Prerrequisitos
- Docker Engine >= 20.10
- Docker Compose v2
- Git configurado

### Pasos de Instalación

1. **Clonar repositorio**
   ```bash
   git clone <repo-url>
   cd DS-DB-RRHH
   ```

2. **Configurar variables de entorno**
   ```bash
   cp .env.example .env
   # Ajustar credenciales si es necesario
   ```

3. **Levantar servicios con Docker**
   ```bash
   docker-compose up -d --build
   ```

4. **Instalar dependencias y configurar aplicación**
   ```bash
   # Backend
   docker-compose exec app composer install
   docker-compose exec app php artisan key:generate
   
   # Frontend
   docker-compose exec app npm ci
   docker-compose exec app npm run build
   ```

5. **Ejecutar migraciones y seeders**
   ```bash
   docker-compose exec app php artisan migrate --seed
   ```

6. **Verificar instalación**
   ```bash
   curl http://localhost:8000/api/health
   # Debe retornar: {"status":"ok","timestamp":"..."}
   ```

## Desarrollo Local

### Comandos Frecuentes
```bash
# Levantar servidor de desarrollo (hot reload)
docker-compose exec app npm run dev

# Ejecutar tests
docker-compose exec app ./vendor/bin/phpunit

# Ejecutar linting
docker-compose exec app ./vendor/bin/pint
docker-compose exec app npm run lint

# Ver logs de aplicación
docker-compose logs -f app

# Acceder a contenedor para debugging
docker-compose exec app bash
```

### Servicios Disponibles
- **Aplicación**: http://localhost:8000
- **API**: http://localhost:8000/api/v1/
- **MailHog**: http://localhost:8025 (emails de desarrollo)
- **SQL Server**: localhost:1433 (usuario: sa, password: DevPassword123!)
- **Redis**: localhost:6379

### Estructura del Proyecto
```
├── app/
│   ├── Http/Controllers/Api/     # Controladores API REST
│   ├── Models/                   # Modelos Eloquent
│   ├── Repositories/             # Pattern Repository
│   └── Services/                 # Lógica de negocio
├── database/
│   ├── migrations/               # Migraciones de BD
│   └── seeders/                  # Datos de prueba
├── resources/
│   ├── css/                      # Estilos Tailwind
│   └── views/                    # Plantillas Blade
├── Docs/
│   ├── specs/                    # Especificaciones técnicas
│   └── sql/draft/                # Plantillas SQL (DRAFT)
└── tickets/                      # Tickets de desarrollo
```

## Testing

### Ejecutar Tests
```bash
# Tests unitarios
docker-compose exec app ./vendor/bin/phpunit --testsuite=Unit

# Tests con base de datos (cuando estén implementados)
docker-compose exec app ./vendor/bin/phpunit --testsuite=Feature

# Tests específicos
docker-compose exec app ./vendor/bin/phpunit tests/Unit/Services/PlanillaServiceTest.php
```

### Debugging
- Usar `dd()` o `dump()` en código PHP
- Logs disponibles en `docker-compose logs app`
- SQL queries: habilitar `DB_LOG_QUERIES=true` en `.env`

## Troubleshooting

### Problemas Comunes
1. **SQL Server no conecta**: Verificar que contenedor esté healthy
2. **Permisos de archivos**: `docker-compose exec app chown -R www-data:www-data storage bootstrap/cache`
3. **Assets no compilan**: Verificar Node.js version y `npm ci`
4. **Tests fallan**: Verificar `.env.testing` y que servicios estén up

### Reset Completo
```bash
docker-compose down -v
docker-compose up -d --build
docker-compose exec app php artisan migrate:fresh --seed
```

## API Endpoints Disponibles

### Empleados
- `GET /api/v1/empleados` - Lista paginada
- `POST /api/v1/empleados` - Crear empleado
- `GET /api/v1/empleados/{id}` - Detalle empleado

### Contratos  
- `GET /api/v1/contratos` - Lista contratos
- `POST /api/v1/contratos` - Crear contrato
- `GET /api/v1/contratos/validar-solape` - Validar fechas

### Planilla
- `POST /api/v1/planilla/generar` - Generar planilla
- `GET /api/v1/planilla/status/{id}` - Estado generación

**Autenticación**: Todos los endpoints requieren `Authorization: Bearer <token>` (Sanctum - pendiente implementar)

## Notas del Stack
- **Backend**: Laravel 11 + PHP 8.3 + SQL Server
- **Frontend**: Blade + TailwindCSS + Alpine.js
- **Cache/Queue**: Redis
- **Testing**: PHPUnit + SQL Server containers
- **CI/CD**: GitHub Actions (draft implementado)

## Próximos Pasos
1. Implementar autenticación Sanctum
2. Añadir tests de integración con SQL Server
3. Implementar stored procedures reales (revisar Docs/sql/draft/)
4. Completar UI responsive para móvil