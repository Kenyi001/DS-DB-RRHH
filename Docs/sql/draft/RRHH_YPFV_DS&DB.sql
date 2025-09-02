/* =========================
   0) Crear y usar la BD
   ========================= */
IF DB_ID(N'YPFB_RRHH') IS NULL
    CREATE DATABASE YPFB_RRHH;
GO
USE YPFB_RRHH;
GO

/* =========================
   1) Catálogos base
   ========================= */

-- Tipos de Afiliación
CREATE TABLE dbo.TiposAfiliacion (
    TipoAfiliacionID INT IDENTITY(1,1) PRIMARY KEY,
    NombreTipoAfiliacion NVARCHAR(50) NOT NULL,
    Descripcion NVARCHAR(255),
    Estado BIT DEFAULT 1
);
GO

-- Tipos de Subsidio
CREATE TABLE dbo.TiposSubsidio (
    TipoSubsidioID INT IDENTITY(1,1) PRIMARY KEY,
    NombreTipoSubsidio NVARCHAR(50) NOT NULL,
    Descripcion NVARCHAR(255),
    MontoBase DECIMAL(10,2),
    Estado BIT DEFAULT 1
);
GO

-- Tipos de Documento
CREATE TABLE dbo.TiposDocumento (
    TipoDocumentoID INT IDENTITY(1,1) PRIMARY KEY,
    NombreTipoDocumento NVARCHAR(50) NOT NULL,
    Descripcion NVARCHAR(255),
    Estado BIT DEFAULT 1
);
GO

-- Categorías
CREATE TABLE dbo.Categorias (
    IDCategoria INT IDENTITY(1,1) PRIMARY KEY,
    NombreCategoria NCHAR(1) NOT NULL CHECK (NombreCategoria IN ('A', 'B', 'C')),
    DescripcionCategoria NVARCHAR(255),
    AniosMinimos INT,
    AniosMaximos INT,
    Estado BIT DEFAULT 1
);
GO

-- Ajustes Salariales
CREATE TABLE dbo.AjustesSalariales (
    AjusteID INT IDENTITY(1,1) PRIMARY KEY,
    IDCategoria INT NOT NULL,
    HaberBasico DECIMAL(10,2) NOT NULL,
    FechaVigencia DATE NOT NULL,
    FechaFinVigencia DATE NULL,
    Observaciones NVARCHAR(255),
    Estado BIT DEFAULT 1,
    CONSTRAINT FK_AjustesSalariales_Categorias 
        FOREIGN KEY (IDCategoria) REFERENCES dbo.Categorias(IDCategoria)
);
GO

-- Departamentos
CREATE TABLE dbo.Departamentos (
    IDDepartamento INT IDENTITY(1,1) PRIMARY KEY,
    NombreDepartamento NVARCHAR(100) NOT NULL,
    Descripcion NVARCHAR(255),
    Estado BIT DEFAULT 1
);
GO

-- Cargos (corregido: sin coma final)
CREATE TABLE dbo.Cargos (
    IDCargo INT IDENTITY(1,1) PRIMARY KEY,
    NombreCargo NVARCHAR(100) NOT NULL,
    Descripcion NVARCHAR(255),
    Estado BIT DEFAULT 1
);
GO

-- Funciones
CREATE TABLE dbo.Funciones (
    IDFuncion INT IDENTITY(1,1) PRIMARY KEY,
    IDCargo INT NOT NULL,
    DescripcionFuncion NVARCHAR(500) NOT NULL,
    Estado BIT DEFAULT 1,
    CONSTRAINT FK_Funciones_Cargos 
        FOREIGN KEY (IDCargo) REFERENCES dbo.Cargos(IDCargo)
);
GO

/* =========================
   2) Personas y relaciones
   ========================= */

