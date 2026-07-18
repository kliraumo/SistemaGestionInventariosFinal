<?php
namespace App\Controllers;
use App\Helpers\{Authorization,Csrf,Flash,Response,View,Audit};
use App\Middleware\AuthMiddleware;
use App\Models\Category;
final class CategoryController
{
 public function index(): void { AuthMiddleware::handle();Authorization::require('categorias.ver');$id=(int)($_GET['id']??0);View::render('categorias/index',['title'=>'Categorías','categories'=>Category::all(),'editing'=>$id?Category::find($id):null]); }
 public function save(): void { AuthMiddleware::handle();Authorization::require(empty($_POST['id'])?'categorias.crear':'categorias.editar');if(!Csrf::validate($_POST['csrf_token']??null))exit('Solicitud inválida.');$codigo=strtoupper(trim((string)($_POST['codigo']??'')));$nombre=trim((string)($_POST['nombre']??''));if($codigo===''||mb_strlen($nombre)<2){Flash::add('danger','Código y nombre son obligatorios.');Response::redirect('index.php?route=categorias');}Category::save(['id'=>(int)($_POST['id']??0),'codigo'=>$codigo,'nombre'=>$nombre,'descripcion'=>trim((string)($_POST['descripcion']??'')),'estado'=>(int)($_POST['estado']??1)]);Audit::log('GUARDAR','Catálogos','EXITOSO','Categoria',$codigo);Flash::add('success','Categoría guardada correctamente.');Response::redirect('index.php?route=categorias'); }
}
