# SIGI - Sprint 1: Autenticación y Administración

## Incluye
- Login seguro y bloqueo por intentos.
- Cambio obligatorio de contraseña temporal.
- CRUD de usuarios: crear, editar, activar/desactivar y desbloquear.
- Roles y asignación de permisos RBAC.
- Middleware de autenticación y autorización.
- CSRF, escape XSS, consultas preparadas y auditoría.
- Interfaz Bootstrap responsiva.

## Actualización desde SIGI v0.1
1. Haga respaldo de la carpeta y de la base SIGI.
2. Copie los archivos de este paquete sobre `C:\xampp\htdocs\SIGI`.
3. Ejecute en SQL Server: `database/04_sprint1_upgrade.sql`.
4. En la carpeta del proyecto ejecute:
   `composer dump-autoload`
5. Reinicie Apache.
6. Ingrese en `http://localhost:81/SIGI` (según el Alias configurado).

## Verificación
- Ingrese como SUPERADMINISTRADOR.
- Menú Usuarios: cree un usuario de prueba.
- Menú Roles: asigne permisos a un rol.
- Ingrese con el usuario nuevo y cambie su contraseña temporal.

Consulte `tests/SPRINT1_TEST_CASES.md` para las pruebas manuales.
