# SIGI Enterprise - Sprint 2

## Alcance
- Rediseño corporativo responsive con sidebar y modo oscuro.
- Dashboard ejecutivo con 8 indicadores, flujo de 7 días, stock crítico y actividad reciente.
- CRUD de categorías.
- CRUD de proveedores.
- CRUD ampliado de productos: marca, proveedor, precios, mínimos y máximos.
- Filtros de productos y cambio de estado.
- Compatibilidad con tablas `Unidades` o `UnidadesMedida`.

## Instalación
1. Respalde la carpeta actual de SIGI y la base de datos.
2. Copie el contenido del paquete sobre `C:\xampp\htdocs\SIGI`.
3. Conserve su archivo `.env`.
4. Ejecute `database/06_sprint2_catalogos.sql` en la base `SIGI`.
5. Ejecute opcionalmente `database/06_Datos_Demo_SIGI.sql`.
6. En PowerShell: `composer dump-autoload`.
7. Abra `http://localhost:81/SIGI/public/index.php?route=dashboard` o su alias configurado.

## Pruebas rápidas
- Acceder al dashboard y validar los indicadores.
- Crear y editar una categoría.
- Crear y editar un proveedor.
- Crear un producto con categoría, unidad, marca, proveedor y precios.
- Filtrar productos por categoría y estado.
- Desactivar y reactivar un producto.
- Cambiar entre modo claro y oscuro.
