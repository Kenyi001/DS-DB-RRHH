# Ticket: Implementar Módulo Subsidios y Anticipos

- **ID del Ticket:** `fase3-0009`
- **Fase:** `Sprint 3: Subsidios y Anticipos`
- **Estado:** `Abierto`
- **Prioridad:** `Alta`

---

## Descripción

Desarrollar el sistema completo de gestión de subsidios y anticipos incluyendo tipos de subsidios, validaciones de negocio, workflows de aprobación y propagación automática a cálculos de planilla.

---

## Criterios de Aceptación

- [ ] CRUD completo para subsidios con tipos: Familiar, Antigüedad, Especial
- [ ] Sistema de anticipos con validación automática de tope 50%
- [ ] Workflow de aprobación para anticipos (Jefe de Área)
- [ ] Propagación automática de subsidios a GestionSalarios
- [ ] UI intuitiva para gestión de subsidios y solicitud de anticipos
- [ ] Reportes de subsidios activos y histórico de anticipos
- [ ] Validaciones multi-capa (UI, API, DB via triggers)
- [ ] Tests automatizados para reglas de negocio críticas

---

## Detalles Técnicos y Notas de Implementación

### Modelos y Relaciones
```php
// Subsidio Model
class Subsidio extends Model
{
    protected $fillable = ['id_contrato', 'id_tipo_subsidio', 'monto', 'fecha_inicio', 'fecha_fin', 'estado'];
    
    public function contrato() { return $this->belongsTo(Contrato::class, 'id_contrato'); }
    public function tipoSubsidio() { return $this->belongsTo(TipoSubsidio::class, 'id_tipo_subsidio'); }
}

// Anticipo Model  
class Anticipo extends Model
{
    protected $fillable = ['id_contrato', 'monto_anticipo', 'fecha_solicitud', 'estado_anticipo', 'mes_descuento'];
    
    public function contrato() { return $this->belongsTo(Contrato::class, 'id_contrato'); }
}
```

### Tipos de Subsidios
- **Familiar**: Monto fijo por carga familiar
- **Antigüedad**: Porcentaje sobre haber básico según años
- **Especial**: Montos variables por situaciones específicas

### Validaciones de Anticipos
- Monto máximo: 50% del haber básico vigente
- Solo un anticipo pendiente por contrato
- Contrato debe estar activo
- Empleado debe tener al menos 3 meses de antigüedad

### API Endpoints
```http
GET    /api/v1/subsidios?empleado_id=123    # Lista subsidios empleado
POST   /api/v1/subsidios                    # Crear subsidio
PUT    /api/v1/subsidios/{id}               # Actualizar subsidio
DELETE /api/v1/subsidios/{id}               # Eliminar subsidio

GET    /api/v1/anticipos?empleado_id=123    # Lista anticipos empleado  
POST   /api/v1/anticipos                    # Solicitar anticipo
PUT    /api/v1/anticipos/{id}/aprobar       # Aprobar/rechazar anticipo
```

### Workflow de Aprobación Anticipos
1. Empleado solicita anticipo (estado: Pendiente)
2. Notificación automática a jefe de área
3. Jefe aprueba/rechaza con comentarios
4. Si aprobado: estado Aprobado, programar descuento
5. Descuento automático en próxima planilla

---

## Especificaciones Relacionadas

- `/Docs/specs/api-contracts.md` - Endpoints y validaciones API
- `/Docs/specs/sql-objects.md` - Triggers de validación
- `/Docs/specs/ux-design.md` - UI patterns para workflows

---

## Dependencias

- **Bloquea:** `fase4-0005` (Planilla MVP - requiere subsidios)
- **Bloqueado por:** `fase2-0004` (API Contratos), `fase4-0006` (Triggers SQL)

---

## Sub-Tareas

- [ ] Crear modelos Eloquent para Subsidios, TiposSubsidio, Anticipos
- [ ] Implementar SubsidioService con lógica de propagación
- [ ] Desarrollar AnticipoService con validaciones de tope
- [ ] Crear controllers REST para ambos módulos
- [ ] Implementar FormRequests con validaciones robustas
- [ ] Desarrollar UI para gestión de subsidios (CRUD)
- [ ] Crear interfaz de solicitud de anticipos
- [ ] Implementar workflow de aprobación con notificaciones
- [ ] Añadir tests unitarios para servicios y validaciones
- [ ] Crear tests de integración para triggers
- [ ] Implementar reportes básicos de subsidios y anticipos

---

## Comentarios y Discusión

**Owner:** [Placeholder - Backend + Frontend Developer]
**Estimación:** 24-28 horas
**Sprint:** Sprint 3 (Semanas 7-8)

**Nota importante**: Coordinar con RRHH para definir montos exactos de subsidios familiares y políticas de antigüedad antes de implementar lógica de cálculo.