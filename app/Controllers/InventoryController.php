<?php
namespace App\Controllers;

use App\Helpers\{Authorization,Csrf,Flash,Response,View,Audit,Database};
use App\Middleware\AuthMiddleware;
use App\Models\Inventory;

final class InventoryController
{
    private function formData(string $nature): array
    {
        $db=Database::connection();
        $stmt=$db->prepare("SELECT TOP 1 IdTipoMovimiento,Nombre,Naturaleza FROM dbo.TiposMovimiento WHERE Naturaleza=:n AND Estado=1 ORDER BY IdTipoMovimiento"); $stmt->execute(['n'=>$nature]);
        return ['type'=>$stmt->fetch(),'products'=>Inventory::products(),'token'=>bin2hex(random_bytes(24))];
    }
    public function entry(): void { AuthMiddleware::handle(); Authorization::require('inventario.entrada'); View::render('inventario/movimiento',array_merge(['title'=>'Registrar entrada','nature'=>'E'],$this->formData('E'))); }
    public function exit(): void { AuthMiddleware::handle(); Authorization::require('inventario.salida'); View::render('inventario/movimiento',array_merge(['title'=>'Registrar salida','nature'=>'S'],$this->formData('S'))); }
    public function store(): void
    {
        AuthMiddleware::handle(); if(!Csrf::validate($_POST['csrf_token']??null)) exit('Solicitud inválida.');
        $nature=(string)($_POST['naturaleza']??''); Authorization::require($nature==='E'?'inventario.entrada':'inventario.salida');
        try {
            $number=Inventory::register(['tipo'=>(int)($_POST['tipo']??0),'referencia'=>trim((string)($_POST['referencia']??'')),'observacion'=>trim((string)($_POST['observacion']??'')),'usuario'=>(int)$_SESSION['user']['id'],'token'=>(string)($_POST['token_idempotencia']??'')],[['producto'=>(int)($_POST['producto']??0),'cantidad'=>(float)($_POST['cantidad']??0),'observacion'=>trim((string)($_POST['detalle_observacion']??''))]]);
            Audit::log($nature==='E'?'ENTRADA':'SALIDA','Inventario','EXITOSO','Movimiento',$number); Flash::success('Movimiento '.$number.' registrado correctamente.'); Response::redirect('index.php?route=inventario/movimientos');
        } catch(\Throwable $e){ Flash::danger($e->getMessage()); Response::redirect('index.php?route='.($nature==='E'?'inventario/entrada':'inventario/salida')); }
    }
    public function stock(): void
    {
        AuthMiddleware::handle(); Authorization::require('inventario.stock');
        $filters=['q'=>trim((string)($_GET['q']??'')),'categoria'=>(int)($_GET['categoria']??0),'estado'=>trim((string)($_GET['estado']??''))];
        $categories=Database::connection()->query("SELECT IdCategoria,Nombre FROM dbo.Categorias WHERE Estado=1 ORDER BY Nombre")->fetchAll();
        View::render('inventario/stock',['title'=>'Consulta de stock','rows'=>Inventory::stock($filters),'categories'=>$categories,'filters'=>$filters]);
    }
    public function movements(): void { AuthMiddleware::handle(); Authorization::require('inventario.movimientos'); View::render('inventario/movimientos',['title'=>'Movimientos de inventario','rows'=>Inventory::movements(200)]); }
}
