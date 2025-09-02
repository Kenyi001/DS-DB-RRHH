# Scripts SQL - DRAFT Templates

## ⚠️ IMPORTANTE - PLANTILLAS DRAFT

**ESTOS SON SCRIPTS DE PLANTILLA NO EJECUTAR EN PRODUCCIÓN SIN REVISIÓN DBA**

Los archivos en `draft/` son plantillas generadas automáticamente basadas en las especificaciones del projectChapter.md. Requieren:

1. **Revisión obligatoria por DBA** antes de cualquier ejecución
2. **Testing en ambiente staging** con datos reales
3. **Optimización de performance** según carga específica
4. **Validación de permisos** y security implications

## Archivos Incluidos

### Stored Procedures
- `sp_GenerarPlanillaMensual.sql` - Proceso principal generación planilla
- `sp_CalcularSalarioMensual.sql` - Cálculo individual por contrato

### Triggers
- `trg_Validar_Anticipo.sql` - Validación tope 50% anticipos
- `trg_Empleados_Audit.sql` - Auditoría cambios empleados

### Tablas de Control
- `LogPlanilla.sql` - Tracking de procesos planilla
- `AuditLog.sql` - Auditoría general del sistema

### Funciones
- `fn_ValidarSolapeContrato.sql` - Validación contratos superpuestos

## Próximos Pasos

1. Revisar cada script con DBA senior
2. Adaptar a estándares específicos de YPFB
3. Convertir a migrations de Laravel probadas
4. Implementar tests de integración en CI

## Owner
**DBA Lead** + **Senior Backend Developer**  
**Última actualización**: 2025-08-31