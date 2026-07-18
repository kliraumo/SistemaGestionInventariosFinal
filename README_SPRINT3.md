# SIGI Enterprise - Sprint 3

## Funcionalidades
- Entradas de inventario.
- Salidas con validación de stock.
- Consulta de existencias con filtros y alertas.
- Historial de movimientos / Kardex básico.
- Reporte Excel de inventario.
- Reporte Excel de movimientos.
- Permisos RBAC y auditoría.
- Compatibilidad de Flash::set y Flash::add.

## Instalación
1. Respalde el proyecto y la base de datos.
2. Copie la carpeta SIGI sobre su instalación conservando `.env`.
3. Ejecute `database/07_sprint3_inventario_reportes.sql` en la base SIGI.
4. Ejecute `composer install` o `composer update` para instalar PhpSpreadsheet.
5. Ejecute `composer dump-autoload`.
6. Ingrese con un rol administrador y verifique los nuevos permisos.

## Rutas
- `inventario/entrada`
- `inventario/salida`
- `inventario/stock`
- `inventario/movimientos`
- `reportes/inventario-excel`
- `reportes/movimientos-excel`
