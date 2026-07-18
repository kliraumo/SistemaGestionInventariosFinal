<?php
namespace App\Models;

use App\Helpers\Database;

final class Inventory
{
    public static function products(): array
    {
        return Database::connection()->query("SELECT p.IdProducto,p.Codigo,p.CodigoBarras,p.Nombre,c.Nombre Categoria,u.Nombre Unidad,e.StockActual,p.StockMinimo,p.StockMaximo FROM dbo.Productos p INNER JOIN dbo.Categorias c ON c.IdCategoria=p.IdCategoria INNER JOIN dbo.UnidadesMedida u ON u.IdUnidad=p.IdUnidad LEFT JOIN dbo.Existencias e ON e.IdProducto=p.IdProducto WHERE p.Estado=1 ORDER BY p.Nombre")->fetchAll();
    }

    public static function stock(array $filters=[]): array
    {
        $sql="SELECT p.IdProducto,p.Codigo,p.CodigoBarras,p.Nombre,c.Nombre Categoria,u.Nombre Unidad,COALESCE(e.StockActual,0) StockActual,p.StockMinimo,p.StockMaximo,CASE WHEN COALESCE(e.StockActual,0)=0 THEN 'AGOTADO' WHEN COALESCE(e.StockActual,0)<=p.StockMinimo THEN 'BAJO' ELSE 'NORMAL' END EstadoStock FROM dbo.Productos p INNER JOIN dbo.Categorias c ON c.IdCategoria=p.IdCategoria INNER JOIN dbo.UnidadesMedida u ON u.IdUnidad=p.IdUnidad LEFT JOIN dbo.Existencias e ON e.IdProducto=p.IdProducto WHERE p.Estado=1";
        $params=[];
        if(($filters['q']??'')!==''){ $sql.=" AND (p.Codigo LIKE :q1 OR p.Nombre LIKE :q2 OR p.CodigoBarras LIKE :q3)"; $like='%'.$filters['q'].'%'; $params=['q1'=>$like,'q2'=>$like,'q3'=>$like]; }
        if((int)($filters['categoria']??0)>0){ $sql.=" AND p.IdCategoria=:categoria"; $params['categoria']=(int)$filters['categoria']; }
        if(($filters['estado']??'')==='agotado') $sql.=" AND COALESCE(e.StockActual,0)=0";
        if(($filters['estado']??'')==='bajo') $sql.=" AND COALESCE(e.StockActual,0)>0 AND COALESCE(e.StockActual,0)<=p.StockMinimo";
        if(($filters['estado']??'')==='normal') $sql.=" AND COALESCE(e.StockActual,0)>p.StockMinimo";
        $sql.=" ORDER BY p.Nombre";
        $stmt=Database::connection()->prepare($sql); $stmt->execute($params); return $stmt->fetchAll();
    }

    public static function movements(int $limit=100): array
    {
        $limit=max(1,min($limit,500));
        return Database::connection()->query("SELECT TOP ($limit) m.NumeroMovimiento,m.FechaMovimiento,t.Nombre Tipo,t.Naturaleza,p.Codigo,p.Nombre Producto,d.Cantidad,d.StockAnterior,d.StockPosterior,m.Referencia,m.Observacion,u.NombreUsuario FROM dbo.MovimientosInventario m INNER JOIN dbo.TiposMovimiento t ON t.IdTipoMovimiento=m.IdTipoMovimiento INNER JOIN dbo.MovimientoDetalle d ON d.IdMovimiento=m.IdMovimiento INNER JOIN dbo.Productos p ON p.IdProducto=d.IdProducto INNER JOIN dbo.Usuarios u ON u.IdUsuario=m.IdUsuario ORDER BY m.FechaMovimiento DESC,m.IdMovimiento DESC")->fetchAll();
    }

    public static function register(array $header, array $details): string
    {
        $db=Database::connection(); $db->beginTransaction();
        try {
            $token=(string)$header['token'];
            $dup=$db->prepare("SELECT NumeroMovimiento FROM dbo.MovimientosInventario WHERE TokenIdempotencia=:token"); $dup->execute(['token'=>$token]);
            if($existing=$dup->fetchColumn()){ $db->rollBack(); return (string)$existing; }
            $type=$db->prepare("SELECT Naturaleza FROM dbo.TiposMovimiento WHERE IdTipoMovimiento=:id AND Estado=1"); $type->execute(['id'=>$header['tipo']]); $nature=$type->fetchColumn();
            if(!in_array($nature,['E','S'],true)) throw new \RuntimeException('Tipo de movimiento inválido.');
            $number=($nature==='E'?'ENT-':'SAL-').date('Ymd-His').'-'.random_int(100,999);
            $stmt=$db->prepare("INSERT dbo.MovimientosInventario(NumeroMovimiento,FechaMovimiento,IdTipoMovimiento,Referencia,Observacion,Estado,IdUsuario,TokenIdempotencia) VALUES(:numero,SYSDATETIME(),:tipo,:referencia,:observacion,'CONFIRMADO',:usuario,:token)");
            $stmt->execute(['numero'=>$number,'tipo'=>$header['tipo'],'referencia'=>$header['referencia'],'observacion'=>$header['observacion'],'usuario'=>$header['usuario'],'token'=>$token]);
            $movementId=(int)$db->lastInsertId();
            foreach($details as $detail){
                $qty=(float)$detail['cantidad']; if($qty<=0) throw new \RuntimeException('La cantidad debe ser mayor que cero.');
                $lock=$db->prepare("SELECT StockActual FROM dbo.Existencias WITH (UPDLOCK,HOLDLOCK) WHERE IdProducto=:producto"); $lock->execute(['producto'=>$detail['producto']]); $previous=$lock->fetchColumn();
                if($previous===false){ if($nature==='S') throw new \RuntimeException('El producto no tiene existencia disponible.'); $previous=0; $db->prepare("INSERT dbo.Existencias(IdProducto,StockActual,FechaUltimaActualizacion) VALUES(:p,0,SYSDATETIME())")->execute(['p'=>$detail['producto']]); }
                $previous=(float)$previous; $new=$nature==='E'?$previous+$qty:$previous-$qty;
                if($new<0) throw new \RuntimeException('Stock insuficiente. Disponible: '.number_format($previous,2));
                $d=$db->prepare("INSERT dbo.MovimientoDetalle(IdMovimiento,IdProducto,Cantidad,StockAnterior,StockPosterior,Observacion) VALUES(:m,:p,:c,:a,:n,:o)");
                $d->execute(['m'=>$movementId,'p'=>$detail['producto'],'c'=>$qty,'a'=>$previous,'n'=>$new,'o'=>$detail['observacion']??null]);
                $db->prepare("UPDATE dbo.Existencias SET StockActual=:s,FechaUltimaActualizacion=SYSDATETIME() WHERE IdProducto=:p")->execute(['s'=>$new,'p'=>$detail['producto']]);
            }
            $db->commit(); return $number;
        } catch(\Throwable $e){ if($db->inTransaction())$db->rollBack(); throw $e; }
    }
}
