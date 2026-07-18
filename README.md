# SIGI - Sistema Integral de Gestión de Inventario

Primera versión funcional para revisión académica. Incluye login seguro, dashboard, productos y movimientos de entrada/salida con transacciones y prevención de stock negativo.

## Requisitos

- PHP 8.2+
- Extensiones `pdo_sqlsrv` y `sqlsrv`
- Composer
- SQL Server 2019+
- IIS o Apache

## Instalación

1. Copie `.env.example` como `.env` y configure la conexión.
2. Ejecute `composer install`.
3. Ejecute en SQL Server, en orden:
   - `database/01_create_database.sql`
   - `database/02_schema.sql`
4. Genere el hash inicial:
   `php -r "echo password_hash('Admin123*', PASSWORD_ARGON2ID), PHP_EOL;"`
5. Reemplace el valor de `@Hash` en `database/03_seed.sql` y ejecútelo.
6. Configure el sitio web para que su raíz pública sea la carpeta `public`.
7. Otorgue permisos de escritura a `storage/logs` al usuario del pool de IIS.

## Credenciales iniciales

- Usuario: `admin`
- Contraseña: la utilizada para generar el hash.

Cambie la contraseña al primer acceso.

## Controles implementados

- Consultas preparadas PDO.
- Hash Argon2id/bcrypt mediante `password_hash`.
- Tokens CSRF.
- Escape de salida contra XSS.
- Cookies HttpOnly, SameSite y Secure cuando hay HTTPS.
- Bloqueo por intentos fallidos.
- Transacciones y bloqueo de filas en movimientos.
- Token de idempotencia.
- Restricciones e integridad referencial.

## Pendientes para la siguiente iteración

- CRUD completo de usuarios, roles y permisos.
- Gestión completa de categorías.
- Kardex y reportes Excel.
- Auditoría automática en cada operación.
- Recuperación de contraseña.
- Pruebas automatizadas PHPUnit.