-- Empleados
CREATE TABLE dbo.Empleados (
    IDEmpleado INT IDENTITY(1,1) PRIMARY KEY,
    Nombres NVARCHAR(100) NOT NULL,
    ApellidoPaterno NVARCHAR(50) NOT NULL,
    ApellidoMaterno NVARCHAR(50),
    FechaNacimiento DATE,
    Telefono NVARCHAR(20),
    Email NVARCHAR(100),
    Direccion NVARCHAR(255),
    FechaIngreso DATE NOT NULL,
    Estado BIT DEFAULT 1
);
GO

-- Dependientes
CREATE TABLE dbo.Dependientes (
    IDDependiente INT IDENTITY(1,1) PRIMARY KEY,
    IDEmpleado INT NOT NULL,
    Nombres NVARCHAR(100) NOT NULL,
    ApellidoPaterno NVARCHAR(50) NOT NULL,
    ApellidoMaterno NVARCHAR(50),
    FechaNacimiento DATE,
    Parentesco NVARCHAR(50) NOT NULL,
    Estado BIT DEFAULT 1,
    CONSTRAINT FK_Dependientes_Empleados 
        FOREIGN KEY (IDEmpleado) REFERENCES dbo.Empleados(IDEmpleado)
);
GO

-- Documentos
CREATE TABLE dbo.Documentos (
    IDDocumento INT IDENTITY(1,1) PRIMARY KEY,
    TipoDocumentoID INT NOT NULL,
    IDEmpleado INT NULL,
    IDDependiente INT NULL,
    NumeroDocumento NVARCHAR(50) NOT NULL,
    FechaEmision DATE,
    FechaVencimiento DATE,
    LugarEmision NVARCHAR(100),
    Estado BIT DEFAULT 1,
    CONSTRAINT FK_Documentos_TiposDocumento 
        FOREIGN KEY (TipoDocumentoID) REFERENCES dbo.TiposDocumento(TipoDocumentoID),
    CONSTRAINT FK_Documentos_Empleados 
        FOREIGN KEY (IDEmpleado) REFERENCES dbo.Empleados(IDEmpleado),
    CONSTRAINT FK_Documentos_Dependientes 
        FOREIGN KEY (IDDependiente) REFERENCES dbo.Dependientes(IDDependiente),
    CHECK (
        (IDEmpleado IS NOT NULL AND IDDependiente IS NULL) 
     OR (IDEmpleado IS NULL AND IDDependiente IS NOT NULL)
    )
);
GO

-- Evaluaciones (corregido: Evaluador con longitud)
CREATE TABLE dbo.Evaluaciones (
    IDEvaluacion INT IDENTITY(1,1) PRIMARY KEY,
    IDEmpleado INT NOT NULL,
    Gestion INT NOT NULL,
    Trimestre INT NOT NULL CHECK (Trimestre BETWEEN 1 AND 4),
    FechaEvaluacion DATE NOT NULL,
    Calificacion INT NOT NULL CHECK (Calificacion BETWEEN 1 AND 5),
    Observaciones NVARCHAR(500),
    Evaluador NVARCHAR(100) NOT NULL,
    AccionTomada NVARCHAR(255),
    Estado BIT DEFAULT 1,
    CONSTRAINT FK_Evaluaciones_Empleados 
        FOREIGN KEY (IDEmpleado) REFERENCES dbo.Empleados(IDEmpleado),
    UNIQUE (IDEmpleado, Gestion, Trimestre)
);
GO

/* =========================
   3) Contratación y gestión
   ========================= */

