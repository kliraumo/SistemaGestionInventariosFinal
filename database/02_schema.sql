USE SIGI;
GO

CREATE TABLE dbo.Roles (
    IdRol INT IDENTITY PRIMARY KEY,
    Nombre VARCHAR(60) NOT NULL UNIQUE,
    Descripcion VARCHAR(250) NULL,
    Estado BIT NOT NULL CONSTRAINT DF_Roles_Estado DEFAULT 1
);

CREATE TABLE dbo.Permisos (
    IdPermiso INT IDENTITY PRIMARY KEY,
    Codigo VARCHAR(100) NOT NULL UNIQUE,
    Nombre VARCHAR(120) NOT NULL,
    Modulo VARCHAR(80) NOT NULL,
    Estado BIT NOT NULL CONSTRAINT DF_Permisos_Estado DEFAULT 1
);

CREATE TABLE dbo.Usuarios (
    IdUsuario INT IDENTITY PRIMARY KEY,
    NombreUsuario VARCHAR(50) NOT NULL UNIQUE,
    Correo VARCHAR(150) NOT NULL UNIQUE,
    Nombres VARCHAR(100) NOT NULL,
    Apellidos VARCHAR(100) NOT NULL,
    PasswordHash VARCHAR(255) NOT NULL,
    Estado BIT NOT NULL CONSTRAINT DF_Usuarios_Estado DEFAULT 1,
    BloqueadoHasta DATETIME2 NULL,
    IntentosFallidos INT NOT NULL CONSTRAINT DF_Usuarios_Intentos DEFAULT 0,
    DebeCambiarPassword BIT NOT NULL CONSTRAINT DF_Usuarios_Cambiar DEFAULT 1,
    UltimoAcceso DATETIME2 NULL,
    FechaCreacion DATETIME2 NOT NULL CONSTRAINT DF_Usuarios_Fecha DEFAULT SYSDATETIME(),
    FechaModificacion DATETIME2 NULL
);

CREATE TABLE dbo.UsuarioRol (
    IdUsuario INT NOT NULL,
    IdRol INT NOT NULL,
    CONSTRAINT PK_UsuarioRol PRIMARY KEY (IdUsuario, IdRol),
    CONSTRAINT FK_UsuarioRol_Usuario FOREIGN KEY (IdUsuario) REFERENCES dbo.Usuarios(IdUsuario),
    CONSTRAINT FK_UsuarioRol_Rol FOREIGN KEY (IdRol) REFERENCES dbo.Roles(IdRol)
);

CREATE TABLE dbo.RolPermiso (
    IdRol INT NOT NULL,
    IdPermiso INT NOT NULL,
    CONSTRAINT PK_RolPermiso PRIMARY KEY (IdRol, IdPermiso),
    CONSTRAINT FK_RolPermiso_Rol FOREIGN KEY (IdRol) REFERENCES dbo.Roles(IdRol),
    CONSTRAINT FK_RolPermiso_Permiso FOREIGN KEY (IdPermiso) REFERENCES dbo.Permisos(IdPermiso)
);

CREATE TABLE dbo.Categorias (
    IdCategoria INT IDENTITY PRIMARY KEY,
    Codigo VARCHAR(30) NOT NULL UNIQUE,
    Nombre VARCHAR(100) NOT NULL UNIQUE,
    Descripcion VARCHAR(300) NULL,
    Estado BIT NOT NULL CONSTRAINT DF_Categorias_Estado DEFAULT 1,
    FechaCreacion DATETIME2 NOT NULL CONSTRAINT DF_Categorias_Fecha DEFAULT SYSDATETIME()
);

CREATE TABLE dbo.UnidadesMedida (
    IdUnidad INT IDENTITY PRIMARY KEY,
    Codigo VARCHAR(20) NOT NULL UNIQUE,
    Nombre VARCHAR(80) NOT NULL UNIQUE,
    Abreviatura VARCHAR(15) NOT NULL,
    Estado BIT NOT NULL CONSTRAINT DF_Unidades_Estado DEFAULT 1
);

CREATE TABLE dbo.Productos (
    IdProducto INT IDENTITY PRIMARY KEY,
    Codigo VARCHAR(50) NOT NULL UNIQUE,
    CodigoBarras VARCHAR(80) NULL UNIQUE,
    Nombre VARCHAR(150) NOT NULL,
    Descripcion VARCHAR(500) NULL,
    IdCategoria INT NOT NULL,
    IdUnidad INT NOT NULL,
    StockMinimo DECIMAL(18,2) NOT NULL CONSTRAINT DF_Productos_Min DEFAULT 0,
    StockMaximo DECIMAL(18,2) NOT NULL CONSTRAINT DF_Productos_Max DEFAULT 0,
    Estado BIT NOT NULL CONSTRAINT DF_Productos_Estado DEFAULT 1,
    FechaCreacion DATETIME2 NOT NULL CONSTRAINT DF_Productos_Fecha DEFAULT SYSDATETIME(),
    IdUsuarioCreacion INT NOT NULL,
    CONSTRAINT CK_Productos_Stock CHECK (StockMinimo >= 0 AND StockMaximo >= StockMinimo),
    CONSTRAINT FK_Productos_Categoria FOREIGN KEY (IdCategoria) REFERENCES dbo.Categorias(IdCategoria),
    CONSTRAINT FK_Productos_Unidad FOREIGN KEY (IdUnidad) REFERENCES dbo.UnidadesMedida(IdUnidad),
    CONSTRAINT FK_Productos_Usuario FOREIGN KEY (IdUsuarioCreacion) REFERENCES dbo.Usuarios(IdUsuario)
);

