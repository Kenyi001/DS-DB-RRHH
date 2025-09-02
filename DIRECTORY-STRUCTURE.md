# Estructura de Directorios - Sistema RRHH YPFB-Andina

## Arquitectura del Proyecto

Este proyecto sigue una **arquitectura modular por dominio** combinando las mejores prácticas de Laravel con organización específica para módulos RRHH.

### Principios de Organización

- **Modular por dominio**: Cada módulo RRHH tiene su estructura completa
- **Separación de capas**: Controllers → Services → Repositories → Models
- **Convención Laravel**: Mantiene compatibilidad con estándares del framework
- **Escalabilidad**: Permite crecimiento independiente de módulos

## Estructura Principal

```
DS-DB-RRHH/
├── app/
│   ├── Http/Controllers/           # Controladores base Laravel
│   ├── Models/                     # Modelos base Laravel
│   ├── Services/                   # Servicios base Laravel
│   ├── Repositories/               # Repositorios base Laravel
│   └── Modules/                    # ⭐ MÓDULOS RRHH
│       ├── Empleados/
│       ├── Contratos/
│       ├── Planilla/
│       ├── Subsidios/
│       ├── Anticipos/
│       ├── Vacaciones/
│       ├── Evaluaciones/
│       ├── Afiliaciones/
│       └── Reportes/
│
├── resources/
│   ├── views/                      # Vistas organizadas por módulo
│   │   ├── empleados/
│   │   ├── contratos/
│   │   ├── planilla/
│   │   └── ...
│   ├── css/
│   │   ├── app.css                 # CSS principal
│   │   ├── components/             # Componentes CSS reutilizables
│   │   └── modules/                # CSS específico por módulo
│   └── js/
│       ├── app.js                  # JS principal
│       └── modules/                # JS específico por módulo
│
├── database/
│   ├── migrations/
│   │   └── modules/                # Migraciones organizadas por módulo
│   ├── seeders/
│   │   └── modules/                # Seeders por módulo
│   └── factories/
│       └── modules/                # Factories por módulo
│
├── tests/
│   ├── Feature/Modules/            # Tests funcionales por módulo
│   └── Unit/Modules/               # Tests unitarios por módulo
│
├── storage/
│   ├── app/modules/                # Archivos organizados por módulo
│   └── logs/modules/               # Logs específicos por módulo
│
├── Docs/                           # Documentación del proyecto
│   ├── projectChapter.md           # Documento canónico del proyecto
│   ├── sql/
│   │   ├── README.md               # ⚠️ Scripts SQL DRAFT
│   │   └── draft/                  # Scripts SQL NO ejecutar en prod
│   ├── specs/                      # Especificaciones técnicas
│   └── design/                     # Tokens de diseño y UI
│
└── docker/                         # Configuración Docker
```

## Estructura por Módulo

Cada módulo en `app/Modules/{Nombre}/` tiene:

```
Empleados/                          # Ejemplo módulo Empleados
├── Controllers/
│   ├── EmpleadoController.php      # CRUD principal
│   └── Api/
│       └── EmpleadoApiController.php
├── Services/
│   └── EmpleadoService.php         # Lógica de negocio
├── Repositories/
│   └── EmpleadoRepository.php      # Acceso a datos
├── Models/
│   └── Empleado.php                # Modelo Eloquent
├── Requests/
│   ├── StoreEmpleadoRequest.php    # Validaciones
│   └── UpdateEmpleadoRequest.php
├── Policies/
│   └── EmpleadoPolicy.php          # Autorización
└── Resources/
    ├── EmpleadoResource.php        # API Resources
    └── EmpleadoCollection.php
```

## Convenciones de Naming

### Archivos y Clases
- **Controladores**: `{Modulo}Controller.php` (ej. `EmpleadoController`)
- **Servicios**: `{Modulo}Service.php` (ej. `EmpleadoService`)
- **Repositorios**: `{Modulo}Repository.php` (ej. `EmpleadoRepository`)
- **Modelos**: `{Entidad}.php` (ej. `Empleado`, `Contrato`)
- **Requests**: `{Action}{Modulo}Request.php` (ej. `StoreEmpleadoRequest`)