-- Contratos
CREATE TABLE dbo.Contratos (
    IDContrato INT IDENTITY(1,1) PRIMARY KEY,
    IDEmpleado INT NOT NULL,
    IDCategoria INT NOT NULL,
    IDCargo INT NOT NULL,
    IDDepartamento INT NOT NULL,
    NumeroContrato NVARCHAR(50) UNIQUE NOT NULL,
    TipoContrato NVARCHAR(50) NOT NULL DEFAULT 'Indefinido',
    FechaContrato DATE NOT NULL,
    FechaInicio DATE NOT NULL,
    FechaFin DATE NULL,
    HaberBasico DECIMAL(10,2) NOT NULL,
    Estado BIT DEFAULT 1,
    CONSTRAINT FK_Contratos_Empleados 
        FOREIGN KEY (IDEmpleado) REFERENCES dbo.Empleados(IDEmpleado),
    CONSTRAINT FK_Contratos_Categorias 
        FOREIGN KEY (IDCategoria) REFERENCES dbo.Categorias(IDCategoria),
    CONSTRAINT FK_Contratos_Cargos 
        FOREIGN KEY (IDCargo) REFERENCES dbo.Cargos(IDCargo),
    CONSTRAINT FK_Contratos_Departamentos 
        FOREIGN KEY (IDDepartamento) REFERENCES dbo.Departamentos(IDDepartamento)
);
GO

-- Afiliaciones
CREATE TABLE dbo.Afiliaciones (
    IDAfiliacion INT IDENTITY(1,1) PRIMARY KEY,
    IDContrato INT NOT NULL,
    IDTipoAfiliacion INT NOT NULL,
    NumeroAfiliacion NVARCHAR(50),
    FechaAfiliacion DATE NOT NULL,
    FechaDesafiliacion DATE NULL,
    EstadoAfiliacion NVARCHAR(20) DEFAULT 'Activo',
    Observaciones NVARCHAR(255),
    CONSTRAINT FK_Afiliaciones_Contratos 
        FOREIGN KEY (IDContrato) REFERENCES dbo.Contratos(IDContrato),
    CONSTRAINT FK_Afiliaciones_TiposAfiliacion 
        FOREIGN KEY (IDTipoAfiliacion) REFERENCES dbo.TiposAfiliacion(TipoAfiliacionID),
    CHECK (EstadoAfiliacion IN ('Activo', 'Inactivo'))
);
GO

-- Subsidios
CREATE TABLE dbo.Subsidios (
    IDSubsidio INT IDENTITY(1,1) PRIMARY KEY,
    IDContrato INT NOT NULL,
    IDTipoSubsidio INT NOT NULL,
    MontoSubsidio DECIMAL(10,2) NOT NULL,
    FechaInicio DATE NOT NULL,
    FechaFin DATE,
    Observaciones NVARCHAR(255),
    Estado BIT DEFAULT 1,
    CONSTRAINT FK_Subsidios_Contratos 
        FOREIGN KEY (IDContrato) REFERENCES dbo.Contratos(IDContrato),
    CONSTRAINT FK_Subsidios_TiposSubsidio 
        FOREIGN KEY (IDTipoSubsidio) REFERENCES dbo.TiposSubsidio(TipoSubsidioID)
);
GO

-- Solicitudes de Vacaciones
CREATE TABLE dbo.SolicitudesVacaciones (
    IDSolicitud INT IDENTITY(1,1) PRIMARY KEY,
    IDContrato INT NOT NULL,
    FechaSolicitud DATE NOT NULL,
    FechaInicio DATE NOT NULL,
    FechaFin DATE NOT NULL,
    DiasVacaciones INT NOT NULL,
    TipoSolicitud NVARCHAR(50) DEFAULT 'Vacaciones',
    EstadoSolicitud NVARCHAR(20) DEFAULT 'Pendiente',
    JefeAprobador NVARCHAR(100),
    FechaAprobacion DATE,
    Observaciones NVARCHAR(255),
    CONSTRAINT FK_SolicitudesVacaciones_Contratos 
        FOREIGN KEY (IDContrato) REFERENCES dbo.Contratos(IDContrato),
    CHECK (EstadoSolicitud IN ('Pendiente', 'Aprobada', 'Rechazada', 'Tomada')),
    CHECK (TipoSolicitud IN ('Vacaciones', 'Licencia'))
);
GO

