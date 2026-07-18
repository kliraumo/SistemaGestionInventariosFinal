USE SIGI;
GO
SET XACT_ABORT ON;
GO

IF OBJECT_ID('dbo.Proveedores','U') IS NULL
BEGIN
    CREATE TABLE dbo.Proveedores (
        IdProveedor INT IDENTITY PRIMARY KEY,
        Identificacion VARCHAR(20) NULL,
        RazonSocial VARCHAR(180) NOT NULL,
        NombreComercial VARCHAR(180) NULL,
        Correo VARCHAR(150) NULL,
        Telefono VARCHAR(30) NULL,
        Direccion VARCHAR(300) NULL,
        Estado BIT NOT NULL CONSTRAINT DF_Proveedores_Estado DEFAULT 1,
        FechaCreacion DATETIME2 NOT NULL CONSTRAINT DF_Proveedores_Fecha DEFAULT SYSDATETIME(),
        IdUsuarioCreacion INT NULL,
        CONSTRAINT FK_Proveedores_Usuario FOREIGN KEY (IdUsuarioCreacion) REFERENCES dbo.Usuarios(IdUsuario)
    );
END;
GO

IF OBJECT_ID('dbo.Marcas','U') IS NULL
BEGIN
    CREATE TABLE dbo.Marcas (
        IdMarca INT IDENTITY PRIMARY KEY,
        Nombre VARCHAR(100) NOT NULL UNIQUE,
        Descripcion VARCHAR(300) NULL,
        Estado BIT NOT NULL CONSTRAINT DF_Marcas_Estado DEFAULT 1,
        FechaCreacion DATETIME2 NOT NULL CONSTRAINT DF_Marcas_Fecha DEFAULT SYSDATETIME()
    );
END;
GO

IF OBJECT_ID('dbo.Bodegas','U') IS NULL
BEGIN
    CREATE TABLE dbo.Bodegas (
        IdBodega INT IDENTITY PRIMARY KEY,
        Codigo VARCHAR(30) NOT NULL UNIQUE,
        Nombre VARCHAR(120) NOT NULL,
        Direccion VARCHAR(300) NULL,
        Estado BIT NOT NULL CONSTRAINT DF_Bodegas_Estado DEFAULT 1,
        FechaCreacion DATETIME2 NOT NULL CONSTRAINT DF_Bodegas_Fecha DEFAULT SYSDATETIME()
    );
END;
GO

IF COL_LENGTH('dbo.Productos','IdMarca') IS NULL
    ALTER TABLE dbo.Productos ADD IdMarca INT NULL;
IF COL_LENGTH('dbo.Productos','IdProveedor') IS NULL
    ALTER TABLE dbo.Productos ADD IdProveedor INT NULL;
IF COL_LENGTH('dbo.Productos','PrecioCompra') IS NULL
    ALTER TABLE dbo.Productos ADD PrecioCompra DECIMAL(18,2) NOT NULL CONSTRAINT DF_Productos_PrecioCompra DEFAULT 0;
IF COL_LENGTH('dbo.Productos','PrecioVenta') IS NULL
    ALTER TABLE dbo.Productos ADD PrecioVenta DECIMAL(18,2) NOT NULL CONSTRAINT DF_Productos_PrecioVenta DEFAULT 0;
IF COL_LENGTH('dbo.Productos','Imagen') IS NULL
    ALTER TABLE dbo.Productos ADD Imagen VARCHAR(255) NULL;
IF COL_LENGTH('dbo.Productos','FechaModificacion') IS NULL
    ALTER TABLE dbo.Productos ADD FechaModificacion DATETIME2 NULL;
GO

IF NOT EXISTS (SELECT 1 FROM sys.foreign_keys WHERE name='FK_Productos_Marca')
    ALTER TABLE dbo.Productos ADD CONSTRAINT FK_Productos_Marca FOREIGN KEY(IdMarca) REFERENCES dbo.Marcas(IdMarca);
