# Matriz de cumplimiento – Sprint 1

| Requisito | Implementación | Evidencia |
|---|---|---|
| RF1 Inicio de sesión | Login, logout, sesión, bloqueo | `AuthController`, `AuthService` |
| RF2 Registrar usuarios | Crear, editar, activar, desbloquear | `UserController`, `UserService` |
| RF3 Administrar roles | Asignación de permisos RBAC | `RoleController`, `RoleService` |
| RNF1 Respuesta < 3 s | Índices y consultas preparadas | SQL `05_sprint1_definitivo.sql` |
| RNF2 Contraseñas cifradas | Argon2id y `password_verify` | Modelo/servicio de usuario |
| RNF4 Navegadores | Bootstrap 5 / HTML5 | Vistas responsivas |
| RNF5 Interfaz responsiva | Navbar y tablas adaptables | Vistas Bootstrap |
| Seguridad adicional | CSRF, XSS, SQLi, headers, auditoría | Helpers y `public/index.php` |
