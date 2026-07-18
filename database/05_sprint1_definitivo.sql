USE SIGI;
GO

IF OBJECT_ID('dbo.IntentosLogin','U') IS NULL
BEGIN
    CREATE TABLE dbo.IntentosLogin (
        IdIntento BIGINT IDENTITY PRIMARY KEY,
        UsuarioIngresado VARCHAR(150) NOT NULL,
        IdUsuario INT NULL,
        DireccionIP VARCHAR(45) NULL,
        AgenteUsuario VARCHAR(500) NULL,
        Exitoso BIT NOT NULL,
        FechaIntento DATETIME2 NOT NULL CONSTRAINT DF_IntentosLogin_Fecha DEFAULT SYSDATETIME(),
        CONSTRAINT FK_IntentosLogin_Usuario FOREIGN KEY (IdUsuario) REFERENCES dbo.Usuarios(IdUsuario)
    );
END;
GO
IF NOT EXISTS (SELECT 1 FROM sys.indexes WHERE name='IX_IntentosLogin_FechaIP' AND object_id=OBJECT_ID('dbo.IntentosLogin'))
    CREATE INDEX IX_IntentosLogin_FechaIP ON dbo.IntentosLogin(FechaIntento DESC, DireccionIP, Exitoso);
GO

MERGE dbo.Permisos AS target
USING (VALUES
 ('usuarios.ver','Ver usuarios','Usuarios'),('usuarios.crear','Crear usuarios','Usuarios'),
 ('usuarios.editar','Editar usuarios','Usuarios'),('usuarios.desactivar','Activar o desactivar usuarios','Usuarios'),
 ('usuarios.desbloquear','Desbloquear usuarios','Usuarios'),('roles.ver','Ver roles','Roles'),
 ('roles.editar','Administrar permisos de roles','Roles'),('productos.ver','Ver productos','Productos'),
 ('productos.crear','Crear productos','Productos'),('inventario.entrada','Registrar entradas','Inventario'),
 ('inventario.salida','Registrar salidas','Inventario'),('reportes.ver','Ver reportes','Reportes'),
 ('auditoria.ver','Ver auditoría','Auditoría')
) AS source(Codigo, Nombre, Modulo)
ON target.Codigo=source.Codigo
WHEN MATCHED THEN UPDATE SET Nombre=source.Nombre, Modulo=source.Modulo, Estado=1
WHEN NOT MATCHED THEN INSERT(Codigo,Nombre,Modulo) VALUES(source.Codigo,source.Nombre,source.Modulo);
GO

DECLARE @SuperAdmin INT=(SELECT TOP 1 IdRol FROM dbo.Roles WHERE Nombre='SUPERADMINISTRADOR');
IF @SuperAdmin IS NOT NULL
BEGIN
    INSERT dbo.RolPermiso(IdRol,IdPermiso)
    SELECT @SuperAdmin,p.IdPermiso FROM dbo.Permisos p
    WHERE p.Estado=1 AND NOT EXISTS(SELECT 1 FROM dbo.RolPermiso rp WHERE rp.IdRol=@SuperAdmin AND rp.IdPermiso=p.IdPermiso);
END;
GO

IF NOT EXISTS (SELECT 1 FROM sys.indexes WHERE name='IX_Usuarios_Estado' AND object_id=OBJECT_ID('dbo.Usuarios'))
    CREATE INDEX IX_Usuarios_Estado ON dbo.Usuarios(Estado,BloqueadoHasta);
IF NOT EXISTS (SELECT 1 FROM sys.indexes WHERE name='IX_Auditoria_FechaModulo' AND object_id=OBJECT_ID('dbo.Auditoria'))
    CREATE INDEX IX_Auditoria_FechaModulo ON dbo.Auditoria(FechaRegistro DESC,Modulo,Resultado);
GO
