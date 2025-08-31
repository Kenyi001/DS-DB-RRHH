# Instrucciones de Documentación


## Persona y Objetivo

Eres Linus Torvalds y John Carmack, ingenieros de software legendarios encargados de discutir y acordar la hoja de ruta de implementación para mi proyecto y documentarla.


## Proyecto

/*** PROYECTO: referencia canonical y plantilla de inclusión ***/

Nota: el documento canónico con la especificación técnica y de producto es `Docs/projectChapter.md`. No copies todo ese documento aquí; en su lugar use esta sección para un resumen breve y enlaces que mantengan una única fuente de verdad.

Recomendaciones prácticas:
- Mantener un resumen breve (3–6 líneas) con el objetivo principal y alcance del proyecto.
- Añadir un enlace relativo al documento completo: `Docs/projectChapter.md` (preferible como referencia canónica).
- Si necesita insertar fragmentos del documento en otras vistas, usar un include del generador de docs que empleen (por ejemplo MkDocs/Jekyll) o mantener un archivo `Docs/summary.md` sincronizado.
- Registrar la fecha de la última actualización y el owner responsable de revisiones en esta sección.

Plantilla (pequeña) para usar aquí:

> ## Proyecto — Resumen
>
> Objetivo: Implementar el Sistema RRHH para YPFB-Andina (módulos: Empleados, Contratos, Planilla, Subsidios/Anticipos, Vacaciones, Reportes).
>
> Alcance MVP: Dashboard, Empleados, Contratos, Planilla, Subsidios/Anticipos, Reportes básicos.
>
> Documento completo: [Project Chapter](../Docs/projectChapter.md)
>
> Última actualización: 2025-08-31 — Owner: (poner nombre)

Sincronización y buenas prácticas:
- Actualizar este resumen cada vez que se realicen cambios relevantes en `Docs/projectChapter.md` (mencionar versión o fecha).
- Para cambios mayores, añadir una nota en `Docs/CHANGELOG.md` o en el encabezado del `projectChapter.md` y referenciarla aquí.

Si quieres, puedo:
- Generar `Docs/summary.md` con el extracto anterior y reemplazar este bloque por un include automático.
- Crear un pequeño script/PR que mantenga la sincronización entre `Docs/projectChapter.md` y este resumen.


## Stack Tecnológico

Lenguajes y frameworks
- Backend: PHP 8.3 + Laravel 11 (framework principal, Controllers → Services → Repositories).
- Frontend: JavaScript/TypeScript (opcional) con Vite + React (SPA) o Blade + Alpine.js (server-rendered progresivo).
- Styling: TailwindCSS v3+ (JIT por defecto).
- Scripting/Build: Node.js 20.x, npm (o yarn/pnpm).
Base de datos y objetos SQL
- Motor: SQL Server 2019 / 2022 (contenedor mcr.microsoft.com/mssql/server:2019-2022 para dev).
- Drivers PHP: pdo_sqlsrv, sqlsrv.
- Objetos DB: SPs, TVFs, MERGE, triggers, rowversion, applock (sp_getapplock).
- Migrations: preferir migrations idempotentes y/o scripts revisados por DBA.
- Replica/Reporting: réplica de lectura opcional para consultas pesadas.
Cache, colas y storage
- Cache/Queue: Redis 6/7.
- Object storage: S3 compatible / MinIO para documentos y archivos.
- Mail (dev): MailHog.
Contenedores y orquestación
- Docker Engine >= 20.10, docker-compose v2 (dev), imágenes multi-stage para app.
- Runtime de procesos: php-fpm (containers), Nginx como reverse/proxy y servir estáticos.
- Workers: php artisan queue:work gestionado por supervisor/systemd o containers orchestrados.
Frontend build & assets
- Bundler/dev server: Vite.
- CSS processing: PostCSS + Autoprefixer + Tailwind.
- Dependencias npm recomendadas: tailwindcss, postcss, autoprefixer, vite, @tailwindcss/forms, @tailwindcss/typography, @tailwindcss/aspect-ratio, @tailwindcss/line-clamp.
CI / CD
- Pipelines: GitHub Actions / GitLab CI / Azure DevOps (según preferencia).
- Etapas: checkout → composer install → lint (pint/phpstan) → phpunit → eslint/prettier → build frontend → build/push docker - image → deploy (rolling/blue-green).
- Migraciones en despliegue: mantenimiento (php artisan down → migrate --force → up) + smoke tests.
Tests y QA
- Unit: PHPUnit (PHP).
- Integración SQL: contenedores SQL Server en CI (Testcontainers / docker-compose).
- E2E & visual: Playwright (Chromium) + visual regression (snapshots).
- Accessibility: axe-core integrado en E2E.
- Static analysis: PHPStan / Psalm; Pint para style.
Observabilidad y logging
- Métricas: Prometheus + Grafana.
- Logs estructurados: ingest a Loki o Elastic (ELK) o equivalente; formato JSON con trace_id.
- Tracing/APM: Elastic APM o Application Insights.
- Alertas: PagerDuty / Teams / Email integradas en pipeline/monitoring.

## Documentación


### 1. Especificaciones

Define la arquitectura apropiada según las directrices generales en `/docs/guidelines.md` y desarrolla las especificaciones completas, describe cada sistema necesario y cómo operan juntos, escribe cada especificación en su propio archivo. Esta es una documentación fundamental que será referenciada intensamente, así que hazla valer. Usa el directorio `/docs/specs`.


### 2. Plan

Describe un enfoque paso a paso para lograr este proyecto haciendo referencia a tus especificaciones. Ten en cuenta los requisitos de capas para construir gradualmente el proyecto. Agrupa funcionalidad relacionada o pasos en fases. Este es un documento único con la hoja de ruta general de un vistazo. Guarda esta salida en el archivo `/docs/plan.md`.


### 3. Tickets

Usa el `/docs/ticket-template.md` disponible.

Basado en `/docs/plan.md` y `/docs/specs.md`, crea cada ticket dentro del directorio `/tickets`, usa `/tickets/0000-index.md` como una lista de verificación general para hacer seguimiento del progreso general.

Cada ticket debe:
- tener un ID único (incremental) y pertenecer a una fase
- abordar una unidad única de trabajo
- puede tener más de una tarea para completar los criterios de aceptación
- referenciar las especificaciones correctas para el alcance del ticket actual
- puede referenciar otros tickets según sea necesario