-- Anticipos
CREATE TABLE dbo.Anticipos (
    IDAnticipo INT IDENTITY(1,1) PRIMARY KEY,
    IDContrato INT NOT NULL,
    MontoAnticipo DECIMAL(10,2) NOT NULL,
    FechaAnticipo DATE NOT NULL,
    MesDescuento INT NOT NULL CHECK (MesDescuento BETWEEN 1 AND 12),
    GestionDescuento INT NOT NULL,
    EstadoAnticipo NVARCHAR(20) DEFAULT 'Pendiente',
    CONSTRAINT FK_Anticipos_Contratos 
        FOREIGN KEY (IDContrato) REFERENCES dbo.Contratos(IDContrato),
    CHECK (EstadoAnticipo IN ('Pendiente', 'Descontado', 'Cancelado'))
);
GO

-- RC-IVA
CREATE TABLE dbo.RCIVA (
    IDRCIVA INT IDENTITY(1,1) PRIMARY KEY,
    IDContrato INT NOT NULL,
    Mes INT NOT NULL CHECK (Mes BETWEEN 1 AND 12),
    Gestion INT NOT NULL,
    MontoFacturas DECIMAL(10,2) NOT NULL DEFAULT 0,
    MontoIVA DECIMAL(10,2) NOT NULL DEFAULT 0,
    FechaPresentacion DATE,
    Estado NVARCHAR(20) DEFAULT 'Pendiente',
    CONSTRAINT FK_RCIVA_Contratos 
        FOREIGN KEY (IDContrato) REFERENCES dbo.Contratos(IDContrato),
    CHECK (Estado IN ('Pendiente', 'Presentado', 'Procesado'))
);
GO

-- Gestión de Salarios
CREATE TABLE dbo.GestionSalarios (
    IDGestionSalario INT IDENTITY(1,1) PRIMARY KEY,
    IDContrato INT NOT NULL,
    Mes INT NOT NULL CHECK (Mes BETWEEN 1 AND 12),
    Gestion INT NOT NULL,
    DiasTrabajos INT NOT NULL DEFAULT 30,
    SalarioBasico DECIMAL(10,2) NOT NULL,
    TotalIngresos DECIMAL(10,2) NOT NULL,
    TotalDescuentos DECIMAL(10,2) NOT NULL,
    LiquidoPagable DECIMAL(10,2) NOT NULL,
    FechaPago DATE,
    EstadoPago NVARCHAR(20) DEFAULT 'Pendiente',
    CONSTRAINT FK_GestionSalarios_Contratos 
        FOREIGN KEY (IDContrato) REFERENCES dbo.Contratos(IDContrato),
    CHECK (EstadoPago IN ('Pendiente', 'Pagado', 'Anulado')),
    UNIQUE (IDContrato, Mes, Gestion)
);
GO

-- Solicitudes de Lactancia
CREATE TABLE dbo.SolicitudesLactancia (
    IDSolicitudLactancia INT IDENTITY(1,1) PRIMARY KEY,
    IDContrato INT NOT NULL,
    FechaSolicitud DATE NOT NULL,
    FechaInicio DATE NOT NULL,
    FechaFin DATE NOT NULL,
    TipoProducto NVARCHAR(100),
    CantidadMensual DECIMAL(10,2),
    MontoMensual DECIMAL(10,2),
    EstadoSolicitud NVARCHAR(20) DEFAULT 'Aprobada',
    CONSTRAINT FK_SolicitudesLactancia_Contratos 
        FOREIGN KEY (IDContrato) REFERENCES dbo.Contratos(IDContrato),
    CHECK (EstadoSolicitud IN ('Pendiente', 'Aprobada', 'Rechazada', 'Finalizada'))
);
GO

