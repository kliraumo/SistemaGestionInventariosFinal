# SIGI - Corrección del núcleo

Esta revisión corrige el error:

`Class "App\\Helpers\\Database" not found`

## Cambios

- Se agregó `app/Helpers/Database.php` dentro de la ruta PSR-4 correcta.
- Se dejó `config/database.php` únicamente como archivo de configuración.
- Se agregó `tests/smoke_autoload.php` para comprobar el autoload.

## Instalación

1. Copie el contenido del paquete sobre `C:\xampp\htdocs\SIGI`.
2. En PowerShell ejecute:

```powershell
cd C:\xampp\htdocs\SIGI
composer dump-autoload
php tests\smoke_autoload.php
```

3. El resultado esperado es:

`Autoload SIGI correcto.`

4. Reinicie Apache y abra `http://localhost:81/SIGI`.
