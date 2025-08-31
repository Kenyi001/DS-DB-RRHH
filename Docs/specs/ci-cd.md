# Especificación CI/CD - Sistema RRHH YPFB-Andina

## Propósito
Definir los pipelines de integración continua y despliegue continuo para garantizar calidad de código, automatización de pruebas y despliegues seguros del Sistema RRHH.

## Alcance
- Pipeline CI para Pull Requests (lint, tests, security)
- Pipeline CD para despliegues a QA y Producción
- Estrategias de rollback y blue-green deployment
- Gestión de artefactos y versionado
- Integración con contenedores Docker

## Pipeline CI (Pull Requests)

### Etapas de Validación
```yaml
# .github/workflows/ci.yml (ejemplo)
name: CI Pipeline

on: [pull_request]

jobs:
  validate:
    steps:
      - checkout
      - setup-php: 8.3 with sqlsrv extensions
      - composer install --no-interaction --optimize-autoloader
      - php artisan pint --test (style check)
      - phpstan analyse (static analysis)
      - phpunit --testsuite=Unit
```

### Validaciones Backend
- **Style Check**: PHP Pint para convenciones PSR-12
- **Static Analysis**: PHPStan nivel 6+ para detección de errores
- **Unit Tests**: PHPUnit con cobertura mínima 70%
- **Security Scan**: PHP Security Checker para vulnerabilidades

### Validaciones Frontend
- **Linting**: ESLint con configuración estricta
- **Formatting**: Prettier para consistencia de código
- **Build**: Vite build para verificar assets compilan
- **Type Check**: TypeScript si aplica

### Tests de Integración (Base de Datos)
```yaml
  integration-tests:
    services:
      sqlserver:
        image: mcr.microsoft.com/mssql/server:2019-latest
        env:
          SA_PASSWORD: TestPassword123!
          ACCEPT_EULA: Y
    steps:
      - run integration tests with SQL Server container
      - test stored procedures: sp_CalcularSalarioMensual
      - validate triggers: trg_Validar_Anticipo
```

## Pipeline CD (Continuous Deployment)

### Estrategia de Despliegue
```yaml
# .github/workflows/cd.yml
name: CD Pipeline

on:
  push:
    branches: [main]

jobs:
  build-and-deploy:
    steps:
      - build Docker image multi-stage
      - tag with git SHA and version
      - push to container registry
      - deploy to staging with health checks
      - run smoke tests
      - promote to production if staging OK
```

### Despliegue a Staging
1. **Build**: Crear imagen Docker con tag `git-sha`
2. **Deploy**: Rolling update en staging
3. **Migrations**: `php artisan migrate --force` en modo mantenimiento
4. **Smoke Tests**: Verificar endpoints críticos
5. **Notification**: Notificar equipo del resultado

### Despliegue a Producción
1. **Approval Gate**: Requiere aprobación manual
2. **Backup**: Backup diferencial antes de migración
3. **Blue-Green**: Mantener versión anterior disponible
4. **Health Checks**: Validar servicios por 10 minutos
5. **Rollback**: Automático si health checks fallan

## Gestión de Artefactos

### Versionado
- **Semantic Versioning**: `v1.2.3` para releases
- **Docker Tags**: `latest`, `v1.2.3`, `git-sha`
- **Database Migrations**: Numeradas y versionadas
- **Assets**: Versionado con hash para cache busting

### Registry y Storage
- **Container Registry**: Harbor/ECR para imágenes Docker
- **Artifact Storage**: S3 para backups y artefactos de build
- **Retention**: Mantener últimas 10 imágenes, 30 días de artefactos

## Scripts de Migración

### Ejemplo de Script Idempotente
```sql
-- Migration: 2025_08_31_create_log_planilla.sql
IF NOT EXISTS (SELECT * FROM sys.tables WHERE name = 'LogPlanilla')
BEGIN
    CREATE TABLE LogPlanilla (
        PlanillaId INT IDENTITY(1,1) PRIMARY KEY,
        IdempotencyKey UNIQUEIDENTIFIER NULL UNIQUE,
        Mes INT NOT NULL,
        Gestion INT NOT NULL,
        Usuario NVARCHAR(100) NOT NULL,
        FechaInicio DATETIME2 NOT NULL DEFAULT SYSUTCDATETIME(),
        FechaFin DATETIME2 NULL,
        EstadoProceso NVARCHAR(20) NOT NULL,
        ContratosProcessados INT NULL,
        Observaciones NVARCHAR(500) NULL
    );
END
```

### Estrategia de Migrations
- **Downtime**: Modo mantenimiento solo para cambios destructivos
- **Rollback**: Migrations reversibles cuando sea posible
- **Validation**: Tests automáticos post-migración
- **Monitoring**: Alertas durante ventana de migración

## Entornos

### Development (Local)
- **Docker Compose**: SQL Server + Redis + MailHog
- **Hot Reload**: Vite dev server para frontend
- **Test Data**: Seeds con 335 empleados de prueba

### QA/Staging
- **Infrastructure as Code**: Docker Compose o K8s manifests
- **Data Refresh**: Copia enmascarada de producción semanalmente
- **Automated Testing**: E2E con Playwright post-deploy

### Production
- **High Availability**: Load balancer + múltiples replicas app
- **Database**: SQL Server con backup automatizado
- **Monitoring**: Prometheus + Grafana + alertas
- **Secrets**: Vault/AWS Secrets Manager

## Rollback y Recovery

### Estrategia de Rollback
1. **Health Check Failure**: Rollback automático en 5 minutos
2. **Database Issues**: Restore desde backup diferencial
3. **Application Issues**: Revert a imagen anterior
4. **Validation**: Smoke tests post-rollback

### Recovery Time Objectives
- **RTO**: ≤ 1 hora para restauración completa
- **RPO**: ≤ 15 minutos (frecuencia de backup logs)
- **MTTR**: ≤ 30 minutos para issues aplicación

## Dependencias
- GitHub Actions o GitLab CI para pipelines
- Docker Registry para almacenar imágenes
- SQL Server Agent para jobs automatizados
- Redis cluster para cache distribuido
- S3-compatible storage para artefactos

## Criterios de Aceptación
- [ ] Pipeline CI ejecuta en < 15 minutos para PRs
- [ ] Tests de integración con SQL Server container funcionando
- [ ] Despliegue a staging automatizado post-merge
- [ ] Health checks y smoke tests implementados
- [ ] Rollback automático funcional en < 5 minutos
- [ ] Migrations idempotentes y reversibles
- [ ] Secrets gestionados de forma segura (no en repo)
- [ ] Alertas configuradas para fallos de pipeline
- [ ] Logs de despliegue centralizados y auditables
- [ ] Blue-green deployment implementado para producción

## Referencias al Documento Canónico
Este documento se basa en las secciones 12, 34 y 56 del [Project Chapter](../projectChapter.md). Para configuraciones específicas de Docker, scripts de deployment y runbooks operativos, consultar el documento principal.

**Supuestos:**
- GitHub/GitLab disponible como plataforma de CI/CD
- Permisos para crear containers y registries
- Acceso a SQL Server para ejecutar migrations en QA/Prod