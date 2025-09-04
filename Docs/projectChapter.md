[Start of document metadata]
# Project Chapter — Sistema RRHH (Documento Unificado 1–55)
Empresa: YPFB-Andina
Fecha: 31/08/2025
Stack: Laravel 11 (PHP 8.3) · SQL Server 2019/2022 (sqlsrv) · Redis · TailwindCSS · Nginx

Índice

### Tabla de contenido

- [1. Resumen Ejecutivo](#1-resumen-ejecutivo)
- [2. Alcance y Objetivos](#2-alcance-y-objetivos)
- [3. Requisitos (Funcionales y No Funcionales)](#3-requisitos-funcionales-y-no-funcionales)
- [4. Arquitectura de Solución (Laravel + SQL Server)](#4-arquitectura-de-soluci%C3%B3n-laravel--sql-server)
- [5. Modelo de Datos y Mapeo](#5-modelo-de-datos-y-mapeo)
- [6. Catálogo de Objetos SQL](#6-catal%C3%B3go-de-objetos-sql)
- [7. Concurrencia, Transacciones y Auditoría](#7-concurrencia-transacciones-y-auditor%C3%ADa)
- [8. Backups & Jobs (SQL Server Agent)](#8-backups--jobs-sql-server-agent)
- [9. UX/UI: Paleta, Tipografías y Componentes](#9-uxui-paleta-tipograf%C3%ADas-y-componentes)
- [10. Mapa de Pantallas, Navegación y CRUDs](#10-mapa-de-pantallas-navegaci%C3%B3n-y-cruds)
- [11. Integración & Seguridad (RBAC, logs, cifrado)](#11-integraci%C3%B3n--seguridad-rbac-logs-cifrado)
- [12. Plan de Despliegue (Dev/QA/Prod, CI/CD)](#12-plan-de-despliegue-devqa-prod-cicd)
- [13. Plan de Pruebas y Métricas](#13-plan-de-pruebas-y-m%C3%A9tricas)
- [14. Riesgos y Mitigaciones](#14-riesgos-y-mitigaciones)
- [15. Roadmap y Entregables](#15-roadmap-y-entregables)
- [16. Especificación detallada de páginas (GUI + backend)](#16-especificaci%C3%B3n-detallada-de-p%C3%A1ginas-gui--backend)
- [17. Contratos API (REST)](#17-contratos-api-rest)
- [18. Roles y Permisos (matriz)](#18-roles-y-permisos-matriz)
- [19. Especificación técnica — sp_GenerarPlanillaMensual](#19-especificaci%C3%B3n-t%C3%A9cnica--sp_generarplanillamensual)
- [20. Concurrencia y Transacciones (prácticas aplicadas)](#20-concurrencia-y-transacciones-pr%C3%A1cticas-aplicadas)
- [21. Jobs SQL Server (Backups/Mantenimiento operativa)](#21-jobs-sql-server-backups-mantenimiento-operativa)
- [22. Observabilidad y Rendimiento](#22-observabilidad-y-rendimiento)
- [23. Datos semilla y pruebas](#23-datos-semilla-y-pruebas)
- [24. Plan de páginas (resumen)](#24-plan-de-p%C3%A1ginas-resumen)
- [25. Requerimientos de entorno](#25-requerimientos-de-entorno)
- [26. Guías de implementación rápida](#26-gu%C3%ADas-de-implementaci%C3%B3n-r%C3%A1pida)
- [27. Flujos, validaciones y racional (por módulo)](#27-flujos-validaciones-y-racional-por-m%C3%B3dulo)
- [28. Criterios generales de validación (catálogo)](#28-criterios-generales-de-validaci%C3%B3n-cat%C3%A1logo)
- [29. Decisiones de diseño (racional)](#29-decisiones-de-dise%C3%B1o-racional)
- [30. Estrategia Responsive (móvil primero + escritorio)](#30-estrategia-responsive-m%C3%B3vil-primero--escritorio)
- [31. Estándares de estilos con Tailwind](#31-est%C3%A1ndares-de-estilos-con-tailwind)
- [32. Guía responsive por página](#32-gu%C3%ADa-responsive-por-p%C3%A1gina)
- [33. Contenerización (Docker)](#33-contenerizaci%C3%B3n-docker)
- [34. Pipeline CI/CD (contenedores)](#34-pipeline-cicd-contenedores)
- [35. Prácticas de calidad para responsive](#35-pr%C3%A1cticas-de-calidad-para-responsive)
- [36. Patrones UI/Tailwind por flujo (ejemplos)](#36-patrones-uitailwind-por-flujo-ejemplos)
- [37. Roadmap responsive + Docker](#37-roadmap-responsive--docker)
- [38. tailwind.config.js (propuesto)](#38-tailwindconfigjs-propuesto)
- [39. nginx.conf (optimizado estáticos)](#39-nginxconf-optimizado-est%C3%A1ticos)
- [40. Plantillas .env (dev/qa/prod)](#40-plantillas-env-devqaprod)
- [41. Mapeo Eloquent y Repositorios (skeletons)](#41-mapeo-eloquent-y-repositorios-skeletons)
- [42. Rutas Laravel (web & API) + middleware](#42-rutas-laravel-web--api--middleware)
- [43. Policies y permisos (ejemplo)](#43-policies-y-permisos-ejemplo)
- [44. Catálogo de validaciones (reglas y mensajes)](#44-catal%C3%B3go-de-validaciones-reglas-y-mensajes)
- [45. Máquinas de estado (transiciones)](#45-m%C3%A1quinas-de-estado-transiciones)
- [46. Diagramas (ASCII)](#46-diagramas-ascii)
- [47. QA: checklists por flujo](#47-qa-checklists-por-flujo)
- [48. Seeds y fixtures (Laravel)](#48-seeds-y-fixtures-laravel)
- [49. SQL: objetos clave (plantillas)](#49-sql-objetos-clave-plantillas)
- [50. Scheduler y colas](#50-scheduler-y-colas)
- [51. DR/BCP (runbook)](#51-drbcp-runbook)
- [52. Monitoreo & Alertas](#52-monitoreo--alertas)
- [53. Retención y privacidad de datos](#53-retenci%C3%B3n-y-privacidad-de-datos)
- [54. Asuntos abiertos / Supuestos](#54-asuntos-abiertos--supuestos)
- [55. Glosario](#55-glosario)
- [56. Pasos a seguir (Acciones inmediatas y roadmap corto)](#56-pasos-a-seguir-acciones-inmediatas-y-roadmap-corto)

---

## 1. Resumen Ejecutivo

Implementación del Sistema de RRHH para YPFB-Andina (335 empleados). Módulos núcleo: Empleados, Contratos, Subsidios, Anticipos, Gestión de Salarios/Planilla, Vacaciones, Evaluaciones, Afiliaciones y Reportes. Stack, decisiones arquitectónicas, KPIs y valor de negocio se mantienen como definiciones estratégicas del proyecto.

## 2. Alcance y Objetivos

Resumen del alcance del MVP (módulos incluidos/excluidos), objetivos SMART y OKRs por módulo. KPIs de éxito: P95 < 3s en endpoints, precisión 99.9% en planilla, cobertura de tests ≥70%.

## 3. Requisitos (Funcionales y No Funcionales)

3.1 Funcionales: ABM de Empleados, contratos, subsidios, anticipos con validaciones, planilla mensual automatizada, vacaciones, evaluaciones, afiliaciones, reportes.

3.2 No funcionales: rendimiento, escalabilidad (hasta 2.500 empleados), seguridad (TLS, mínimos privilegios), disponibilidad 99.9%, mantenibilidad y accesibilidad AA.

## 4. Arquitectura de Solución (Laravel + SQL Server)

Patrón 3 capas: UI→Controller→Service→Repository→DB. Uso combinado de Eloquent y llamadas a SP/TVF para operaciones set-based críticas. Redis para caché y colas. Triggers y SPs en DB donde las invariantes deben protegerse.

4.1 Diagrama de Componentes (resumen)

- Frontend: SPA con Vite/React o Blade + Alpine/Tailwind; CDN para assets públicos.
- Backend: Laravel 11 (PHP 8.3) con controlador → service → repository; colas (Redis) para tareas largas; workers php artisan queue:work gestionados por supervisor/systemd o containers.
- Base de datos: SQL Server 2019/2022, réplica de lectura para reporting si se requiere; backups y job agent.
- Cache/Colas: Redis para sessions, cache y queue.
- Storage: S3/MinIO para documentos; referencias en tabla Documentos.
- Observabilidad: Prometheus + Grafana para métricas; Loki/Elastic para logs; APM (App Insights o Elastic APM).

ASCII-deployment (simplificado):

[User] -> [NGINX LB] -> [App replicas (PHP-FPM)] -> [SQLServer Primary]
                                   \-> [Redis]
                                   \-> [S3/MinIO]
                                   \-> [Worker replicas]

4.2 Contratos API y ejemplo OpenAPI (planilla)

- Endpoint: POST /api/v1/planilla/generar
- Autenticación: Bearer token (Sanctum/JWT) + permiso can:generar-planilla

Request JSON:
{
  "mes": 8,
  "gestion": 2025,
  "idempotency_key": "uuid-v4"
}

Response 200 (éxito):
{
  "success": true,
  "planilla_id": 123,
  "message": "Planilla 8/2025 generada"
}

Response 4xx/5xx: usar estructura de errores estándar
{
  "success": false,
  "code": -3,
  "error": "Proceso en curso"
}

OpenAPI snippet (resumen):
- security: bearerAuth
- responses: standardized with schema ErrorResponse/SuccessResponse

4.3 Contrato DB (entrada/salida sp_GenerarPlanillaMensual)

- Inputs: @Mes INT, @Gestion INT, @Usuario NVARCHAR(100), @IdempotencyKey UNIQUEIDENTIFIER (nullable)
- Output: @PlanillaId INT OUTPUT
- Efectos secundarios: inserta/actualiza registros en GestionSalarios y LogPlanilla; actualiza Anticipos; escribe AuditLog.
- Garantías: idempotencia por IdempotencyKey; exclusividad por applock.

---

## 5. Modelo de Datos y Mapeo

Tablas núcleo: Empleados, Contratos, GestionSalarios, Subsidios, TiposSubsidio, Anticipos, Evaluaciones, SolicitudesVacaciones, Afiliaciones, Documentos, Usuarios, Roles, Permisos, UsuarioRoles, RolesPermisos, AuditLog.

Recomendaciones: rowversion en tablas críticas, soft delete con FechaBaja/UsuarioBaja, FK estrictas, documentos en blob/storage con referencia en tabla Documentos.

## 6. Catálogo de Objetos SQL

6.1 Índices optimizados

```sql
CREATE NONCLUSTERED INDEX IX_GestionSalarios_PagosPendientes
ON GestionSalarios (Gestion, Mes)
INCLUDE (IDContrato, LiquidoPagable, FechaPago);

CREATE NONCLUSTERED INDEX IX_Contratos_ActivoCargoDepto
ON Contratos (IDDepartamento, IDCargo, Estado)
INCLUDE (IDEmpleado, NumeroContrato, FechaInicio, FechaFin)
WHERE Estado = 1;

CREATE NONCLUSTERED INDEX IX_SolicitudesVacaciones_JefePendientes
ON SolicitudesVacaciones (JefeAprobador, EstadoSolicitud, FechaInicio)
INCLUDE (IDContrato, FechaFin, DiasVacaciones, Observaciones);

CREATE NONCLUSTERED INDEX IX_Empleados_Departamento_Estado
ON Empleados (Estado)
INCLUDE (Nombres, ApellidoPaterno);

CREATE NONCLUSTERED INDEX IX_Postulaciones_Recientes
ON Postulaciones (FechaPostulacion DESC)
WHERE Estado = 'Pendiente';
```

6.2 Triggers críticos

- `trg_Validar_Anticipo` (INSTEAD OF INSERT) valida tope ≤ 50% del haber básico y contrato activo.
- `TRG_Subsidios_A_GestionSalarios` (AFTER INSERT/UPDATE) propaga cambios a `GestionSalarios` mediante MERGE.
- `trg_Empleados_Audit` (AFTER INSERT/UPDATE/DELETE) registra en `AuditLog` (JSON antes/después).

Ejemplo (anticipo):

```sql
CREATE TRIGGER trg_Validar_Anticipo
ON Anticipos
INSTEAD OF INSERT
AS
BEGIN
    SET NOCOUNT ON;
    IF EXISTS (SELECT 1 FROM inserted i INNER JOIN Contratos c ON i.IDContrato = c.IDContrato
               WHERE i.MontoAnticipo > (c.HaberBasico * 0.5))
    BEGIN
        RAISERROR('El anticipo no puede superar el 50% del salario básico vigente.', 16, 1);
        RETURN;
    END;
    -- Validaciones adicionales y INSERT
    INSERT INTO Anticipos (...) SELECT ... FROM inserted;
END;
```

6.3 Funciones de cálculo

- `fn_CalcularSalarioTotal(@IDEmpleado, @FechaInicio, @FechaFin)` → DECIMAL
- `fn_TotalBeneficiosPeriodo(@IDEmpleado, @FechaInicio, @FechaFin)` → TABLE

6.4 Stored Procedures core

- `sp_CalcularSalarioMensual(@IDContrato, @Mes, @Gestion)` — cálculo por contrato y MERGE upsert en `GestionSalarios`.

Ejemplo (resumen):

```sql
CREATE OR ALTER PROCEDURE sp_CalcularSalarioMensual
    @IDContrato INT,
    @Mes INT,
    @Gestion INT
AS
BEGIN
    SET NOCOUNT ON;
    -- Obtener básico, subsidios y anticipos
    -- Calcular líquido y MERGE en GestionSalarios
END;
```

6.5 Métricas de rendimiento (índices)

Consultas sugeridas sobre `sys.dm_db_index_usage_stats` y `sys.indexes` para medir uso de índices y optimizar.

## 7. Concurrencia, Transacciones y Auditoría

Objetivo: garantizar integridad y consistencia en operaciones críticas (generación de planilla, descuentos, cierre de periodos) sin sacrificar la experiencia de usuario.

7.1 Estrategia general
- Activar READ_COMMITTED_SNAPSHOT y ALLOW_SNAPSHOT_ISOLATION para reducir bloqueos de lectura y mejorar concurrencia lectora.
- Usar combinación de concurrencia optimista (rowversion / timestamp) en endpoints UI y concurrencia controlada (locks de aplicación) para procesos largos o exclusivos.
- Emplear transacciones cortas y deterministas en código T-SQL; mover trabajos set‑based al servidor (SPs) para evitar chatarra de round trips.

7.2 Patrones recomendados
- Transacción segura T-SQL (plantilla):

```sql
BEGIN TRY
  BEGIN TRANSACTION;
  -- operaciones DML aquí
  COMMIT TRANSACTION;
END TRY
BEGIN CATCH
  IF XACT_STATE() <> 0 ROLLBACK TRANSACTION;
  THROW; -- propaga error con contexto
END CATCH;
```

- Uso de `sp_getapplock` para exclusividad lógica (ej: generar planilla por periodo):

```sql
DECLARE @rc INT;
EXEC @rc = sp_getapplock @Resource = 'planilla_2025_08', @LockMode = 'Exclusive', @LockTimeout = 30000;
IF @rc < 0
BEGIN
  THROW 51000, 'No se pudo adquirir bloqueo para el periodo', 1;
END
-- ejecutar generación
EXEC sp_releaseapplock @Resource = 'planilla_2025_08';
```

- Batch + tabla temporal: procesar contratos en batches (ej. 200–1000 filas) para reducir memoria y permitir checkpoints.

7.3 Reintentos y backoff
- Implementar reintentos exponenciales en la capa app para errores transitorios (deadlock 1205, timeouts). Límite 3–5 intentos con backoff jitter.
- En jobs y workers, usar idempotencia (IdempotencyKey / LogPlanilla) para evitar efectos dobles en reintentos.

7.4 Auditoría
- `AuditLog` (JSON) por entidad crítica con campos: { audit_id, entity, entity_id, action, user_id, trace_id, created_at, payload_before, payload_after, ip }. Guardar hashes y metadatos para búsquedas eficientes.
- Triggers vs. app audit: usar triggers simples para garantizar audit en cambios DML críticos, y complementar con audit desde la aplicación para contexto (comentarios, trace_id).

Ejemplo simplificado de trigger de auditoría:

```sql
CREATE TRIGGER trg_Empleados_Audit
ON Empleados
AFTER INSERT, UPDATE, DELETE
AS
BEGIN
  SET NOCOUNT ON;
  INSERT INTO AuditLog (entity, entity_id, action, user_id, created_at, payload_before, payload_after, trace_id)
  SELECT 'Empleados', COALESCE(i.IDEmpleado, d.IDEmpleado),
       CASE WHEN i.IDEmpleado IS NOT NULL AND d.IDEmpleado IS NULL THEN 'INSERT'
        WHEN i.IDEmpleado IS NOT NULL AND d.IDEmpleado IS NOT NULL THEN 'UPDATE'
        ELSE 'DELETE' END,
       SUSER_SNAME(), SYSUTCDATETIME(),
       (SELECT d.* FOR JSON PATH, WITHOUT_ARRAY_WRAPPER),
       (SELECT i.* FOR JSON PATH, WITHOUT_ARRAY_WRAPPER),
       NULL
  FROM inserted i FULL OUTER JOIN deleted d ON i.IDEmpleado = d.IDEmpleado;
END;
```

7.5 Telemetría y métricas transaccionales
- Instrumentar métricas: duration por SP (histograma), rows processed, rows failed, deadlocks, lock waits. Exponer a Prometheus.

## 8. Backups & Jobs (SQL Server Agent)

Objetivo: asegurar RTO/RPO definidos por negocio, verificar restauraciones y automatizar mantenimiento para rendimiento y estabilidad.

8.1 Política de backups (ejemplo)
- Full backup: diario a 02:00 (retención 14 días)
- Differential: cada 6 horas (retención 7 días)
- Log backups: cada 15 minutos (retención 7 días)
- Copia offsite/immutable: semanal y tras cada release crítico
- Opcional: COPY_ONLY en backups puntuales fuera ventana

8.2 Encriptación y seguridad
- Usar Transparent Data Encryption (TDE) en producción o cifrado de backups con certificados/keys.
- Asegurar que archivos de backup escriban a almacenamiento seguro con control de acceso.

8.3 Jobs operativos esenciales
- Job: DBCC CHECKDB (semana, con notificación si falla)
- Job: Rebuild/Reorganize índices (planificado según fragmentation)
- Job: Update Statistics (diario)
- Job: Purge/Archive de AuditLog y rotación (según política de retención)
- Job: Backup verification – restaurar en un sandbox y ejecutar smoke tests (periodicidad semanal / mensualmente)

8.4 Monitoreo y alertas
- Alertas por fallos de backup, espacio en disco, jobs que exceden duración esperada.
- Métricas: tiempo de última copia, tiempo medio de restore, percentil de duración de restauración.

8.5 Runbook de restauración (resumido)
1. Identificar punto de recovery (último full + dif + logs necesarios).
2. Restaurar full WITH NORECOVERY, luego difs y logs hasta el punto deseado.
3. Ejecutar validaciones de integridad y smoke tests (tablas clave, contadores).
4. Documentar resultado y notificar stakeholders.

## 9. UX/UI: Paleta, Tipografías y Componentes

Objetivo: proporcionar una guía de diseño sistemática para acelerar desarrollo frontend, garantizar accesibilidad y coherencia visual.


9.1 Tokens y paleta (YPFB — azul / blanco / rojo)

Nota: asumo que la identidad visual busca priorizar azul + blanco + rojo como tokens principales; los hex siguientes son aproximaciones útiles para UI y deben validarse con el manual de marca oficial.

- Tokens (YPFB — propuesta de implementación):
  - color-ypfb-blue (primario): #0A3E8F  /* Azul corporativo profundo */
  - color-ypfb-white: #FFFFFF
  - color-ypfb-red (acentos/CTA): #E31B23  /* Rojo vivo para acciones críticas */
  - neutral-900: #111827
  - neutral-700: #374151
  - neutral-500: #6B7280
  - neutral-300: #D1D5DB
  - neutral-100: #F3F4F6
  - success: #16A34A
  - danger: #DC2626
  - warning: #F59E0B

Usos recomendados:
- Botón primario: fondo `color-ypfb-blue`, texto blanco.
- CTA crítico (eliminar/confirmar/alert): usar `color-ypfb-red` con texto blanco.
- Fondos y secciones: neutros `neutral-100..300` para contenedores; reservar `neutral-900/700` para textos primarios.
- Iconografía y marcas: versión monocroma en `color-ypfb-blue` o `color-ypfb-white` según contraste.

Contraste y accesibilidad:
- Mantener contraste mínimo 4.5:1 para texto normal; usar herramientas como axe o contrast-checker para verificar combinaciones inusuales.

9.2 Tipografías y tokens tipográficos (propuesta)

- Primaria (UI): Roboto, sistema: `font-family: 'Roboto', system-ui, -apple-system, 'Segoe UI', sans-serif;`
  - Weights recomendados: 400 (regular), 500 (medium), 700 (bold)
- Secundaria (reports/print): Merriweather o Noto Serif: `font-family: 'Merriweather', 'Noto Serif', serif;` (para PDFs/impresos)
- Stack de fallbacks: siempre incluir fuentes genéricas (sans-serif / serif) para robustez.

Tokens tipográficos (ejemplo):
- font-size-xs: 12px
- font-size-sm: 14px
- font-size-md: 16px
- font-size-lg: 20px
- font-size-xl: 24px
- line-heights: 1.25–1.6 según contexto

Licencias y rendimiento:
- Preferir fuentes hospedadas por CDN o variable fonts (Roboto Variable) para reducir requests; respetar licencias de uso comercial.

9.3 Sistema de componentes (principios)

El sistema de componentes debe definirse como librería interna (Atomic Design): atoms (botones, inputs), molecules (form-groups, selects combinados), organisms (datatables, card lists) y templates/pages. Cada componente tendrá:

- Documentación de props/inputs y ejemplos de uso.
- Tests visuales y unitarios (Playwright snapshots + Jest/RTL o equivalente).
- Soporte de accesibilidad (roles, aria-attributes, keyboard navigation).
- Estilos basados en tokens Tailwind y utilidades compartidas; evitar estilos en línea y duplicación.

9.6 Logo y marca (guía rápida)

Asunción: dispones de un logo oficial (SVG). Estas reglas son pautas prácticas para su uso en la app:

- Variantes de color:
  - Principal: logo en `color-ypfb-blue` sobre fondo blanco.
  - Invertido: logo blanco sobre fondo `color-ypfb-blue` o `color-ypfb-red` cuando el contraste lo requiera.
  - Monocromo: usar `neutral-900` o blanco para contexts neutros.

- Espaciado mínimo: dejar un clear space equivalente a la altura de la "cap" del logotipo a su alrededor (mínimo 8px en iconos pequeños, más en cabeceras).

- Tamaños mínimos: para web 24px de altura para icono; en cabecera 32–48px según diseño. En PDF/print seguir guía de marca.

- Accesibilidad: siempre suministrar `alt` descriptivo (ej. "YPFB - Sistema RRHH"). Evitar superponer texto pequeño sobre el logo.

- SVG preferido: usar SVG optimizado (sin metadata extra), y versiones PNG solo para legacy donde SVG no sea compatible.

- Prohibiciones comunes: no recolorar el logotipo con gradientes distintos, no distorsionar ni rotar el logo, no usar efectos que comprometan legibilidad.

Si quieres, extraigo los tokens de color y la configuración tipográfica a un archivo `Docs/design/tokens.md` y añado muestras visuales (SVG/PNG) si proporcionas el logo fuente.

9.3 Sistema de componentes (principios)
- Componentes atómicos y composables: Button, Input, Select, DataTable, Modal, Toast, DatePicker, Stepper
- Cada componente debe exponer props/accessibility hooks y ser probado por visual regression.
- States: default / hover / focus / active / disabled / error / loading

9.4 Formularios y validaciones UX
- Mostrar errores inline y resumen arriba del formulario para accesibilidad.
- Validaciones client-side y server-side; normalizar mensajes (lang/es/*.php) y códigos de error.

9.5 Diseño accesible y mobile‑first
- Mobile-first: nav drawer en móvil, sidebar en desktop; acciones primarias accesibles en la parte superior.
- Atajos de teclado para tablas y páginas de alto uso.

## 10. Mapa de Pantallas, Navegación y CRUDs

Objetivo: mapear rutas, permisos y comportamientos CRUD por pantalla para que el desarrollo backend/frontend sea paralelo y alineado.

10.1 Principios de navegación
- Ruta base: `/app` para interfaz autenticada y `/api/v1` para servicios.
- Navegación jerárquica: Dashboard → Módulos → Recurso → Acciones (CRUD)
- Permisos condicionan visibilidad de rutas y acciones (p. ej. botones ocultos si no posee permiso).

10.2 Mapa condensado de pantallas y permisos (MVP prioritario)
- Dashboard — `/dashboard` — permiso: `view_dashboard` — componentes: KPI cards, alerts, timeline — aceptación: carga <2s.
- Empleados (lista) — `/empleados` — `empleados.view` — datatable server-side, filtros, export — aceptación: filtros <1s.
- Empleado (detalle) — `/empleados/{id}` — `empleados.view` — tabs: perfil, contratos, documentos, auditoría — aceptación: descarga documento y ver historial.
- Contratos (wizard/create) — `/contratos/new` — `contratos.create` — stepper, validación no solape — aceptación: no permitir solape.
- Planilla (preview/generar) — `/planilla` — `planilla.view|planilla.generar` — preview grid, generar (idempotente) — aceptación: 202 Accepted + tracking.
- Subsidios/Anticipos — `/subsidios`, `/anticipos` — `subsidios.manage`, `anticipos.request` — formularios y approvals — aceptación: reglas de negocio aplicadas por triggers/SPs.

10.3 Comportamientos CRUD y contraints
- Creación: validar en UI (FormRequests) y en DB (triggers/constraints); devolver errores estructurados.
- Edición: usar rowversion/If-Match para evitar sobreescrituras silenciosas.
- Borrado: soft-delete con `FechaBaja` y `UsuarioBaja`; operaciones físicas solo por jobs de retención.

10.4 Rutas API (ejemplos)
- GET /api/v1/empleados?filter...
- POST /api/v1/empleados
- GET /api/v1/empleados/{id}
- PUT /api/v1/empleados/{id}
- DELETE /api/v1/empleados/{id}

## 11. Integración & Seguridad (RBAC, logs, cifrado)

Objetivo: proteger datos, controlar accesos y garantizar trazabilidad mientras se facilita integración con sistemas internos/externos.

11.1 Autenticación y autorización
- Autenticación: Laravel Sanctum (SPA) o JWT/OAuth2 para integraciones externas y APIs.
- Autorización: RBAC con Roles y Permisos (tables: Roles, Permissions, RolePermissions, UserRoles). Policies en Laravel ligados a permisos granulares (p. ej. `planilla.generar`).
- Minimizar privilegios DB: la app usa un usuario con permisos DML limitados; procedimientos almacenados ejecutan lógica sensible y pueden usar `EXECUTE AS` cuando se requiere.

11.2 Logs y trazabilidad
- Formato: JSON estructurado con campos mínimos: { timestamp, level, service, env, user_id, trace_id, route, duration_ms, message, meta }
- Niveles: INFO para eventos de negocio, WARN para condiciones recuperables, ERROR para fallos.
- Correlación: propagar `X-Trace-Id` desde frontend a backend y persistir en DB (AuditLog/LogPlanilla) para trazabilidad completa.
- Retención: logs de app 90 días, AuditLog 5 años (según regulaciones).

11.3 Cifrado y gestión de secretos
- En tránsito: TLS 1.2+ para todos los endpoints y servicios internos.
- En reposo: cifrado de backups y uso de TDE para la base de datos en producción si procede.
- Secret management: Vault/Secrets Manager para credenciales; `.env` solo en dev, nunca en SCM.

11.4 Integraciones externas
- Contratos: definir OpenAPI/Swagger para cada integración; versionado y contratos de compatibilidad.
- Retries y idempotencia: integrar con Idempotency-Key para operaciones mutativas remotas.

11.5 Hardening y buenas prácticas
- OWASP Top10 mitigations (input validation, output encoding, auth flows hardened)
- Rate limiting (throttle) en endpoints sensibles y WAF para capa pública.
- Escaneo SAST/DAST en pipelines y pentests programados anualmente.

Resumen: estas secciones sirven como guía operativa y técnica para implementar controles de concurrencia, backups, experiencia de usuario y seguridad con ejemplos y runbooks mínimos que el equipo deberá adaptar al entorno de producción.
11.6 Controles de Seguridad (detallados)

- Autenticación y Autorización: Laravel Sanctum o JWT; políticas (Policies) aplicadas en controllers; gates para acciones críticas (planilla.pagar, planilla.generar).
- Gestión de secretos: usar Vault o AWS Secrets Manager; no almacenar secretos en repositorio; .env sólo en entornos locales.
- Base de datos: usuarios con mínimos privilegios; separar cuenta de lectura para reporting; en producción usar cuentas con permisos DML restringidos y procedures con EXECUTE AS si se requiere.
- Cifrado: TLS 1.2+ en todos los servicios; cifrado en reposo para backups y storage (S3 SSE).
- Auditoría: `AuditLog` con JSON antes/después, usuario, fecha, IP; retención 5 años.
- Protección contra inyección: parametrizar consultas, no concatenar SQL dinámico; validar/sanitizar inputs.
- WAF / Rate limiting: reglas base y throttling en endpoints críticos (login, planilla generar). 

11.2 Roles y Privilegios mínimos (ejemplo)

- Admin RRHH: acceso completo (mantenimiento)
- RRHH Analista: crear/editar empleados/contratos/subsidios
- Jefe Área: aprobar vacaciones/anticipos
- Contabilidad: ver planillas, marcar pago
- Auditor: solo lectura de AuditLog/descargas

---

## 12. Plan de Despliegue (Dev/QA/Prod, CI/CD)

Ramas: main/develop. Pipelines: composer install/test, phpunit, pint, eslint, build imagen y despliegue rolling/blue-green. Scripts de migración y warmup.

12.1 CI/CD (pipeline detallado)

- CI (on PR):
  - checkout
  - composer install --no-interaction
  - phpstan/pint lint
  - phpunit (unit tests)
  - eslint + prettier (frontend)
  - build assets (vite)
  - security-scan (optional)
- CD (on merge to main):
  - build Docker image tagged with git-sha
  - push to registry
  - run DB migrations in maintenance mode: php artisan down; php artisan migrate --force; php artisan db:seed --class=MinimalSeed; php artisan up
  - deploy new image (rolling): update service in compose/k8s, wait for healthchecks, switch traffic
  - run smoke tests and rollback on failure

12.2 Artefacts y Rollback

- Mantener imágenes por tag y despliegues por tag; implementar healthcheck y una ventana de observabilidad (5–10m) antes de finalizar despliegue.

---

## 13. Plan de Pruebas y Métricas

Unitarias (PHPUnit), Integración (repos+SPs), E2E (Playwright), Carga (2.5k empleados). KPIs: P95, tasa 5xx, duración planilla, jobs fallidos.

## 14. Riesgos y Mitigaciones

Listado por sprint y mitigaciones (p. ej. prototipar applock para planilla, validación incremental para migración de datos).

## 15. Roadmap y Entregables

Cronograma 8 sprints (Fundación → Empleados → Contratos → Subsidios/Anticipos → Planilla → Reportes → Vacaciones/Evaluaciones → Afiliaciones/Finiquitos → Auditoría/Producción). DoD por feature y entregables por sprint.

## 16. Especificación detallada de páginas (GUI + backend)

Cada página: rutas, UI, controlador/servicio, validaciones UI/API/DB, concurrencia/transacciones, índices y errores esperados.

## 17. Contratos API (REST)

Endpoints clave: CRUD empleados y contratos; alta subsidios/anticipos; `POST /api/planilla/generar {mes,gestion}` que ejecuta SP.

## 18. Roles y Permisos (matriz)

Matriz: Admin RRHH, Jefe RRHH, Analista, Jefe Área, Contabilidad, Auditor con permisos C/R/U/D/Generar/Pagar.

## 19. Especificación técnica — sp_GenerarPlanillaMensual

19.1 Signature

```sql
CREATE OR ALTER PROCEDURE sp_GenerarPlanillaMensual
    @Mes INT,
    @Gestion INT,
    @Usuario NVARCHAR(100),
    @IdempotencyKey UNIQUEIDENTIFIER = NULL,
    @PlanillaId INT OUTPUT
AS
```

19.2 Lógica paso a paso (resumen)

- Validaciones iniciales (mes/gestion)
- Verificar idempotencia (LogPlanilla)
- Adquirir applock exclusivo para período
- Snapshot de contratos activos (tabla temporal #ContratosActivos)
- Calcular por contrato (llamada a `sp_CalcularSalarioMensual`) en batches
- Marcar anticipos como descontados
- Upsert/actualizar `GestionSalarios` y `LogPlanilla` (estado, contadores)
- Auditoría y liberación de lock
- Manejo de errores: rollback, actualizar LogPlanilla como Error

19.3 Tabla de Log para trazabilidad

```sql
CREATE TABLE LogPlanilla (
    PlanillaId INT IDENTITY(1,1) PRIMARY KEY,
    IdempotencyKey UNIQUEIDENTIFIER NULL,
    Mes INT NOT NULL,
    Gestion INT NOT NULL,
    Usuario NVARCHAR(100) NOT NULL,
    FechaInicio DATETIME2 NOT NULL,
    FechaFin DATETIME2 NULL,
    EstadoProceso NVARCHAR(20) NOT NULL,
    ContratosProcessados INT NULL,
    Observaciones NVARCHAR(500) NULL,
    UNIQUE(IdempotencyKey)
);
```

19.4 Uso desde Laravel (ejemplo PlanillaService)

```php
class PlanillaService
{
    public function generarPlanillaMensual(int $mes, int $gestion, string $usuario): array
    {
        $idempotencyKey = Str::uuid();
        try {
            DB::statement('EXEC sp_GenerarPlanillaMensual ?, ?, ?, ?, ?', [
                $mes, $gestion, $usuario, $idempotencyKey, 0
            ]);
            $planillaId = DB::selectOne('SELECT PlanillaId FROM LogPlanilla WHERE IdempotencyKey = ?', [$idempotencyKey])->PlanillaId;
            return ['success' => true, 'planillaId' => $planillaId];
        } catch (\Exception $e) {
            Log::error('Error generando planilla', ['error' => $e->getMessage()]);
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
}
```

19.5 Códigos de Error Normalizados

| Código | Descripción | Acción Recomendada |
|--------|-------------|-------------------|
| 0 | Éxito | Continuar |
| -1 | Mes inválido | Verificar parámetro mes (1-12) |
| -2 | Gestión inválida | Verificar parámetro gestión |
| -3 | No se pudo adquirir bloqueo | Reintentar en 30s |
| -4 | Planilla ya existe | Verificar estado actual |
| -99 | Error general | Revisar logs detallados |

19.6 Métricas de rendimiento

Consulta sobre `LogPlanilla` para medir duración y contadores de proceso.

## 20. Concurrencia y Transacciones (prácticas aplicadas)

Detalles operativos: activar READ_COMMITTED_SNAPSHOT y ALLOW_SNAPSHOT_ISOLATION; uso de rowversion; patrones UPDLOCK/HOLDLOCK y application locks para procesos críticos; reintentos en la capa app.

## 21. Jobs SQL Server (Backups/Mantenimiento operativa)

Definición de jobs y horarios: DBCC, backups, index maintenance y alertas.

## 22. Observabilidad y Rendimiento

Usar query store, APM (App Insights / Prometheus), logs estructurados con traceId, alertas en P95 y jobs fallidos.

22.1 Observabilidad (implementación práctica)

Métricas a exportar (Prometheus):
- API: request_duration_seconds (histograma), request_errors_total, request_rate
- Planilla: planilla_generation_duration_seconds, planilla_generation_success_total, planilla_generation_fail_total
- DB: deadlocks_total, long_queries_seconds (>5s), open_transactions
- Jobs: queue_jobs_processed_total, queue_jobs_failed_total

Tracing:
- Propagar traceId desde frontend → backend → DB/worker y escribir en logs; correlacionar traces en APM.

Logs:
- JSON structured logs con fields {timestamp, level, service, env, user_id, trace_id, route, duration_ms, message}
- Retener logs críticos 90 días; índices en log store para AuditLog queries.

Alertas (ejemplos):
- P95 > 3s por 15m → alerta P95-High
- Planilla generation failures > 3 en 24h → alerta crítica
- Deadlocks > 10/h → alerta DB

---

## 23. Datos semilla y pruebas

- Seeders:
  - RolesPermisosSeeder, DepartamentosSeeder, CargosSeeder
  - EmpleadosSeeder (335 reales mínimo) con datos coherentes
  - ContratosSeeder enlazando empleados
  - SubsidiosSeeder y AnticiposSeeder con casos borde
- Tests automatizados:
  - Unit: services, helpers, policies
  - Integration: repos que ejecutan SPs en contenedor SQL Server (usar Testcontainers / docker-compose for CI)
  - E2E: Playwright/Chromium para flujos críticos
  - SQL tests: validación de resultados esperados de `sp_CalcularSalarioMensual` para escenarios numerados

23.1 Estrategia de validación de migración de datos

Objetivo: garantizar que la migración desde sistemas legados hacia la base de datos destino sea verificable, reversible (parcialmente) y auditable.

Entregables por fase:
- Plantilla de mapping CSV → tabla (campo origen, tipo, transformación, campo destino, regla de validación, ejemplo).
- Scripts de carga idempotentes (batches) y scripts de reconciliación automatizados.
- Playbook de backout y ventana de corte con checklists para cada lote.

Fase 0 — Análisis y mapping
- Generar documento `mapping.csv` con columnas: source_column, sample_value, transform_sql, target_table, target_column, nullable, max_length, validation_rule.
- Definir reglas de calidad de datos (ej.: CI/ID unique, fecha nacimiento razonable, montos ≥ 0, emails válidos).
- Identificar campos sensibles y decidir enmascaramiento para entornos de staging.

Fase 1 — Migración de prueba a staging (subset)
- Crear entorno staging con snapshot de esquema en producción; limpiar/mascarar PII.
- Ejecutar migración de muestra (1–5% de datos) por lotes; validar performance y errores.
- Ejecutar reconciliación preliminar: counts por tabla, counts por clave (empleado), y checksum base.

Fase 2 — Validación por scripts de reconciliación
- Scripts automáticos:
  - Conteo por entidad:
    SELECT COUNT(*) FROM Empleados;
    SELECT COUNT(*) FROM Contratos;
  - Conteo por entidad por fecha/periodo y por estado.
  - Checksum por empleado (ejemplo SQL Server):

```sql
-- Checksum por empleado (ejemplo)
SELECT e.IDEmpleado,
       HASHBYTES('MD5', CONCAT(
           ISNULL(e.CI,''), '|', ISNULL(e.Nombres,''), '|', ISNULL(e.ApellidoPaterno,''), '|', ISNULL(e.FechaNacimiento,''))) AS EmpHash
FROM Empleados e
WHERE e.IDEmpleado IN (/* lote */);
```

- Comparar hashes entre origen y destino (exportar hash por empleado desde origen y comparar con destino);
- Reconciliación de balances y totales (ej.: sumatoria de haberes por periodo por contrato) y tolerancias definidas.

Fase 3 — Migración en ventanas (por lotes) y validación final
- Definir tamaño de lote (p. ej. 500–2000 registros) según performance observada.
- Plan de ejecución:
  1. Poner en modo mantenimiento si es corte total; para migración parcial, usar ventanas off-hours.
  2. Ejecutar lote N: cargar datos, ejecutar validaciones automáticas, ejecutar reconciliación y generar reporte.
  3. Si éxito, marcar lote como completado y proceder al siguiente.
  4. Si falla, ejecutar backout para ese lote (restaurar tablas desde backup incremental o reverter registros insertados por idempotencia) y reportar.
- Post-migración: ejecutar scripts de reconciliación globales y reporte de discrepancias.

Backout y rollback
- Mantener backup diferencial antes de cada ventana crítica.
- Diseñar carga idempotente (usar claves naturales y `MERGE` con control de origen para evitar duplicados).
- Registrar en `MigrationLog` cada lote con status, hash inicial/final y errores.

Criterios mínimos de aceptación (Data Migration)
- Cobertura: ≥ 99.5% filas migradas sin discrepancias críticas.
- Reconciliación: todos los checksums aplicados para lotes muestreados coinciden; totales por periodos dentro de tolerancia aceptada (p. ej. 0.1%).
- Errores críticos: 0 (errores bloqueantes que impidan operación normal)
- Operacional: scripts y playbooks documentados y validados en Scripting CI.

---

## 24. Plan de páginas (resumen)

Resumen ejecutivo de páginas y prioridades (MVP → Fase siguiente). Para cada página se indica: ruta, propósito, permisos mínimos, componentes UI principales y criterios de aceptación rápidos.

- Dashboard
  - Ruta: /dashboard
  - Propósito: vista ejecutiva KPIs (empleados activos, contratos próximos a vencer, planillas recientes)
  - Permisos: view_dashboard
  - Componentes: KPI cards, gráfico tendencias, lista de alertas
  - Criterio: Carga inicial <2s con 335 empleados; KPIs coinciden con consultas de DB.

- Empleados (lista)
  - Ruta: /empleados
  - Propósito: listar y filtrar empleados
  - Permisos: empleados.view
  - Componentes: datatable server-side, filtros, paginación, export CSV
  - Criterio: filtros aplicados <1s (p95) para dataset semilla.

- Empleado (detalle)
  - Ruta: /empleados/{id}
  - Propósito: ver ficha, contratos, documentos, historial
  - Permisos: empleados.view
  - Componentes: tabs (perfil, contratos, documentos, auditoría)
  - Criterio: documento descargable y trazable; auditoría visible.

- Contratos (wizard)
  - Ruta: /contratos/new
  - Propósito: alta/renovación sin solapes
  - Permisos: contratos.create
  - Componentes: stepper, validaciones instantáneas (fecha, solape), preview PDF
  - Criterio: no permitir solape; respuesta 400 con detalle en caso invalido.

- Subsidios / Anticipos
  - Ruta: /subsidios, /anticipos
  - Propósito: gestión política y aplicación en planilla
  - Permisos: subsidios.manage, anticipos.request/approve
  - Componentes: forms, historiales, triggers de validación
  - Criterio: trigger anticipo bloquea si >50% del básico.

- Gestión de Salarios / Planilla
  - Ruta: /planilla, /api/v1/planilla/generar
  - Propósito: preview y generación idempotente de planilla
  - Permisos: planilla.generar, planilla.pagar
  - Componentes: preview grid, generate button, job tracker
  - Criterio: generación <30s (P95) y registro en LogPlanilla.

- Vacaciones, Evaluaciones, Afiliaciones, Documentos, Reportes, Seguridad, Auditoría, Parámetros
  - Ruta y componentes por módulo (detallar según prioridad del sprint)

Notas:
- Priorizar en MVP: Dashboard, Empleados, Contratos, Planilla, Subsidios/Anticipos, Reportes básicos.
- UI: mobile-first, accesibilidad AA, tests visuales por breakpoint.

## 25. Requerimientos de entorno

Entorno de desarrollo (mínimo):
- OS: Windows/macOS/Linux
- Docker Engine >= 20.10, docker-compose v2
- PHP 8.3 (cli) con ext: pdo_sqlsrv, sqlsrv, intl, zip
- Composer 2.x
- Node.js 20.x, npm/yarn
- SQL Server: image mcr.microsoft.com/mssql/server:2019-2022 (para dev usar contenedor)
- Redis 6/7
- Mailhog (dev)

Recursos recomendados para entornos:
- Dev (local): 2 CPU, 4 GB RAM
- CI: 4 CPU, 8 GB RAM (ejecución de tests + contenedores DB)
- QA/Staging: 2–4 vCPU, 8–16 GB RAM, almacenamiento rápido
- Prod: según carga, mínimo 4 vCPU, 16GB RAM y plan de escalado para DB

Variables esenciales en `.env` (ejemplo):
- APP_ENV, APP_DEBUG, APP_URL
- DB_CONNECTION=sqlsrv, DB_HOST, DB_PORT, DB_DATABASE, DB_USERNAME, DB_PASSWORD
- REDIS_HOST, REDIS_PASSWORD
- QUEUE_CONNECTION=redis
- MAIL_MAILER=smtp, MAIL_HOST, MAIL_PORT, MAIL_USERNAME, MAIL_PASSWORD
- STORAGE_DRIVER=s3, S3_BUCKET, S3_KEY, S3_SECRET, S3_REGION

Requisitos de seguridad/infra:
- TLS con certificados gestionados (Let’s Encrypt / ACM)
- Backups automatizados con retención y pruebas periódicas
- Registros de auditoría centralizados y protegidos

## 26. Guías de implementación rápida

Objetivo: permitir a un desarrollador levantar el entorno y ejecutar flujos clave en <1h en local.

Pasos mínimos - Local (Docker Compose):
1. Clonar repo
2. Copiar `.env.example` → `.env` y ajustar credenciales (usar contraseñas locales seguras)
3. Levantar servicios:

```pwsh
docker compose up -d --build
``` 

4. Ejecutar migraciones y seeds:

```pwsh
docker compose exec app php artisan migrate --seed
``` 

5. Instalar dependencias frontend y compilar (si aplica):

```pwsh
docker compose exec app npm ci
docker compose exec app npm run build
``` 

6. Levantar worker (opcional):

```pwsh
docker compose exec app php artisan queue:work --sleep=3 --tries=3
``` 

7. Ejecutar tests rápidos:

```pwsh
docker compose exec app ./vendor/bin/phpunit --testsuite=Unit
``` 

Atajos y recomendaciones:
- Crear alias/Makefile o scripts `make dev` para automatizar.
- Para ejecutar pruebas de integración que usan SQL Server, usar imagen SQL Server en CI o Testcontainers.
- Para debugging de SPs, usar `sqlcmd` dentro del contenedor SQL o un cliente GUI (Azure Data Studio).

Checklist post-setup (verificar):
- [ ] Conexión DB ok
- [ ] Migraciones aplicadas
- [ ] Seeds cargados (empleados mínimos)
- [ ] Worker en ejecución
- [ ] Endpoint /health o /status devuelve OK

Guía rápida de despliegue a QA/Prod (resumen):
- Crear release tag
- Ejecutar pipeline CI que construye imagen y ejecuta tests
- Push imagen a registry privado
- Desplegar a QA, ejecutar smoke tests
- Si OK, promover imagen a Prod con estrategia rolling/blue-green

---

## 27. Flujos, validaciones y racional (por módulo)

Nota: los flujos detallados por módulo se documentan en 27.1..27.8 más abajo; aquí añadimos contratos operativos comunes, esquemas JSON y códigos de error estándar para uniformidad.

27.0 Contratos y convenciones transversales

Headers y seguridad
- Autorización: Authorization: Bearer <token>
- Idempotency: Idempotency-Key: <uuid-v4> (aplicable a endpoints mutativos como POST /api/v1/planilla/generar)
- Trace: X-Trace-Id: <uuid> (propagar para tracing)
- Rate limit: X-RateLimit-Limit, X-RateLimit-Remaining, X-RateLimit-Reset (respuestas)

Formato de respuesta estándar
- SuccessResponse:
{
  "success": true,
  "code": 0,
  "data": { ... },
  "message": "Descripción breve"
}

- ErrorResponse:
{
  "success": false,
  "code": -99,
  "error": "Mensaje legible para usuario",
  "details": { "field": "error" }
}

Códigos de error comunes (transversales)
- -1: Parámetro inválido
- -2: Recurso no encontrado
- -3: Conflicto / Proceso en curso (usar cuando applock impide operación)
- -4: Permiso denegado
- -5: Límite de tasa excedido
- -99: Error interno

Validaciones y respuestas API (ejemplos)
- POST /api/v1/contratos (create contract):
  - Request: contrato data (IDEmpleado, FechaInicio, FechaFin?, HaberBasico, IDDepartamento, IDCargo)
  - Validaciones: no solapes (`fn_ValidarSolapeContrato`), HaberBasico >= SMN, fechas coherentes
  - Response 201: success true + contrato id
  - Response 400: code -1 con campo 'fechas' o 'haber_basico' en details

- POST /api/v1/anticipos (create anticipo):
  - Validaciones: monto <= HaberBasico*0.5, no anticipo pendiente por contrato
  - Response 201 / 400 con code -1

Operaciones de long-running (planilla)
- POST /api/v1/planilla/generar
  - Respuesta inmediata 202 Accepted con objeto de tracking: { planilla_id, status:'Iniciado', links: { status_url } }
  - Cliente puede poll o usar webhook/notifications para recibir 'Completado'/'Error'

Idempotencia
- Para endpoints mutativos largos, exigir Idempotency-Key header; la app debe persistir la clave y devolver el mismo resultado si reenvían.

Observabilidad en flujos
- Cada operación crítica debe emitir eventos a un topic (ej. planilla.started, planilla.completed, anticipo.created) con payload mínimo { entity_id, user_id, timestamp, trace_id }.

Auditoría y trazabilidad
- Todas las mutaciones críticas deben crear entradas JSON en `AuditLog` con before/after, usuario, trace_id, ip y comentario opcional.

---

27.1 Módulo Empleados

Flujo de alta: [Formulario Alta] → Validaciones CI/Email → Generación Kardex → Asignación Usuario → Notificación RRHH.

Trigger de auditoría (resumen): `trg_Empleados_Audit` registra operaciones en `AuditLog`.

27.2 Módulo Contratos

Wizard: alta → validación solapes (función `fn_ValidarSolapeContrato`) → confirmación → generación documento. Estados: Borrador→Activo→Renovado→Finalizado.

27.3 Módulo Subsidios

Tipos: Familiar, Antigüedad, Especial. Trigger `TRG_Subsidios_A_GestionSalarios` propaga a `GestionSalarios`.

27.4 Módulo Anticipos

Flujo: solicitud → validar tope 50% → aprobar/rechazar → programar descuento. Trigger INSTEAD OF garantiza invariantes.

27.5 Módulo Planilla Mensual

Flujo crítico: seleccionar periodo → validar → adquirir lock → snapshot contratos → calcular por contrato → preview → confirmar → auditar. Idempotencia y applock obligatorios.

27.6 Módulo Vacaciones

Flujo: solicitud → validar saldo → notificar jefe → aprobar → descontar saldo → auditar. Función `fn_CalcularSaldoVacaciones` para saldos.

27.7 Criterios Transversales

Requeridos, unicidad, referencialidad, fechas coherentes, montos ≥0, rowversion para concurrencia; accesibilidad AA.

27.8 Criterios de Aceptación (por módulos clave)

Planilla (MVP):
- Dado un periodo válido, al invocar POST /api/v1/planilla/generar con permiso y idempotency_key distinto, entonces se genera una entrada en LogPlanilla con EstadoProceso='Iniciado' y al completar 'Completado'.
- Performance: generación completa < 30s para 335 empleados (P95). 
- Exactitud: para 10 casos de prueba definidos, los resultados numéricos coinciden con calculadora de referencia (tolerancia 0.1%).

Contratos:
- Validación de no solapes: al crear un contrato que solapa con contrato activo, la API debe devolver 400 con mensaje claro y no insertar.

Anticipos:
- Trigger valida tope 50% y evita inserciones inválidas; pruebas automatizadas deben cubrir monto límite y contrato inactivo.

Empleados:
- ABM completo; al subir CSV con 335 registros válidos la operación debe completar sin errores y persistir documentos referenciados.

Vacaciones:
- Al solicitar vacaciones, el saldo se valida y la aprobación actualiza el saldo y genera notificación al empleado y al jefe.

---

Edge cases y consideraciones operativas

- Reloj del sistema: usar timezone config y validar fechas con DATEFROMPARTS para evitar discrepancias.
- Reintentos: implementar backoff para jobs que fallen por deadlock y cap de reintentos para no saturar DB.
- Fallback para planilla: si el procedimiento falla, exponer un mecanismo de preview que reprocese sin afectar producción.
- Seguridad: limitar la cantidad de intentos de generación por usuario y periodos concurrentes.

---

Calidad gates (recomendado antes de merge a main)

- Lint/Format: PHP (Pint), JS (ESLint, Prettier)
- Unit tests >= 70% en servicios modificados
- Integration tests para SPs corriendo en CI con SQL Server container
- Smoke tests en staging post-deploy

---

Notas:
- He unificado y eliminado duplicados visibles (especialmente secciones 6.* y 19.* y la doble aparición del apartado 27).
- Mantengo los bloques SQL y ejemplos esenciales en 6.* y 19.*; otras secciones están resumidas para mantener el documento legible; puedo expandir algún bloque completo si lo deseas.

## 28. Criterios generales de validación (catálogo)

Catálogo de reglas reutilizables y mensajes de error para la capa API / UI / DB:
- Requerido: campo no nulo, mensaje "El campo X es obligatorio." (HTTP 400, code -1)
- Unicidad: índice único + validación previa; mensaje "Ya existe un registro con X." (409/-3)
- Formato: regex/format validators (CI, email, teléfono) con ejemplos válidos/ínválidos
- Fechas: FechaInicio <= FechaFin; FechaNacimiento razonable (edad < 100)
- Montos: >= 0; límites configurable por parámetro de sistema
- Límites de texto: max_length y sanitize (XSS safe)
- Concurrencia: rowversion/If-Match header para ediciones; mensaje ‘Recurso modificado por otro usuario’ (409)
- Transaccionalidad: operaciones multi-step (contrato+documento) deben ser atómicas; comprobar rollback en fallo

Implementación sugerida:
- Centralizar reglas en `app/Rules` y `FormRequest` en Laravel; mantener mensajes en `lang/es/*.php`.

## 29. Decisiones de diseño (racional)

Resumen de decisiones clave y por qué:
- SPs para cálculos set‑based: performance y atomicidad para planilla; evitar lógica financiera pesada en PHP.
- Triggers para invariantes críticas (anticipo ≤50%): garantizan reglas aunque la app falle.
- Rowversion + applock: combinación optimista/pesimista para UX y seguridad en cierres criticos.
- Documentos en object storage: reducir tamaño DB y mejorar escalabilidad.
- RBAC en aplicación: flexibilidad de permisos por roles vs. permisos DB rígidos.

## 30. Estrategia Responsive (móvil primero + escritorio)

Principios:
- Mobile-first: diseñar componentes y flujos para móvil y adaptar a escritorio
- Priorizar contenido: en móvil mostrar acciones primarias y resumen; detalles en expand/collapse
- Performance: lazy-loading de assets y listados con infinite scroll o paginación server-side
- Accesibilidad AA: contraste, labels, keyboard navigation, aria-attributes

## 31. Estándares de estilos con Tailwind

Convenciones:
- Tokens de color, espaciado y tipografía en `tailwind.config.js`
- Componentes atómicos y reutilizables en `resources/views/components`
- Clases utilitarias preferidas; evitar CSS inline ni estilos únicos no tokenizados
- Formatos de nombres: componentes PascalCase, utilidades camelCase en JS

## 32. Guía responsive por página

Patrones por página:
- Listados: cards móvil / tabla md+; filtros en drawer móvil, sidebar md+
- Wizards: stepper lineal en desktop, vertical en móvil
- Detalle: tabs + sticky actions en desktop; accordions en móvil

## 33. Contenerización (Docker)

Arquitectura de imágenes:
- Multi-stage Dockerfile: builder (node) → base (php-fpm) → final (app)
- Servicios en `docker-compose.yml`: app, nginx, sqlserver, redis, mailhog
- Buenas prácticas: usuarios no-root, multi-arch build, tamaño de imagen optimizado

## 34. Pipeline CI/CD (contenedores)

Pipelines recomendados:
- PR: lint, unit tests, build frontend, static analysis
- Main: build image, run integration tests en environment ephemeral (SQL Server container), push image, deploy to staging
- Release: deploy to prod con strategy (k8s/compose) y smoke tests

## 35. Prácticas de calidad para responsive

- Visual regression con Playwright/Chromatic por breakpoints
- Automatizar axe-core accessibility checks en E2E
- Tests visuales en CI para componentes críticos (forms, tables, wizards)

## 36. Patrones UI/Tailwind por flujo (ejemplos)

- Formulario validable: label + helper + inline error + hint
- Data table: server-side pagination + column toggles + accessible headers
- Wizard: step validation, back/next, preview antes de commit

## 37. Roadmap responsive + Docker

Sprints propuestos:
- Sprint UI base: tokens, layout, header/footer, auth pages
- Sprint Lists/Wizards: empleados, contratos
- Sprint Assets/Perf: build caching, CDN integration
- Sprint Dockerize Prod: multi-service, healthchecks, secrets

## 38. tailwind.config.js (propuesto)

Descripción: plantilla lista para usar con tokens de marca YPFB (azul/blanco/rojo), plugins recomendados y mejores prácticas para producción (purge/content, JIT comportamental por defecto en Tailwind v3+).

38.1 Configuración sugerida (copiar a `tailwind.config.js`)

```js
/** tailwind.config.js - propuesta para YPFB UI */
module.exports = {
  darkMode: 'class',
  content: [
    './resources/**/*.{blade.php,js,ts,vue}',
    './assets/**/*.{js,ts,css,scss}',
    './resources/views/**/*.blade.php'
  ],
  theme: {
    container: { center: true, padding: '1rem' },
    screens: { sm: '640px', md: '768px', lg: '1024px', xl: '1280px', '2xl': '1536px' },
    extend: {
      colors: {
        ypfb: {
          blue: '#0A3E8F',    // primary
          red: '#E31B23',     // accent / danger
          white: '#FFFFFF'
        },
        neutral: {
          900: '#111827',
          700: '#374151',
          500: '#6B7280',
          300: '#D1D5DB',
          100: '#F3F4F6'
        }
      },
      fontFamily: {
        sans: ["Roboto", "system-ui", "-apple-system", "Segoe UI", "Helvetica", "Arial", "sans-serif"],
        serif: ["Merriweather", "Georgia", "serif"]
      },
      spacing: {
        72: '18rem'
      },
      borderRadius: {
        lg: '0.75rem'
      }
    }
  },
  plugins: [
    require('@tailwindcss/forms'),
    require('@tailwindcss/typography'),
    require('@tailwindcss/aspect-ratio'),
    require('@tailwindcss/line-clamp')
  ],
  safelist: [
    'text-ypfb-blue',
    'bg-ypfb-blue',
    'text-ypfb-red',
    'bg-ypfb-red',
    {
      pattern: /bg-(ypfb|neutral)-(100|300|500|700|900)/
    }
  ]
};
```

38.2 Notas y buenas prácticas
- Content / Purge: incluir todas las rutas que generan clases dinámicamente (templates Blade, componentes Vue/React, ficheros JS/TS). Esto asegura que la build de producción elimine clases no usadas.
- JIT: Tailwind v3+ incorpora JIT por defecto; evita concatenar clases dinámicas en strings que no puedan analizarse (usar safelist o classnames helpers).
- Tokens: usar `bg-ypfb-blue text-white` o `bg-ypfb-red` en vez de hex en componentes. Centralizar utilidades con `@apply` en archivos SCSS/CSS de componentes para mantener consistencia.

38.3 Ejemplo de componentes reusables (styles)

`resources/css/components/buttons.css`

```css
@tailwind base;
@tailwind components;
@tailwind utilities;

.btn {
  @apply inline-flex items-center justify-center rounded-lg px-4 py-2 font-medium;
}
.btn-primary { @apply bg-ypfb-blue text-white hover:bg-ypfb-blue/90 focus:outline-none focus:ring-2 focus:ring-ypfb-blue/40 }
.btn-danger  { @apply bg-ypfb-red text-white hover:bg-ypfb-red/90 }
```

38.4 Uso de variables CSS para theming dinámico (opcional)

En `app.css` o `:root` puedes exportar variables para permitir theming en runtime:

```css
:root {
  --color-ypfb-blue: #0A3E8F;
  --color-ypfb-red:  #E31B23;
}
.theme-ypfb { --tw-bg-opacity: 1; background-color: var(--color-ypfb-blue); }
```

38.5 Integración con build (npm scripts, PostCSS)
- Dependencias recomendadas (package.json):
  - tailwindcss, postcss, autoprefixer, @tailwindcss/forms, @tailwindcss/typography, @tailwindcss/aspect-ratio, @tailwindcss/line-clamp

Ejemplos de scripts en `package.json`:

```json
"scripts": {
  "dev": "vite",
  "build": "vite build",
  "preview": "vite preview"
}
```

38.6 Comandos útiles (local/dev)

```pwsh
# Instalar dependencias
npm ci
# Levantar servidor dev con watch (vite)
npm run dev
# Generar build para producción
npm run build
```

38.7 Recomendaciones de QA y rendimiento
- Visual regression: agregar snapshot tests (Playwright/Chromatic) para botones, tablas y formularios principales.
- Size budgets: monitorear CSS final; eliminar clases no usadas si el bundle crece demasiado.
- Accessibility: correr `axe` en E2E y tests unitarios para componentes form/tabla.

38.8 Próximos pasos (opcional)
- Puedo crear el archivo `tailwind.config.js` real en el repo y actualizar `package.json` con los scripts recomendados.
- Puedo extraer los tokens (colores/tipografía) a `Docs/design/tokens.md` y generar `resources/css/components/buttons.css` automático.

## 39. nginx.conf (optimizado estáticos)

- Servir assets estáticos con cache headers largos, gzip/ brotli
- Health check endpoint proxy to php-fpm
- Security headers: X-Frame-Options, X-Content-Type-Options, Referrer-Policy

Ejemplo mínimo ya incluido en el repo (sección 39).

## 40. Plantillas .env (dev/qa/prod)

- Mantener `.env.example` con variables obligatorias
- No incluir secretos en repo; usar vault/secret manager en prod
- Variables críticas: DB_*, REDIS_*, S3_*, MAIL_*, APP_ENV, APP_DEBUG, TRUSTED_PROXIES

## 41. Mapeo Eloquent y Repositorios (skeletons)

- Eloquent models con $casts, $dates, relaciones y scopes para filtros comunes
- Repositories: interfaces + implementación para desacoplar la capa DB (use dependency injection)
- Services: orquestación de transacciones y llamadas a SP

## 42. Rutas Laravel (web & API) + middleware

- Web: auth, verified, roles middleware; namespaced controllers
- API: versionado `/api/v1/`, throttle, auth:sanctum, scope permissions
- Endpoints críticos con gate checks y policy autorization

## 43. Policies y permisos (ejemplo)

- Policy PlanillaPolicy { generar, pagar, ver } map a permissions planilla.generar|planilla.pagar
- Seeder inicial de roles y permisos en `RolesPermisosSeeder`

## 44. Catálogo de validaciones (reglas y mensajes)

Listado de reglas (implementarlas en `app/Rules`):
- CIValida: regex bolivian CI
- FechaCoherente: FechaInicio <= FechaFin
- HaberBasicoMinimo: >= SMN (configurable)
- NoSolapeContrato: invoca `fn_ValidarSolapeContrato`
- AnticipoTope: monto <= HaberBasico*0.5

## 45. Máquinas de estado (transiciones)

Estado Planilla: Borrador → Generada → Pagada → Anulada
Estado Vacaciones: Solicitada → Aprobada → Tomada → Cancelada
Estado Anticipo: Pendiente → Aprobado → Pagado → Descontado → Cerrado

## 46. Diagramas (ASCII)

Planilla: [UI selecciona periodo] -> [API valida] -> [LogPlanilla Inicia] -> [sp_GenerarPlanillaMensual (applock)] -> [GestionSalarios upsert] -> [LogPlanilla Completa]

Anticipo: [Alta UI] -> [API] -> [Trigger trg_Validar_Anticipo] -> [Anticipos insert / RAISERROR]

## 47. QA: checklists por flujo

- Contratos: validación solapes, fechas, documentos generados
- Anticipos: trigger tope, no duplicados pendientes
- Planilla: idempotencia, auditoría, performance
- Vacaciones: saldo, notificaciones, actualización de saldos

## 48. Seeds y fixtures (Laravel)

- Estructura de seeders y factories: RolesPermisosSeeder, DepartamentosSeeder, EmpleadoFactory, ContratoFactory, SubsidioFactory, AnticipoFactory
- Datos mínimos: 335 empleados, 300 contratos, 100 subsidios, 50 anticipos para pruebas

## 49. SQL: objetos clave (plantillas)

- Triggers: trg_Validar_Anticipo, TRG_Subsidios_A_GestionSalarios, trg_Empleados_Audit
- Funciones: fn_CalcularSalarioTotal, fn_TotalBeneficiosPeriodo, fn_ValidarSolapeContrato
- SPs: sp_CalcularSalarioMensual, sp_GenerarPlanillaMensual, sp_ReporteEvaluacionAnual

## 50. Scheduler y colas

- Scheduler jobs: backups, reportes programados, limpieza de jobs, notificaciones
- Queues: redis queues con prioridad; workers escalables; retry policy y DLQ (dead letter queue)

## 51. DR/BCP (runbook)

- RTO ≤ 1h, RPO ≤ 1h definidos por negocio
- Procedimiento de failover: restore full + diff + logs en DR, ejecutar smoke tests
- Probar recovery quarterly; mantén runbooks con pasos y responsables

## 52. Monitoreo & Alertas

- Métricas: P95 latency, error rate, job failures, deadlocks, CPU/Disk
- Alertas en herramientas (PagerDuty/Teams) con playbook de respuesta
- Dashboards por servicio: App, DB, Worker, infra

## 53. Retención y privacidad de datos

- PII: enmascaramiento en staging y logs
- Retención: AuditLog 5 años, planillas 10 años
- Accesos: log de descargas con propósito y usuario; políticas de export

## 54. Asuntos abiertos / Supuestos

- Integración ERP (fase 2) queda pendiente con interfaz y contract spec
- Definición definitiva de 3 tipos de subsidios por RRHH legal
- Decisión sobre HA SQL Server (Always On) depende de presupuesto

## 55. Glosario

- ABM: Alta, Baja, Modificación
- RPO/RTO: Recovery Point/Time Objective
- RBAC: Role-Based Access Control
- Idempotencia: operación repetida sin efecto adicional
- Applock: bloqueo de aplicación en SQL Server (sp_getapplock)
- Rowversion: campo para concurrencia optimista

---

## 56. Pasos a seguir (Acciones inmediatas y roadmap corto)

Objetivo: transformar esta documentación en artefactos ejecutables y prioridades claras para las próximas 4–12 semanas.

56.1 Acciones inmediatas (48–72 horas)
- 56.1.1 Marcar los SQL generados como borrador y añadir README: mover `Docs/sql/*.sql` a `Docs/sql/draft/` o prefijar `DRAFT_` y crear `Docs/sql/README.md` que explique: "plantillas, no ejecutar en producción sin revisión DBA". Responsable: Dev/DBA. Criterio de aceptación: archivos trasladados y README creado.
- 56.1.2 Añadir `package.json` y scripts mínimos para Tailwind/PostCSS y Vite (dev/build). Responsable: Frontend/Dev. Criterio de aceptación: `npm ci` y `npm run build` generan CSS sin errores en entorno local.
- 56.1.3 Añadir un pequeño README en `Docs/` indicando el owner del proyecto y el canal de comunicación (Slack/Teams) y el próximo checkpoint. Responsable: PO. Criterio de aceptación: README con owner y fecha del próximo checkpoint.

56.2 Prioridad a 2 semanas (sprint siguiente)
- 56.2.1 Convertir plantillas SQL críticas en migrations o scripts idempotentes revisados por DBA (LogPlanilla, sp_GenerarPlanillaMensual, sp_CalcularSalarioMensual). Responsable: Dev + DBA. Criterio: scripts ejecutables en entorno staging sin errores y con tests básicos.
- 56.2.2 Añadir paso de compilación de CSS en CI y job de lint (Pint, ESLint) en pipeline. Responsable: DevOps. Criterio: pipeline de PR ejecuta lint y build.
- 56.2.3 Crear seeds y pruebas de integración para `sp_CalcularSalarioMensual` usando SQL Server container en CI (Testcontainers/docker-compose). Responsable: QA/Dev. Criterio: integración verde en CI para 3 casos base.

56.3 Roadmap 1–3 meses
- 56.3.1 Implementar integración CI con SQL Server ephemeral para tests de SPs e integración. Responsable: DevOps. Criterio: PRs con tests que ejecutan SPs pasan en CI.
- 56.3.2 Sistema de visual regression y accessibility checks (Playwright + axe). Responsable: QA/Frontend. Criterio: snapshots y accessibility guard implementados para componentes críticos.
- 56.3.3 Finalizar tokens de diseño y obtener logo SVG oficial; publicar `Docs/design/tokens.md` como fuente única. Responsable: UX/PO. Criterio: tokens importados en `tailwind.config.js` y `resources/css`.

56.4 Criterios de aceptación generales (DoD aplicable a cada tarea)
- Implementación con tests automatizados (unitarios o integración según alcance).
- Documentación asociada actualizada (`Docs/`), incluyendo instrucciones de despliegue y rollback.
- Revisiones por pares y aprobación de DBA/PO donde aplique (especialmente para SQL y backups).
- Pipelines verdes (CI) para PRs que cambien backend, DB o assets críticos.

56.5 Comandos recomendados (PowerShell — local / dev)
Copiar estas instrucciones al `README-dev.md` o a scripts de repo.

```pwsh
# Instalar dependencias frontend
npm ci

# Levantar entorno local (docker-compose)
docker compose up -d --build

# Compilar assets (dev)
npm run dev

# Compilar assets (producción)
npm run build

# Ejecutar migraciones y seeds desde contenedor app
docker compose exec app php artisan migrate --seed

# Ejecutar tests unitarios rápidos
docker compose exec app ./vendor/bin/phpunit --testsuite=Unit
```

56.6 Decisión sobre los SQL generados (opciones y recomendación)
- Opción A (recomendada ahora): mover a `Docs/sql/draft/` y crear `Docs/sql/README.md` indicando "Revisar con DBA; no ejecutar en producción".
- Opción B: convertir inmediatamente a migrations revisadas por DBA y añadir tests de integración (mayor esfuerzo, alta seguridad).
- Opción C: eliminar si no son útiles — perderíamos historial y plantillas.

56.7 Siguientes pasos propuestos (elige una)
1) Mover archivos SQL a `Docs/sql/draft/` y crear `Docs/sql/README.md`. (rápido, seguro)
2) Crear `package.json` con dependencias Tailwind y scripts + añadir job CI para build CSS. (útil para evitar linter errors en `buttons.css`)
3) Convertir 2 SPs críticos a migrations y añadir 3 tests de integración in CI. (prioridad alta para producción)

56.8 Estado de requisitos tras esta edición
- Revisión del documento: Completa. Se añadió esta sección con pasos accionables. 
- Próxima decisión requerida del dueño del repositorio: elegir la opción para los SQL y confirmar owner de Frontend/DevOps.

---
````