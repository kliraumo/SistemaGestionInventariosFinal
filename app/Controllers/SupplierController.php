<?php
namespace App\Controllers;
use App\Helpers\{Authorization,Csrf,Flash,Response,View,Audit};
use App\Middleware\AuthMiddleware;
use App\Models\Supplier;
final class SupplierController
{
 public function index(): void { AuthMiddleware::handle();Authorization::require('proveedores.ver');$id=(int)($_GET['id']??0);View::render('proveedores/index',['title'=>'Proveedores','suppliers'=>Supplier::all(),'editing'=>$id?Supplier::find($id):null]); }
 public function save(): void { AuthMiddleware::handle();Authorization::require(empty($_POST['id'])?'proveedores.crear':'proveedores.editar');if(!Csrf::validate($_POST['csrf_token']??null))exit('Solicitud inválida.');$razon=trim((string)($_POST['razon']??''));if(mb_strlen($razon)<2){Flash::add('danger','La razón social es obligatoria.');Response::redirect('index.php?route=proveedores');}$d=['id'=>(int)($_POST['id']??0),'identificacion'=>trim((string)($_POST['identificacion']??'')),'razon'=>$razon,'comercial'=>trim((string)($_POST['comercial']??'')),'correo'=>trim((string)($_POST['correo']??'')),'telefono'=>trim((string)($_POST['telefono']??'')),'direccion'=>trim((string)($_POST['direccion']??'')),'estado'=>(int)($_POST['estado']??1)];if(empty($d['id']))$d['usuario']=(int)$_SESSION['user']['id'];Supplier::save($d);Audit::log('GUARDAR','Catálogos','EXITOSO','Proveedor',$razon);Flash::add('success','Proveedor guardado correctamente.');Response::redirect('index.php?route=proveedores'); }
}
