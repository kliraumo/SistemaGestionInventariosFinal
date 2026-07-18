<?php
namespace App\Models;
use App\Helpers\Database;
final class Supplier
{
 public static function all(): array { return Database::connection()->query("SELECT * FROM dbo.Proveedores ORDER BY RazonSocial")->fetchAll(); }
 public static function find(int $id): ?array { $s=Database::connection()->prepare("SELECT * FROM dbo.Proveedores WHERE IdProveedor=:id");$s->execute(['id'=>$id]);return $s->fetch()?:null; }
 public static function save(array $d): void {
  $db=Database::connection();
  if(!empty($d['id'])) $s=$db->prepare("UPDATE dbo.Proveedores SET Identificacion=:identificacion,RazonSocial=:razon,NombreComercial=:comercial,Correo=:correo,Telefono=:telefono,Direccion=:direccion,Estado=:estado WHERE IdProveedor=:id");
  else { $s=$db->prepare("INSERT dbo.Proveedores(Identificacion,RazonSocial,NombreComercial,Correo,Telefono,Direccion,Estado,IdUsuarioCreacion) VALUES(:identificacion,:razon,:comercial,:correo,:telefono,:direccion,:estado,:usuario)"); unset($d['id']); }
  $s->execute($d);
 }
}
