USE SIGI;
GO

INSERT INTO dbo.Roles (Nombre, Descripcion) VALUES
('SUPERADMINISTRADOR','Acceso total'),('ADMINISTRADOR','Administracion funcional'),('BODEGUERO','Operaciones de inventario'),('CONSULTA','Solo lectura'),('AUDITOR','Consulta y auditoria');

INSERT INTO dbo.Permisos (Codigo, Nombre, Modulo) VALUES
('productos.ver','Ver productos','Productos'),('productos.crear','Crear productos','Productos'),
('inventario.entrada','Registrar entradas','Inventario'),('inventario.salida','Registrar salidas','Inventario'),
('reportes.ver','Ver reportes','Reportes'),('auditoria.ver','Ver auditoria','Auditoria');

INSERT INTO dbo.Categorias (Codigo, Nombre, Descripcion) VALUES
('GEN','General','Categoria general'),('TEC','Tecnologia','Equipos y accesorios'),('LIM','Limpieza','Insumos de limpieza');

INSERT INTO dbo.UnidadesMedida (Codigo, Nombre, Abreviatura) VALUES
('UND','Unidad','und'),('CAJ','Caja','caja'),('KG','Kilogramo','kg'),('LT','Litro','lt');

INSERT INTO dbo.TiposMovimiento (Codigo, Nombre, Naturaleza) VALUES
('ENT_COMPRA','Entrada por compra','E'),('ENT_AJUSTE','Ajuste positivo','E'),
('SAL_CONSUMO','Salida por consumo','S'),('SAL_AJUSTE','Ajuste negativo','S');

-- Genere el hash en PHP antes de ejecutar este bloque:
-- php -r "echo password_hash('Admin123*', PASSWORD_ARGON2ID), PHP_EOL;"
DECLARE @Hash VARCHAR(255) = '$argon2id$v=19$m=65536,t=4,p=1$REEMPLAZAR$REEMPLAZAR';
INSERT INTO dbo.Usuarios (NombreUsuario, Correo, Nombres, Apellidos, PasswordHash, DebeCambiarPassword)
VALUES ('admin','admin@sigi.local','Administrador','General',@Hash,1);

DECLARE @Admin INT = SCOPE_IDENTITY();
DECLARE @Rol INT = (SELECT IdRol FROM dbo.Roles WHERE Nombre='SUPERADMINISTRADOR');
INSERT INTO dbo.UsuarioRol (IdUsuario, IdRol) VALUES (@Admin, @Rol);
INSERT INTO dbo.RolPermiso (IdRol, IdPermiso) SELECT @Rol, IdPermiso FROM dbo.Permisos;
GO