-- Entregas de Lactancia (corregido: sin coma final)
CREATE TABLE dbo.EntregasLactancia (
    IDEntrega INT IDENTITY(1,1) PRIMARY KEY,
    IDSolicitudLactancia INT NOT NULL,
    FechaEntrega DATE NOT NULL,
    Cantidad DECIMAL(10,2) NOT NULL,
    MontoEntregado DECIMAL(10,2) NOT NULL,
    ResponsableEntrega NVARCHAR(100),
    Observaciones NVARCHAR(255),
    CONSTRAINT FK_EntregasLactancia_SolicitudesLactancia 
        FOREIGN KEY (IDSolicitudLactancia) REFERENCES dbo.SolicitudesLactancia(IDSolicitudLactancia)
);
GO

-- Finiquitos
CREATE TABLE dbo.Finiquitos (
    IDFiniquito INT IDENTITY(1,1) PRIMARY KEY,
    IDContrato INT NOT NULL,
    TipoRetiro NVARCHAR(20) NOT NULL,
    FechaRetiro DATE NOT NULL,
    FechaCalculoFiniquito DATE NOT NULL,
    DiasVacacionesPendientes INT DEFAULT 0,
    MontoVacacionesPendientes DECIMAL(10,2) DEFAULT 0,
    AguinaldoProporcional DECIMAL(10,2) DEFAULT 0,
    PrimaProporcional DECIMAL(10,2) DEFAULT 0,
    MontoDesahucio DECIMAL(10,2) DEFAULT 0,
    IndemnizacionAnosServicio DECIMAL(10,2) DEFAULT 0,
    TotalBeneficios DECIMAL(12,2) NOT NULL,
    TotalDescuentos DECIMAL(10,2) DEFAULT 0,
    LiquidoFiniquito DECIMAL(12,2) NOT NULL,
    EstadoFiniquito NVARCHAR(20) DEFAULT 'Calculado',
    CONSTRAINT FK_Finiquitos_Contratos 
        FOREIGN KEY (IDContrato) REFERENCES dbo.Contratos(IDContrato),
    CHECK (TipoRetiro IN ('Voluntario', 'Forzoso')),
    CHECK (EstadoFiniquito IN ('Calculado', 'Pagado', 'Anulado'))
);
GO

/* =========================
   4) Seguridad (Usuarios/Roles/Permisos)
   ========================= */

-- Usuarios
CREATE TABLE dbo.Usuarios (
    IDUsuario INT IDENTITY(1,1) PRIMARY KEY,
    NombreUsuario NVARCHAR(50) UNIQUE NOT NULL,
    ContrasenaHash NVARCHAR(255) NOT NULL,
    EmpleadoID INT NULL REFERENCES dbo.Empleados(IDEmpleado),
    Estado BIT DEFAULT 1
);
GO

-- Roles
CREATE TABLE dbo.Roles (
    IDRol INT IDENTITY(1,1) PRIMARY KEY,
    NombreRol NVARCHAR(50) UNIQUE NOT NULL,
    Descripcion NVARCHAR(255),
    Estado BIT DEFAULT 1
);
GO

-- UsuarioRoles (N:M)
CREATE TABLE dbo.UsuarioRoles (
    IDUsuarioRol INT IDENTITY(1,1) PRIMARY KEY,
    IDUsuario INT NOT NULL REFERENCES dbo.Usuarios(IDUsuario),
    IDRol INT NOT NULL REFERENCES dbo.Roles(IDRol),
    UNIQUE (IDUsuario, IDRol)
);
GO

-- Permisos
CREATE TABLE dbo.Permisos (
    IDPermiso INT IDENTITY(1,1) PRIMARY KEY,
    NombrePermiso NVARCHAR(100) UNIQUE NOT NULL,
    Descripcion NVARCHAR(255)
);
GO

-- RolPermisos (N:M)
CREATE TABLE dbo.RolPermisos (
    IDRolPermiso INT IDENTITY(1,1) PRIMARY KEY,
    IDRol INT NOT NULL REFERENCES dbo.Roles(IDRol),
    IDPermiso INT NOT NULL REFERENCES dbo.Permisos(IDPermiso),
    UNIQUE (IDRol, IDPermiso)
);
GO