IF NOT EXISTS (SELECT 1 FROM sys.foreign_keys WHERE name='FK_Productos_Proveedor')
    ALTER TABLE dbo.Productos ADD CONSTRAINT FK_Productos_Proveedor FOREIGN KEY(IdProveedor) REFERENCES dbo.Proveedores(IdProveedor);
GO

MERGE dbo.Marcas AS T
USING (VALUES
 ('Dell','Equipos informáticos'),('HP','Tecnología e impresión'),('Logitech','Accesorios tecnológicos'),
 ('Samsung','Pantallas y electrónica'),('TP-Link','Conectividad'),('Genérica','Productos sin marca específica')
) AS S(Nombre,Descripcion)
ON T.Nombre=S.Nombre
WHEN MATCHED THEN UPDATE SET T.Descripcion=S.Descripcion,T.Estado=1
WHEN NOT MATCHED THEN INSERT(Nombre,Descripcion) VALUES(S.Nombre,S.Descripcion);
GO

IF NOT EXISTS (SELECT 1 FROM dbo.Proveedores WHERE RazonSocial='Proveedor General SIGI')
INSERT dbo.Proveedores(Identificacion,RazonSocial,NombreComercial,Correo,Telefono,Direccion,IdUsuarioCreacion)
SELECT '9999999999001','Proveedor General SIGI','Proveedor Demo','compras@sigi.local','0999999999','Quito - Ecuador',MIN(IdUsuario)
FROM dbo.Usuarios;
GO

IF NOT EXISTS (SELECT 1 FROM dbo.Bodegas WHERE Codigo='BOD-PRINCIPAL')
INSERT dbo.Bodegas(Codigo,Nombre,Direccion) VALUES('BOD-PRINCIPAL','Bodega Principal','Quito - Ecuador');
GO

MERGE dbo.Permisos AS T
USING (VALUES
 ('categorias.ver','Ver categorías','Catálogos'),('categorias.crear','Crear categorías','Catálogos'),
 ('categorias.editar','Editar categorías','Catálogos'),('proveedores.ver','Ver proveedores','Catálogos'),
 ('proveedores.crear','Crear proveedores','Catálogos'),('proveedores.editar','Editar proveedores','Catálogos'),
 ('marcas.ver','Ver marcas','Catálogos'),('marcas.crear','Crear marcas','Catálogos'),
 ('productos.editar','Editar productos','Productos'),('productos.estado','Cambiar estado de productos','Productos')
) AS S(Codigo,Nombre,Modulo)
ON T.Codigo=S.Codigo
WHEN MATCHED THEN UPDATE SET T.Nombre=S.Nombre,T.Modulo=S.Modulo,T.Estado=1
WHEN NOT MATCHED THEN INSERT(Codigo,Nombre,Modulo) VALUES(S.Codigo,S.Nombre,S.Modulo);
GO

DECLARE @Rol INT=(SELECT TOP 1 IdRol FROM dbo.Roles WHERE Nombre='SUPERADMINISTRADOR');
IF @Rol IS NOT NULL
INSERT dbo.RolPermiso(IdRol,IdPermiso)
SELECT @Rol,P.IdPermiso FROM dbo.Permisos P
WHERE P.Estado=1 AND NOT EXISTS(SELECT 1 FROM dbo.RolPermiso RP WHERE RP.IdRol=@Rol AND RP.IdPermiso=P.IdPermiso);
GO

IF NOT EXISTS (SELECT 1 FROM sys.indexes WHERE name='IX_Productos_CategoriaEstado' AND object_id=OBJECT_ID('dbo.Productos'))
CREATE INDEX IX_Productos_CategoriaEstado ON dbo.Productos(IdCategoria,Estado) INCLUDE(Nombre,Codigo,StockMinimo,StockMaximo);
IF NOT EXISTS (SELECT 1 FROM sys.indexes WHERE name='IX_Proveedores_RazonSocial' AND object_id=OBJECT_ID('dbo.Proveedores'))
CREATE INDEX IX_Proveedores_RazonSocial ON dbo.Proveedores(RazonSocial,Estado);
GO

PRINT 'Sprint 2 instalado correctamente.';
