# Casos de prueba - Sprint 1

1. Login válido: admin / contraseña configurada. Resultado esperado: ingreso al dashboard.
2. Login inválido cinco veces: cuenta bloqueada durante 15 minutos.
3. CSRF: eliminar el token de un formulario POST. Resultado esperado: HTTP 419.
4. Crear usuario con contraseña débil. Resultado esperado: rechazo y mensaje de validación.
5. Crear usuario duplicado. Resultado esperado: no se guarda.
6. Crear usuario válido. Resultado esperado: usuario activo y contraseña temporal con Argon2id.
7. Primer ingreso de usuario nuevo. Resultado esperado: cambio obligatorio de contraseña.
8. Usuario sin permiso usuarios.ver. Resultado esperado: HTTP 403.
9. Desactivar usuario. Resultado esperado: no puede iniciar sesión.
10. Desbloquear usuario. Resultado esperado: IntentosFallidos=0 y BloqueadoHasta=NULL.
11. Asignar permisos a rol. Resultado esperado: navegación y backend respetan permisos.
12. XSS en nombres: `<script>alert(1)</script>`. Resultado esperado: se muestra escapado, nunca se ejecuta.
13. SQL Injection en login: `' OR 1=1--`. Resultado esperado: acceso denegado.
14. Logout: sesión y cookie destruidas.
15. Timeout de sesión: superar SESSION_IDLE_MINUTES. Resultado esperado: redirección al login.
