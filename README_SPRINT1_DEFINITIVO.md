# SIGI - Sprint 1 definitivo

## Incluye

- Inicio y cierre de sesión seguro.
- Argon2id, cambio obligatorio de contraseña y comprobación de la clave actual.
- Bloqueo temporal después de cinco intentos fallidos.
- Registro de intentos de acceso.
- CRUD administrativo de usuarios.
- Activación, desactivación y desbloqueo.
- Roles y permisos RBAC.
- Auditoría consultable desde el menú.
- CSRF, consultas preparadas, escape XSS y cabeceras de seguridad.
- Servicios separados de controladores.
- Pruebas PHPUnit y documento formal de pruebas.

## Instalación sobre el proyecto actual

1. Respaldar `C:\xampp\htdocs\SIGI` y la base `SIGI`.
2. Copiar el contenido de la carpeta `SIGI` del paquete sobre el proyecto actual.
3. Conservar el archivo `.env` local. Para SQL Express puede utilizar:

```env
DB_HOST=KMORALES\SQLEXPRESS
DB_PORT=
DB_DATABASE=SIGI
DB_USERNAME=sa
DB_PASSWORD=SU_CLAVE
DB_ENCRYPT=false
```

4. Ejecutar en SSMS:

```text
database\05_sprint1_definitivo.sql
```

5. Actualizar dependencias y autoload:

```powershell
cd C:\xampp\htdocs\SIGI
composer update
composer dump-autoload
php bin\verify_environment.php
```

6. Reiniciar Apache y abrir `http://localhost:81/SIGI`.

## Restablecer la contraseña del administrador

```powershell
php bin\reset_admin_password.php "Admin123*"
```

El usuario deberá cambiarla en el próximo acceso.

## Pruebas

```powershell
vendor\bin\phpunit
```

Documentos:

- `docs\Documento_Pruebas_Servicios_Sprint1.docx`
- `docs\PLAN_PRUEBAS_SERVICIOS_SPRINT1.md`
- `docs\MATRIZ_CUMPLIMIENTO_SPRINT1.md`

## Revisión funcional sugerida

1. Iniciar sesión como administrador.
2. Crear un usuario de prueba con rol BODEGUERO.
3. Iniciar sesión con el nuevo usuario y cambiar la contraseña temporal.
4. Retirar un permiso del rol y comprobar el acceso 403.
5. Probar cinco claves incorrectas y desbloquear al usuario.
6. Revisar los eventos desde el menú Auditoría.
