# CLAUDE.md — Instrucciones para Claude (resumen para agentes)

Propósito
- Proveer a Claude instrucciones claras para analizar, editar y ayudar con el repositorio.
- Mantener una única fuente de verdad: referenciar `Docs/projectChapter.md` y archivos en `Docs/`.

Guía rápida de inicio
1. Leer `prompts/instructions.md` y `Docs/projectChapter.md` antes de cualquier cambio.
2. Confirmar comprensión en 1–2 frases y proponer un plan corto (3–6 pasos).
3. Antes de editar, listar archivos a cambiar y mostrar ejemplo de cambio (fragmento o ticket) y esperar confirmación.
4. No copiar el documento entero de `Docs/projectChapter.md` a otro lugar; siempre referenciar.

Comandos y flujo recomendado
- /init: (manual) usar para crear este archivo desde un asistente si no existe.
- Lectura de archivos: pide rutas exactas (p. ej. `Docs/projectChapter.md`) y solicita confirmación antes de lecturas masivas.
- Edición: devolver un resumen de cambios y aplicar patch solo tras confirmación del autor.
- Git: antes de `push`, mostrar el diff y pedir confirmación; preferir abrir PR en vez de push directo a `main`.

Buenas prácticas para prompts
- Sé específico: rutas exactas, líneas o fragmentos relevantes.
- Dale contexto: objetivo del cambio, prioridad y criterios de aceptación.
- Divide tareas grandes en pasos pequeños y entregables (specs, plan, tickets).
- Dejar placeholders para owners/fechas y pedir asignación antes de merges a `main`.

Plantilla de prompt (usar al comenzar)
"Lee `prompts/instructions.md` y `Docs/projectChapter.md`. Confirma en 3–6 líneas tu entendimiento y muestra un plan en 3–6 pasos. Luego lista los archivos que crearás y espera confirmación antes de modificarlos. Escribe todo en español."

Checklist de seguridad
- No ejecutar SQL en producción ni push de secretos.
- Verificar que `.env` no se suba a SCM.
- Marcar scripts SQL generados como `DRAFT` si no están revisados por DBA.

Última actualización: 2025-08-31
Owner: (placeholder)
