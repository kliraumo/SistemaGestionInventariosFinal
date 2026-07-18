USE SIGI;
GO
SET XACT_ABORT ON;
GO

IF OBJECT_ID('dbo.TiposMovimiento','U') IS NULL
BEGIN
 CREATE TABLE dbo.TiposMovimiento(IdTipoMovimiento INT IDENTITY PRIMARY KEY,Nombre VARCHAR(80) NOT NULL,Naturaleza CHAR(1) NOT NULL,Estado BIT NOT NULL CONSTRAINT DF_TiposMovimiento_Estado DEFAULT 1,CONSTRAINT CK_TiposMovimiento_Naturaleza CHECK(Naturaleza IN('E','S')));
END;
GO
MERGE dbo.TiposMovimiento T USING(VALUES('Entrada de inventario','E'),('Salida de inventario','S'))S(Nombre,Naturaleza) ON T.Naturaleza=S.Naturaleza WHEN MATCHED THEN UPDATE SET T.Nombre=S.Nombre,T.Estado=1 WHEN NOT MATCHED THEN INSERT(Nombre,Naturaleza)VALUES(S.Nombre,S.Naturaleza);
GO

IF OBJECT_ID('dbo.MovimientosInventario','U') IS NULL
BEGIN
 CREATE TABLE dbo.MovimientosInventario(IdMovimiento BIGINT IDENTITY PRIMARY KEY,NumeroMovimiento VARCHAR(40) NOT NULL UNIQUE,FechaMovimiento DATETIME2 NOT NULL CONSTRAINT DF_Movimientos_Fecha DEFAULT SYSDATETIME(),IdTipoMovimiento INT NOT NULL,Referencia VARCHAR(100) NULL,Observacion VARCHAR(500) NULL,Estado VARCHAR(20) NOT NULL CONSTRAINT DF_Movimientos_Estado DEFAULT 'CONFIRMADO',IdUsuario INT NOT NULL,TokenIdempotencia VARCHAR(100) NOT NULL UNIQUE,CONSTRAINT FK_Movimientos_Tipo FOREIGN KEY(IdTipoMovimiento) REFERENCES dbo.TiposMovimiento(IdTipoMovimiento),CONSTRAINT FK_Movimientos_Usuario FOREIGN KEY(IdUsuario) REFERENCES dbo.Usuarios(IdUsuario));
END;
GO
IF OBJECT_ID('dbo.MovimientoDetalle','U') IS NULL
BEGIN
 CREATE TABLE dbo.MovimientoDetalle(IdDetalle BIGINT IDENTITY PRIMARY KEY,IdMovimiento BIGINT NOT NULL,IdProducto INT NOT NULL,Cantidad DECIMAL(18,2) NOT NULL,StockAnterior DECIMAL(18,2) NOT NULL,StockPosterior DECIMAL(18,2) NOT NULL,Observacion VARCHAR(500) NULL,CONSTRAINT CK_MovimientoDetalle_Cantidad CHECK(Cantidad>0),CONSTRAINT FK_Detalle_Movimiento FOREIGN KEY(IdMovimiento) REFERENCES dbo.MovimientosInventario(IdMovimiento),CONSTRAINT FK_Detalle_Producto FOREIGN KEY(IdProducto) REFERENCES dbo.Productos(IdProducto));
END;
GO

IF NOT EXISTS(SELECT 1 FROM sys.indexes WHERE name='IX_Movimientos_Fecha' AND object_id=OBJECT_ID('dbo.MovimientosInventario')) CREATE INDEX IX_Movimientos_Fecha ON dbo.MovimientosInventario(FechaMovimiento DESC) INCLUDE(NumeroMovimiento,IdTipoMovimiento,IdUsuario);
IF NOT EXISTS(SELECT 1 FROM sys.indexes WHERE name='IX_Detalle_Producto' AND object_id=OBJECT_ID('dbo.MovimientoDetalle')) CREATE INDEX IX_Detalle_Producto ON dbo.MovimientoDetalle(IdProducto,IdMovimiento) INCLUDE(Cantidad,StockAnterior,StockPosterior);
GO

MERGE dbo.Permisos T USING(VALUES
('inventario.entrada','Registrar entradas de inventario','Inventario'),
('inventario.salida','Registrar salidas de inventario','Inventario'),
('inventario.stock','Consultar stock disponible','Inventario'),
('inventario.movimientos','Consultar movimientos de inventario','Inventario'),
('reportes.inventario','Generar reporte Excel de inventario','Reportes'),
('reportes.movimientos','Generar reporte Excel de movimientos','Reportes')
)S(Codigo,Nombre,Modulo) ON T.Codigo=S.Codigo WHEN MATCHED THEN UPDATE SET T.Nombre=S.Nombre,T.Modulo=S.Modulo,T.Estado=1 WHEN NOT MATCHED THEN INSERT(Codigo,Nombre,Modulo)VALUES(S.Codigo,S.Nombre,S.Modulo);
GO
DECLARE @Rol INT=(SELECT TOP 1 IdRol FROM dbo.Roles WHERE Nombre='SUPERADMINISTRADOR');
IF @Rol IS NULL SELECT @Rol=MIN(IdRol) FROM dbo.Roles;
INSERT dbo.RolPermiso(IdRol,IdPermiso) SELECT @Rol,p.IdPermiso FROM dbo.Permisos p WHERE p.Codigo IN('inventario.entrada','inventario.salida','inventario.stock','inventario.movimientos','reportes.inventario','reportes.movimientos') AND NOT EXISTS(SELECT 1 FROM dbo.RolPermiso rp WHERE rp.IdRol=@Rol AND rp.IdPermiso=p.IdPermiso);
GO
PRINT 'Sprint 3 instalado correctamente: entradas, salidas, consulta de stock y reportes Excel.';
