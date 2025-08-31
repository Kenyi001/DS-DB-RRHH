# Ticket: Configurar Pipeline CI/CD

- **ID del Ticket:** `fase0-0008`
- **Fase:** `Sprint 0: Fundación`
- **Estado:** `Abierto`
- **Prioridad:** `Alta`

---

## Descripción

Implementar pipeline completo de CI/CD con GitHub Actions incluyendo tests automatizados, linting, security scanning y deployment automatizado a staging. Base fundamental para quality gates y deployments seguros.

---

## Criterios de Aceptación

- [ ] Pipeline CI ejecuta en PRs con lint, tests y security checks
- [ ] Tests unitarios e integración ejecutan con SQL Server container
- [ ] Build de assets frontend (Vite) incluido en pipeline
- [ ] Security scanning (Composer Audit, npm audit) implementado
- [ ] Deployment automático a staging post-merge main
- [ ] Artifacts (Docker images) versionados y almacenados
- [ ] Pipeline completo ejecuta en < 15 minutos
- [ ] Notificaciones de status a Teams/Slack configuradas

---

## Detalles Técnicos y Notas de Implementación

### CI Pipeline (.github/workflows/ci.yml)
```yaml
name: CI Pipeline

on: [pull_request]

jobs:
  lint-and-test:
    runs-on: ubuntu-latest
    
    services:
      sqlserver:
        image: mcr.microsoft.com/mssql/server:2019-latest
        env:
          SA_PASSWORD: TestPassword123!
          ACCEPT_EULA: Y
        ports:
          - 1433:1433
      redis:
        image: redis:7-alpine
        ports:
          - 6379:6379
    
    steps:
      - uses: actions/checkout@v4
      
      - name: Setup PHP 8.3
        uses: shivammathur/setup-php@v2
        with:
          php-version: 8.3
          extensions: pdo_sqlsrv, sqlsrv, redis
          
      - name: Install Composer dependencies
        run: composer install --no-interaction --optimize-autoloader
        
      - name: Run PHP Pint (style check)
        run: ./vendor/bin/pint --test
        
      - name: Run PHPStan (static analysis)
        run: ./vendor/bin/phpstan analyse
        
      - name: Run Unit Tests
        run: ./vendor/bin/phpunit --testsuite=Unit
        
      - name: Run Integration Tests
        run: ./vendor/bin/phpunit --testsuite=Integration
        env:
          DB_HOST: localhost
          DB_PASSWORD: TestPassword123!
          
      - name: Setup Node.js
        uses: actions/setup-node@v4
        with:
          node-version: 20
          
      - name: Install Node dependencies
        run: npm ci
        
      - name: Run ESLint
        run: npm run lint
        
      - name: Build assets
        run: npm run build
        
      - name: Security Audit
        run: |
          composer audit
          npm audit --audit-level=high
```

### CD Pipeline (.github/workflows/cd.yml)
```yaml
name: CD Pipeline

on:
  push:
    branches: [main]

jobs:
  build-and-deploy:
    steps:
      - name: Build Docker image
        run: |
          docker build -t rrhh-app:${{ github.sha }} .
          docker tag rrhh-app:${{ github.sha }} rrhh-app:latest
          
      - name: Push to registry
        run: |
          docker push registry.local/rrhh-app:${{ github.sha }}
          docker push registry.local/rrhh-app:latest
          
      - name: Deploy to staging
        run: |
          ssh staging-server "docker-compose pull && docker-compose up -d --no-deps app"
          
      - name: Run smoke tests
        run: ./scripts/smoke-tests.sh staging
```

### Security Scanning
- **PHP**: Composer security audit para vulnerabilidades
- **JavaScript**: npm audit para dependencias frontend
- **SAST**: PHPStan nivel 6+ para análisis estático
- **Container**: Trivy scan para vulnerabilidades en imágenes

### Quality Gates
- Todos los tests deben pasar (0 fallos)
- Cobertura de tests ≥ 70% en código nuevo
- Security audit sin vulnerabilidades high/critical
- Lint checks must pass (PHP Pint + ESLint)

---

## Especificaciones Relacionadas

- `/Docs/specs/ci-cd.md` - Estrategias de deployment y testing
- `/Docs/specs/architecture.md` - Arquitectura de contenedores

---

## Dependencias

- **Bloquea:** `fase1-0003` (API Empleados testing), `fase0-0007` (Tailwind build)
- **Bloqueado por:** `fase0-0001` (Infraestructura base)

---

## Sub-Tareas

- [ ] Crear workflow CI con tests y linting
- [ ] Configurar SQL Server container para integration tests
- [ ] Implementar security scanning en pipeline
- [ ] Crear workflow CD para deployment automatizado
- [ ] Configurar Docker registry y image tagging
- [ ] Implementar smoke tests post-deployment
- [ ] Añadir notificaciones de pipeline status
- [ ] Configurar quality gates y branch protection
- [ ] Crear scripts de rollback automatizado
- [ ] Documentar troubleshooting de pipeline issues

---

## Comentarios y Discusión

**Owner:** [Placeholder - DevOps Engineer]
**Estimación:** 12-16 horas
**Sprint:** Sprint 0 (Semanas 1-2)

**Nota crítica**: Pipeline debe ser robusto desde el inicio ya que todos los demás tickets dependerán de CI/CD funcional para quality assurance.