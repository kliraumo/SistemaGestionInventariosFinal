# Casos de prueba SIGI

| ID | Caso | Datos | Resultado esperado |
|---|---|---|---|
| CP-01 | Login correcto | Usuario activo y contraseña válida | Acceso al dashboard |
| CP-02 | Login incorrecto | Contraseña errónea | Mensaje genérico y aumento de intentos |
| CP-03 | Bloqueo | 5 intentos fallidos | Bloqueo temporal por 15 minutos |
| CP-04 | SQL Injection | `' OR 1=1 --` en login | Acceso denegado, sin error SQL |
| CP-05 | XSS | `<script>alert(1)</script>` en nombre | Se almacena/rechaza según validación y nunca se ejecuta |
| CP-06 | CSRF | POST sin token | Solicitud rechazada |
| CP-07 | Crear producto | Código único y datos válidos | Producto y existencia en cero creados |
| CP-08 | Código duplicado | Código existente | Operación rechazada por UNIQUE |
| CP-09 | Entrada | Cantidad 10 | Stock aumenta en 10 |
| CP-10 | Salida válida | Stock 10, salida 4 | Stock final 6 |
| CP-11 | Salida inválida | Stock 6, salida 7 | Rollback y stock permanece en 6 |
| CP-12 | Doble envío | Reutilizar token | Segundo movimiento rechazado |
