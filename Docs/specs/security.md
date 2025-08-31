# Especificación de Seguridad - Sistema RRHH YPFB-Andina

## Propósito
Definir los controles de seguridad, autenticación, autorización y protección de datos del Sistema RRHH para cumplir con estándares corporativos y regulaciones aplicables.

## Alcance
- Autenticación y gestión de sesiones
- Autorización basada en roles (RBAC)
- Protección de datos sensibles y PII
- Auditoría y trazabilidad
- Controles de acceso a base de datos
- Seguridad en comunicaciones

## Autenticación y Gestión de Sesiones

### Mecanismo de Autenticación
- **Framework**: Laravel Sanctum para SPA tokens
- **Token Lifetime**: 8 horas con refresh automático
- **Multi-Factor**: Opcional para roles administrativos
- **Password Policy**: Mínimo 8 caracteres, complejidad media

### Gestión de Sesiones
```php
// Configuración de sesión segura
'session' => [
    'lifetime' => 480, // 8 horas
    'expire_on_close' => true,
    'encrypt' => true,
    'http_only' => true,
    'same_site' => 'strict',
    'secure' => env('SESSION_SECURE_COOKIE', true)
]
```

### Password Hashing
```php
// Usar bcrypt con cost factor alto
'passwords' => [
    'users' => [
        'provider' => 'users',
        'table' => 'password_resets',
        'expire' => 60,
        'throttle' => 60,
    ],
    'cost' => 12, // bcrypt cost factor
]
```

## Autorización RBAC

### Roles del Sistema
- **admin_rrhh**: Acceso completo al sistema
- **analista_rrhh**: CRUD empleados, contratos, subsidios
- **jefe_area**: Aprobación de vacaciones y anticipos
- **contabilidad**: Visualización planillas, marcar pagos
- **auditor**: Solo lectura de AuditLog y reportes

### Permisos Granulares
```php
// Ejemplos de permisos en sistema
$permissions = [
    'empleados.view', 'empleados.create', 'empleados.edit', 'empleados.delete',
    'contratos.view', 'contratos.create', 'contratos.edit',
    'planilla.view', 'planilla.generar', 'planilla.pagar',
    'subsidios.manage', 'anticipos.request', 'anticipos.approve',
    'vacaciones.request', 'vacaciones.approve',
    'reportes.view', 'reportes.export',
    'auditlog.view', 'usuarios.manage'
];
```

### Policy Implementation
```php
// Ejemplo: PlanillaPolicy
class PlanillaPolicy
{
    public function generar(User $user): bool
    {
        return $user->can('planilla.generar') && 
               $user->hasRole(['admin_rrhh', 'analista_rrhh']);
    }
    
    public function pagar(User $user): bool
    {
        return $user->can('planilla.pagar') && 
               $user->hasRole(['admin_rrhh', 'contabilidad']);
    }
}
```

## Protección de Datos

### Clasificación de Datos
- **Públicos**: Departamentos, cargos, tipos de subsidio
- **Internos**: Nombres, emails corporativos, números de contrato
- **Confidenciales**: Salarios, CI, teléfonos personales, evaluaciones
- **Restringidos**: Datos médicos, información disciplinaria

### Cifrado de Datos

#### En Tránsito
- **TLS 1.2+**: Obligatorio para todas las comunicaciones
- **Certificate Pinning**: Para conexiones críticas
- **HSTS**: Headers de seguridad habilitados

#### En Reposo
```sql
-- Transparent Data Encryption (TDE) para producción
CREATE DATABASE ENCRYPTION KEY
WITH ALGORITHM = AES_256
ENCRYPTION BY SERVER CERTIFICATE TDE_Cert;

ALTER DATABASE [RRHH_DB] SET ENCRYPTION ON;
```

### Enmascaramiento de Datos
```sql
-- Dynamic Data Masking para staging/development
ALTER TABLE Empleados
ALTER COLUMN CI ADD MASKED WITH (FUNCTION = 'partial(1,"XXX-XXXX-",1)');

ALTER TABLE Empleados  
ALTER COLUMN Email ADD MASKED WITH (FUNCTION = 'email()');

-- Grant unmask para roles autorizados
GRANT UNMASK TO [RRHH_Admin];
```

## Controles de Acceso a Base de Datos

### Usuarios y Permisos DB
```sql
-- Usuario aplicación con permisos mínimos
CREATE LOGIN [rrhh_app] WITH PASSWORD = 'ComplexPassword123!';
CREATE USER [rrhh_app] FOR LOGIN [rrhh_app];

-- Permisos granulares
GRANT SELECT, INSERT, UPDATE ON Empleados TO [rrhh_app];
GRANT SELECT, INSERT, UPDATE ON Contratos TO [rrhh_app];
GRANT SELECT ON Departamentos TO [rrhh_app];
GRANT EXECUTE ON sp_GenerarPlanillaMensual TO [rrhh_app];

-- Usuario de solo lectura para reportes
CREATE LOGIN [rrhh_reader] WITH PASSWORD = 'ReadOnlyPassword123!';
CREATE USER [rrhh_reader] FOR LOGIN [rrhh_reader];
GRANT SELECT ON SCHEMA::dbo TO [rrhh_reader];
```

### Auditoría de Accesos
```sql
-- Trigger para auditar accesos sensibles
CREATE TRIGGER trg_Audit_Sensitive_Access
ON GestionSalarios
AFTER SELECT
AS
BEGIN
    INSERT INTO AccessLog (UserId, TableName, Action, Timestamp, IP)
    VALUES (SUSER_SNAME(), 'GestionSalarios', 'SELECT', GETDATE(), 
            CONNECTIONPROPERTY('client_net_address'));
END
```

