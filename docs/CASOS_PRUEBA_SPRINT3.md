# Casos de prueba Sprint 3

1. Registrar una entrada válida y comprobar que aumenta el stock.
2. Registrar una salida válida y comprobar que disminuye el stock.
3. Intentar una salida superior al stock disponible; debe rechazarse.
4. Ingresar cantidad cero o negativa; debe rechazarse.
5. Reenviar el mismo formulario; el token de idempotencia evita duplicados.
6. Consultar stock por código, nombre o código de barras.
7. Filtrar stock por categoría.
8. Filtrar productos normales, bajo mínimo y agotados.
9. Exportar inventario a Excel y verificar encabezados y valores.
10. Exportar movimientos a Excel y verificar trazabilidad.
11. Verificar permisos para entrada, salida, stock y reportes.
12. Confirmar que cada movimiento conserva stock anterior y posterior.