### Namespaces
```php
// Módulos siguen el patrón App\\Modules\\{Modulo}\\{Capa}
App\\Modules\\Empleados\\Controllers\\EmpleadoController
App\\Modules\\Empleados\\Services\\EmpleadoService
App\\Modules\\Contratos\\Repositories\\ContratoRepository
```

### Rutas
- **Web**: `/empleados`, `/contratos`, `/planilla`
- **API**: `/api/v1/empleados`, `/api/v1/contratos`

## Frontend (Resources)

### CSS
- `resources/css/app.css`: CSS principal con imports
- `resources/css/components/`: Componentes reutilizables (botones, forms)
- `resources/css/modules/`: CSS específico por módulo

### JavaScript
- `resources/js/app.js`: JavaScript principal
- `resources/js/modules/`: JS específico por módulo

### Vistas Blade
- `resources/views/{modulo}/`: Vistas organizadas por módulo
- Nombres descriptivos: `index.blade.php`, `create.blade.php`, `show.blade.php`

## Base de Datos

### Migraciones
```
database/migrations/modules/empleados/
├── 2025_01_01_000001_create_empleados_table.php
├── 2025_01_01_000002_create_contratos_table.php
└── ...
```

### Seeders
```
database/seeders/modules/empleados/
├── EmpleadoSeeder.php
├── ContratoSeeder.php
└── ...
```

## Testing

### Organización
```
tests/
├── Feature/Modules/Empleados/
│   ├── EmpleadoControllerTest.php
│   └── EmpleadoApiTest.php
└── Unit/Modules/Empleados/
    ├── EmpleadoServiceTest.php
    └── EmpleadoRepositoryTest.php
```

### Convenciones
- **Feature Tests**: Prueban endpoints y flujos completos
- **Unit Tests**: Prueban servicios y repositorios en aislamiento

## Scripts y Build

### NPM Scripts Disponibles
```bash
npm run dev          # Desarrollo con watch
npm run build        # Build producción
npm run lint         # ESLint con fix
npm run lint:check   # ESLint solo verificar
npm run css:build    # Build solo CSS
npm run css:watch    # Watch solo CSS
```

### Comandos Laravel
```bash
php artisan make:module {Nombre}        # Crear nuevo módulo (custom)
php artisan module:controller Empleados # Crear controlador en módulo
php artisan module:service Empleados    # Crear servicio en módulo
```

## Tokens de Diseño (TailwindCSS)

### Colores YPFB
- `ypfb-blue`: #0A3E8F (primario)
- `ypfb-red`: #E31B23 (accent/danger)
- `ypfb-white`: #FFFFFF

### Componentes CSS
- `.btn`, `.btn-primary`, `.btn-danger`: Botones estándar
- `.form-input`, `.form-select`: Formularios
- Accesibilidad AA por defecto

## Documentación

### Documentos Clave
- `Docs/projectChapter.md`: **Documento canónico** (no duplicar)
- `Docs/sql/README.md`: Scripts SQL **DRAFT** (revisar con DBA)
- `CLAUDE.md`: Instrucciones para Claude
- Este archivo: Estructura y convenciones

### SQL Scripts (⚠️ IMPORTANTE)
- Todos los `.sql` en `Docs/sql/draft/` son **PLANTILLAS**
- **NO ejecutar en producción sin revisión DBA**
- Convertir a migrations Laravel probadas

## Seguridad y Buenas Prácticas

### Archivos Sensibles
- `.env` nunca en repositorio
- Secrets en variables de entorno o Vault
- Logs con información sensible enmascarados

### Permisos
- Seguir RBAC definido en project specs
- Policies por módulo para autorización granular
- Gates para acciones críticas (planilla, pagos)

## Próximos Pasos

1. **Implementar módulos MVP**: Empleados → Contratos → Planilla
2. **Revisar SQL con DBA**: Convertir plantillas a migrations
3. **Setup CI/CD**: Tests, lint, build automático
4. **Documentar APIs**: OpenAPI/Swagger por módulo

---

**Owner**: Equipo Desarrollo RRHH  
**Última actualización**: 2025-09-01  
**Versión**: 1.0