CREATE TABLE dbo.Existencias (
    IdExistencia INT IDENTITY PRIMARY KEY,
    IdProducto INT NOT NULL UNIQUE,
    StockActual DECIMAL(18,2) NOT NULL CONSTRAINT DF_Existencias_Stock DEFAULT 0,
    FechaUltimaActualizacion DATETIME2 NOT NULL CONSTRAINT DF_Existencias_Fecha DEFAULT SYSDATETIME(),
    RowVersion ROWVERSION,
    CONSTRAINT CK_Existencias_NoNegativo CHECK (StockActual >= 0),
    CONSTRAINT FK_Existencias_Producto FOREIGN KEY (IdProducto) REFERENCES dbo.Productos(IdProducto)
);

CREATE TABLE dbo.TiposMovimiento (
    IdTipoMovimiento INT IDENTITY PRIMARY KEY,
    Codigo VARCHAR(30) NOT NULL UNIQUE,
    Nombre VARCHAR(100) NOT NULL,
    Naturaleza CHAR(1) NOT NULL,
    Estado BIT NOT NULL CONSTRAINT DF_TipoMov_Estado DEFAULT 1,
    CONSTRAINT CK_TipoMov_Naturaleza CHECK (Naturaleza IN ('E','S'))
);

CREATE TABLE dbo.MovimientosInventario (
    IdMovimiento BIGINT IDENTITY PRIMARY KEY,
    NumeroMovimiento VARCHAR(40) NOT NULL UNIQUE,
    FechaMovimiento DATETIME2 NOT NULL,
    IdTipoMovimiento INT NOT NULL,
    Referencia VARCHAR(100) NULL,
    Observacion VARCHAR(500) NULL,
    Estado VARCHAR(20) NOT NULL,
    IdUsuario INT NOT NULL,
    TokenIdempotencia VARCHAR(64) NOT NULL UNIQUE,
    CONSTRAINT FK_Movimientos_Tipo FOREIGN KEY (IdTipoMovimiento) REFERENCES dbo.TiposMovimiento(IdTipoMovimiento),
    CONSTRAINT FK_Movimientos_Usuario FOREIGN KEY (IdUsuario) REFERENCES dbo.Usuarios(IdUsuario)
);

CREATE TABLE dbo.MovimientoDetalle (
    IdDetalle BIGINT IDENTITY PRIMARY KEY,
    IdMovimiento BIGINT NOT NULL,
    IdProducto INT NOT NULL,
    Cantidad DECIMAL(18,2) NOT NULL,
    StockAnterior DECIMAL(18,2) NOT NULL,
    StockPosterior DECIMAL(18,2) NOT NULL,
    Observacion VARCHAR(500) NULL,
    CONSTRAINT CK_Detalle_Cantidad CHECK (Cantidad > 0),
    CONSTRAINT FK_Detalle_Movimiento FOREIGN KEY (IdMovimiento) REFERENCES dbo.MovimientosInventario(IdMovimiento),
    CONSTRAINT FK_Detalle_Producto FOREIGN KEY (IdProducto) REFERENCES dbo.Productos(IdProducto)
);

CREATE TABLE dbo.Auditoria (
    IdAuditoria BIGINT IDENTITY PRIMARY KEY,
    IdUsuario INT NULL,
    Accion VARCHAR(80) NOT NULL,
    Modulo VARCHAR(80) NOT NULL,
    Entidad VARCHAR(80) NULL,
    IdEntidad VARCHAR(80) NULL,
    DatosAnteriores NVARCHAR(MAX) NULL,
    DatosNuevos NVARCHAR(MAX) NULL,
    DireccionIP VARCHAR(45) NULL,
    AgenteUsuario VARCHAR(500) NULL,
    Resultado VARCHAR(30) NOT NULL,
    FechaRegistro DATETIME2 NOT NULL CONSTRAINT DF_Auditoria_Fecha DEFAULT SYSDATETIME(),
    CONSTRAINT FK_Auditoria_Usuario FOREIGN KEY (IdUsuario) REFERENCES dbo.Usuarios(IdUsuario)
);

CREATE INDEX IX_Productos_Nombre ON dbo.Productos(Nombre);
CREATE INDEX IX_Movimientos_Fecha ON dbo.MovimientosInventario(FechaMovimiento);
CREATE INDEX IX_Detalle_Producto ON dbo.MovimientoDetalle(IdProducto);
GO
