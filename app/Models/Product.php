<?php
namespace App\Models;

use App\Helpers\Database;
use App\Helpers\Schema;

final class Product
{
    public static function all(string $search = '', int $category = 0, string $status = ''): array
    {
        $units=Schema::unitsTable();
        $unitExpr=Schema::unitAbbreviationExpression('u');
        $sql="SELECT p.IdProducto,p.Codigo,p.CodigoBarras,p.Nombre,p.Descripcion,c.Nombre Categoria,$unitExpr Unidad,
                     ISNULL(e.StockActual,0) StockActual,p.StockMinimo,p.StockMaximo,p.PrecioCompra,p.PrecioVenta,p.Estado,
                     m.Nombre Marca,pr.RazonSocial Proveedor
              FROM dbo.Productos p
              INNER JOIN dbo.Categorias c ON c.IdCategoria=p.IdCategoria
              INNER JOIN $units u ON u.IdUnidad=p.IdUnidad
              LEFT JOIN dbo.Existencias e ON e.IdProducto=p.IdProducto
              LEFT JOIN dbo.Marcas m ON m.IdMarca=p.IdMarca
              LEFT JOIN dbo.Proveedores pr ON pr.IdProveedor=p.IdProveedor
              WHERE (:search='' OR p.Codigo LIKE :code OR p.Nombre LIKE :name OR p.CodigoBarras LIKE :barcode)
                AND (:category=0 OR p.IdCategoria=:category2)
                AND (:status='' OR p.Estado=:status2)
              ORDER BY p.Nombre";
        $s=Database::connection()->prepare($sql);
        $like='%'.$search.'%';
        $s->execute(['search'=>$search,'code'=>$like,'name'=>$like,'barcode'=>$like,'category'=>$category,'category2'=>$category,'status'=>$status,'status2'=>$status===''?0:(int)$status]);
        return $s->fetchAll();
    }

    public static function find(int $id): ?array
    {
        $s=Database::connection()->prepare("SELECT * FROM dbo.Productos WHERE IdProducto=:id");$s->execute(['id'=>$id]);return $s->fetch()?:null;
    }

    public static function create(array $data): int
    {
        $db=Database::connection();$db->beginTransaction();
        try {
            $s=$db->prepare("INSERT dbo.Productos(Codigo,CodigoBarras,Nombre,Descripcion,IdCategoria,IdUnidad,IdMarca,IdProveedor,PrecioCompra,PrecioVenta,StockMinimo,StockMaximo,Estado,IdUsuarioCreacion)
                             VALUES(:codigo,:barras,:nombre,:descripcion,:categoria,:unidad,:marca,:proveedor,:compra,:venta,:minimo,:maximo,1,:usuario)");
            $s->execute($data);$id=(int)$db->lastInsertId();
            $s=$db->prepare("INSERT dbo.Existencias(IdProducto,StockActual) VALUES(:id,0)");$s->execute(['id'=>$id]);$db->commit();return $id;
        } catch(\Throwable $e){$db->rollBack();throw $e;}
    }

    public static function update(array $data): void
    {
        $s=Database::connection()->prepare("UPDATE dbo.Productos SET Codigo=:codigo,CodigoBarras=:barras,Nombre=:nombre,Descripcion=:descripcion,IdCategoria=:categoria,IdUnidad=:unidad,IdMarca=:marca,IdProveedor=:proveedor,PrecioCompra=:compra,PrecioVenta=:venta,StockMinimo=:minimo,StockMaximo=:maximo,FechaModificacion=SYSDATETIME() WHERE IdProducto=:id");
        $s->execute($data);
    }

    public static function toggle(int $id): void
    {
        $s=Database::connection()->prepare("UPDATE dbo.Productos SET Estado=CASE WHEN Estado=1 THEN 0 ELSE 1 END,FechaModificacion=SYSDATETIME() WHERE IdProducto=:id");$s->execute(['id'=>$id]);
    }

    public static function catalogs(): array
    {
        $db=Database::connection();$units=Schema::unitsTable();
        return [
            'categories'=>$db->query("SELECT IdCategoria,Nombre FROM dbo.Categorias WHERE Estado=1 ORDER BY Nombre")->fetchAll(),
            'units'=>$db->query("SELECT IdUnidad,Nombre FROM $units WHERE Estado=1 ORDER BY Nombre")->fetchAll(),
            'brands'=>$db->query("SELECT IdMarca,Nombre FROM dbo.Marcas WHERE Estado=1 ORDER BY Nombre")->fetchAll(),
            'suppliers'=>$db->query("SELECT IdProveedor,RazonSocial FROM dbo.Proveedores WHERE Estado=1 ORDER BY RazonSocial")->fetchAll(),
        ];
    }
}
