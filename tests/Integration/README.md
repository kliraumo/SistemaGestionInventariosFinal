# Pruebas de integración Sprint 1

Estas pruebas se ejecutan manualmente contra la base local `SIGI`:

1. Ejecutar `php bin/verify_environment.php`.
2. Iniciar sesión con un usuario activo.
3. Probar cinco claves incorrectas y confirmar bloqueo por 15 minutos.
4. Desbloquear desde Administración de usuarios.
5. Crear un usuario con contraseña temporal y comprobar cambio obligatorio.
6. Asignar y retirar permisos de un rol y volver a iniciar sesión.
7. Confirmar registros en `Auditoria` e `IntentosLogin`.
