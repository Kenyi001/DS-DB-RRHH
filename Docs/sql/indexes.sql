-- Índices recomendados (extracto)
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
