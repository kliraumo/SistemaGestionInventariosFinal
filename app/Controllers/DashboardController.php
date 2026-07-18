<?php
namespace App\Controllers;
use App\Helpers\{Database,View};
use App\Middleware\AuthMiddleware;
final class DashboardController
{
 public function index(): void {
  AuthMiddleware::handle();$db=Database::connection();
  $stats=[
   'productos'=>(int)$db->query("SELECT COUNT(*) FROM dbo.Productos WHERE Estado=1")->fetchColumn(),
   'categorias'=>(int)$db->query("SELECT COUNT(*) FROM dbo.Categorias WHERE Estado=1")->fetchColumn(),
   'sin_stock'=>(int)$db->query("SELECT COUNT(*) FROM dbo.Existencias WHERE StockActual<=0")->fetchColumn(),
   'stock_bajo'=>(int)$db->query("SELECT COUNT(*) FROM dbo.Existencias e JOIN dbo.Productos p ON p.IdProducto=e.IdProducto WHERE e.StockActual>0 AND e.StockActual<=p.StockMinimo")->fetchColumn(),
   'stock_total'=>(float)$db->query("SELECT ISNULL(SUM(StockActual),0) FROM dbo.Existencias")->fetchColumn(),
   'valor'=>(float)$db->query("SELECT ISNULL(SUM(e.StockActual*p.PrecioCompra),0) FROM dbo.Existencias e JOIN dbo.Productos p ON p.IdProducto=e.IdProducto")->fetchColumn(),
   'entradas'=>(float)$db->query("SELECT ISNULL(SUM(d.Cantidad),0) FROM dbo.MovimientosInventario m JOIN dbo.TiposMovimiento t ON t.IdTipoMovimiento=m.IdTipoMovimiento JOIN dbo.MovimientoDetalle d ON d.IdMovimiento=m.IdMovimiento WHERE t.Naturaleza='E' AND CAST(m.FechaMovimiento AS date)=CAST(GETDATE() AS date)")->fetchColumn(),
   'salidas'=>(float)$db->query("SELECT ISNULL(SUM(d.Cantidad),0) FROM dbo.MovimientosInventario m JOIN dbo.TiposMovimiento t ON t.IdTipoMovimiento=m.IdTipoMovimiento JOIN dbo.MovimientoDetalle d ON d.IdMovimiento=m.IdMovimiento WHERE t.Naturaleza='S' AND CAST(m.FechaMovimiento AS date)=CAST(GETDATE() AS date)")->fetchColumn(),
  ];
  $recent=$db->query("SELECT TOP 8 m.FechaMovimiento,t.Nombre Tipo,p.Nombre Producto,d.Cantidad,t.Naturaleza,u.NombreUsuario FROM dbo.MovimientosInventario m JOIN dbo.TiposMovimiento t ON t.IdTipoMovimiento=m.IdTipoMovimiento JOIN dbo.MovimientoDetalle d ON d.IdMovimiento=m.IdMovimiento JOIN dbo.Productos p ON p.IdProducto=d.IdProducto JOIN dbo.Usuarios u ON u.IdUsuario=m.IdUsuario ORDER BY m.FechaMovimiento DESC")->fetchAll();
  $critical=$db->query("SELECT TOP 6 p.Codigo,p.Nombre,e.StockActual,p.StockMinimo FROM dbo.Productos p JOIN dbo.Existencias e ON e.IdProducto=p.IdProducto WHERE p.Estado=1 AND e.StockActual<=p.StockMinimo ORDER BY (p.StockMinimo-e.StockActual) DESC")->fetchAll();
  $flow=$db->query(";WITH D AS (SELECT CAST(DATEADD(DAY,-6,CAST(GETDATE() AS date)) AS date) F UNION ALL SELECT DATEADD(DAY,1,F) FROM D WHERE F<CAST(GETDATE() AS date)) SELECT CONVERT(varchar(10),D.F,23) Fecha,ISNULL(SUM(CASE WHEN t.Naturaleza='E' THEN md.Cantidad ELSE 0 END),0) Entradas,ISNULL(SUM(CASE WHEN t.Naturaleza='S' THEN md.Cantidad ELSE 0 END),0) Salidas FROM D LEFT JOIN dbo.MovimientosInventario m ON CAST(m.FechaMovimiento AS date)=D.F LEFT JOIN dbo.TiposMovimiento t ON t.IdTipoMovimiento=m.IdTipoMovimiento LEFT JOIN dbo.MovimientoDetalle md ON md.IdMovimiento=m.IdMovimiento GROUP BY D.F ORDER BY D.F OPTION(MAXRECURSION 10)")->fetchAll();
  View::render('dashboard/index',['title'=>'Dashboard Corporativo','stats'=>$stats,'recent'=>$recent,'critical'=>$critical,'flow'=>$flow]);
 }
}
