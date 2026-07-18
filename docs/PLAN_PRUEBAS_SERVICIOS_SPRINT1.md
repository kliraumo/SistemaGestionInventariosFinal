# Documento de pruebas de servicios – SIGI Sprint 1

## Objetivo
Validar las reglas de negocio, seguridad y servicios de autenticación, usuarios, roles y permisos.

## Casos principales

| ID | Servicio | Prueba | Entrada | Resultado esperado |
|---|---|---|---|---|
| TS-01 | Autenticación | Login válido | admin + clave correcta | Sesión creada y auditoría exitosa |
| TS-02 | Autenticación | Clave incorrecta | admin + clave falsa | Acceso denegado e intento incrementado |
| TS-03 | Autenticación | Bloqueo | 5 intentos fallidos | Bloqueo temporal de 15 minutos |
| TS-04 | Usuario | Crear válido | Datos completos | Usuario y rol guardados en transacción |
| TS-05 | Usuario | Usuario duplicado | Nombre existente | Operación rechazada |
| TS-06 | Usuario | Correo inválido | texto sin formato email | Validación rechazada |
| TS-07 | Usuario | Contraseña débil | 12345678 | Validación rechazada |
| TS-08 | Usuario | Cambio de clave | actual + nueva segura | Hash actualizado y sesión habilitada |
| TS-09 | Rol | Permiso válido | IDs activos | Permisos reemplazados en transacción |
| TS-10 | Rol | Permiso inexistente | ID arbitrario | ID descartado |
| TS-11 | Seguridad | CSRF incorrecto | token falso | HTTP 419 |
| TS-12 | Autorización | Acceso sin permiso | rol consulta | HTTP 403 y auditoría |
| TS-13 | Auditoría | Acción administrativa | crear usuario | Registro con usuario, IP y fecha |
| TS-14 | Sesión | Inactividad | tiempo mayor al límite | Cierre y redirección al login |

## Ejecución automatizada

```powershell
cd C:\xampp\htdocs\SIGI
composer install
vendor\bin\phpunit
```

## Criterio de aceptación
Todas las pruebas unitarias deben aprobar y las pruebas de integración no deben producir errores PHP, SQL ni accesos no autorizados.