## Validación de Entrada y Sanitización

### Input Validation (Laravel)
```php
// FormRequest para validaciones robustas
class CreateEmpleadoRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'ci' => ['required', 'string', 'regex:/^\d{7,8}$/', 'unique:empleados'],
            'nombres' => ['required', 'string', 'max:100', 'regex:/^[a-zA-ZÀ-ÿ\s]+$/'],
            'email' => ['required', 'email:rfc,dns', 'max:150'],
            'telefono' => ['nullable', 'regex:/^\+?591[0-9]{8}$/'],
            'fecha_nacimiento' => ['required', 'date', 'before:18 years ago']
        ];
    }
    
    public function sanitize(): array
    {
        return [
            'nombres' => trim(strip_tags($this->nombres)),
            'email' => strtolower(trim($this->email)),
            'ci' => preg_replace('/[^0-9]/', '', $this->ci)
        ];
    }
}
```

### SQL Injection Prevention
- **Parametrized Queries**: Obligatorio para todas las consultas
- **Stored Procedures**: Para operaciones críticas
- **Input Escaping**: Sanitización en capa de entrada

## Gestión de Secretos

### Development Environment
```bash
# .env.example (sin secretos reales)
DB_CONNECTION=sqlsrv
DB_HOST=localhost
DB_PORT=1433
DB_DATABASE=rrhh_dev
DB_USERNAME=rrhh_user
DB_PASSWORD=local_password_123

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=redis_local_pass
```

### Production Environment
- **AWS Secrets Manager** o **Azure Key Vault** para credenciales
- **Rotation automática** cada 90 días
- **Access logs** para auditar uso de secretos

## Rate Limiting y Throttling

### API Protection
```php
// Middleware de throttling
Route::middleware(['auth', 'throttle:planilla'])->group(function () {
    Route::post('/api/v1/planilla/generar', [PlanillaController::class, 'generar']);
});

// Config/throttle.php
'planilla' => [
    'max_attempts' => 3,
    'decay_minutes' => 60,
],
'api' => [
    'max_attempts' => 100,
    'decay_minutes' => 1,
]
```

### WAF Rules (Nginx/CloudFlare)
- **Rate Limiting**: 100 req/min por IP para APIs
- **Geographic Blocking**: Solo Bolivia y países vecinos
- **Signature Detection**: SQL injection, XSS patterns

## Compliance y Auditoría

### Registros de Auditoría
```sql
-- Estructura completa AuditLog
CREATE TABLE AuditLog (
    AuditId BIGINT IDENTITY(1,1) PRIMARY KEY,
    Entity NVARCHAR(50) NOT NULL,
    EntityId INT NOT NULL,
    Action NVARCHAR(20) NOT NULL,
    UserId INT NULL,
    TraceId UNIQUEIDENTIFIER NULL,
    CreatedAt DATETIME2 NOT NULL DEFAULT SYSUTCDATETIME(),
    PayloadBefore NVARCHAR(MAX) NULL,
    PayloadAfter NVARCHAR(MAX) NULL,
    IP NVARCHAR(45) NULL,
    UserAgent NVARCHAR(500) NULL,
    Comments NVARCHAR(500) NULL
);

-- Índice para búsquedas frecuentes
CREATE INDEX IX_AuditLog_EntityDate 
ON AuditLog (Entity, CreatedAt DESC) 
INCLUDE (EntityId, Action, UserId);
```

### Retención de Logs
- **AuditLog**: 5 años (regulación)
- **AccessLog**: 1 año
- **Application Logs**: 90 días
- **Security Events**: 2 años

## Incident Response

### Security Incident Classification
- **P0**: Breach de datos, acceso no autorizado a producción
- **P1**: Vulnerabilidad crítica explotada
- **P2**: Intento de acceso no autorizado detectado
- **P3**: Vulnerability scan findings

### Response Procedures
1. **Immediate**: Aislar sistema afectado
2. **Assessment**: Determinar scope y impact
3. **Containment**: Parchear vulnerabilidad
4. **Communication**: Notificar según regulaciones
5. **Recovery**: Restaurar operación normal
6. **Lessons Learned**: Post-mortem y mejoras

## Dependencias
- Laravel Sanctum para autenticación API
- Spatie Laravel Permission para RBAC
- TLS certificates (Let's Encrypt/corporate CA)
- WAF solution (CloudFlare/AWS WAF)
- Secrets management service
- SIEM tool para correlación de eventos

## Criterios de Aceptación
- [ ] Autenticación robusta implementada con MFA opcional
- [ ] RBAC con permissions granulares funcionando
- [ ] Datos sensibles cifrados en tránsito y reposo
- [ ] Input validation implementada en todas las capas
- [ ] Rate limiting configurado en endpoints críticos
- [ ] Auditoría completa de acciones sensibles
- [ ] Secrets gestionados de forma segura (no en repo)
- [ ] Penetration testing passed con 0 vulnerabilidades críticas
- [ ] Security headers implementados (HSTS, CSP, etc.)
- [ ] Incident response procedures documentados y probados

## Referencias al Documento Canónico
Este documento se basa en las secciones 11, 18, 43, 53 y 54 del [Project Chapter](../projectChapter.md). Para implementaciones específicas de policies, ejemplos de código y configuraciones detalladas, consultar el documento principal.

**Supuestos:**
- Compliance con regulaciones bolivianas de protección de datos
- Infraestructura corporativa con controles base implementados
- Team de seguridad disponible para revisiones y auditorías