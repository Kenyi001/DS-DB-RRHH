# Ticket: Implementar Datos Semilla y Seeders

- **ID del Ticket:** `fase0-0002`
- **Fase:** `Sprint 0: Fundación`
- **Estado:** `Abierto`
- **Prioridad:** `Alta`

---

## Descripción

Crear seeders completos de Laravel para poblar la base de datos con datos maestros, empleados de prueba y datos relacionales necesarios para desarrollo y testing. Incluye 335 empleados realistas y datos coherentes para todas las entidades.

---

## Criterios de Aceptación

- [ ] Seeder de roles y permisos (6 roles, 20+ permisos) ejecuta sin errores
- [ ] Seeder de departamentos y cargos poblado con datos YPFB
- [ ] 335 empleados con datos realistas (CI, nombres, fechas coherentes)
- [ ] 300+ contratos vinculados a empleados con fechas no superpuestas
- [ ] 100 subsidios distribuidos por tipos y empleados
- [ ] 50 anticipos con montos ≤ 50% haber básico
- [ ] Comando `php artisan db:seed` completa en < 2 minutos
- [ ] Datos de prueba validados por reglas de negocio

---

## Detalles Técnicos y Notas de Implementación

### Seeders Principales Requeridos
```php
// database/seeders/DatabaseSeeder.php
public function run(): void
{
    $this->call([
        RolesPermisosSeeder::class,
        DepartamentosSeeder::class,
        CargosSeeder::class,
        EmpleadosSeeder::class,      // 335 empleados
        ContratosSeeder::class,      // 300+ contratos
        TiposSubsidioSeeder::class,
        SubsidiosSeeder::class,      // 100 subsidios
        AnticiposSeeder::class,      // 50 anticipos
    ]);
}
```

### Factories para Datos Realistas
- EmpleadoFactory: CIs bolivianos válidos, nombres bolivianos, fechas coherentes
- ContratoFactory: Rangos salariales realistas, fechas no superpuestas
- SubsidioFactory: Montos según tipo y políticas
- AnticipoFactory: Respeta tope 50% haber básico

### Validaciones en Seeders
- CI únicos y formato boliviano válido
- Emails únicos en dominio @ypfb.gov.bo
- Contratos sin solapes por empleado
- Anticipos que respeten trigger de validación

---

## Especificaciones Relacionadas

- `/Docs/specs/db-model.md` - Modelo de datos y relaciones
- `/Docs/specs/security.md` - Roles y permisos del sistema

---

## Dependencias

- **Bloquea:** `fase1-0003` (API Empleados), `fase2-0004` (API Contratos)
- **Bloqueado por:** `fase0-0001` (Infraestructura de desarrollo)

---

## Sub-Tareas

- [ ] Crear RolesPermisosSeeder con 6 roles y permisos granulares
- [ ] Implementar DepartamentosSeeder y CargosSeeder con datos YPFB
- [ ] Desarrollar EmpleadoFactory con datos bolivianos realistas
- [ ] Crear ContratoFactory que respete validaciones de solapes
- [ ] Implementar SubsidiosSeeder con tipos y montos coherentes
- [ ] Desarrollar AnticipoFactory que respete trigger de 50%
- [ ] Añadir validaciones y tests para cada seeder
- [ ] Optimizar performance de seeding para < 2 minutos

---

## Comentarios y Discusión

**Owner:** [Placeholder - Backend Developer]
**Estimación:** 12-16 horas
**Sprint:** Sprint 0 (Semanas 1-2)

**Nota**: Coordinar con RRHH para obtener lista de departamentos y cargos oficiales de YPFB-Andina.