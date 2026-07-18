<?php
namespace App\Models;

use App\Helpers\Database;

final class Category
{
    public static function all(): array
    {
        return Database::connection()->query("SELECT IdCategoria, Codigo, Nombre, Descripcion, Estado, FechaCreacion FROM dbo.Categorias ORDER BY Nombre")->fetchAll();
    }

    public static function find(int $id): ?array
    {
        $s=Database::connection()->prepare("SELECT * FROM dbo.Categorias WHERE IdCategoria=:id");
        $s->execute(['id'=>$id]); return $s->fetch() ?: null;
    }

    public static function save(array $d): void
    {
        $db=Database::connection();
        if (!empty($d['id'])) {
            $s=$db->prepare("UPDATE dbo.Categorias SET Codigo=:codigo,Nombre=:nombre,Descripcion=:descripcion,Estado=:estado WHERE IdCategoria=:id");
        } else {
            $s=$db->prepare("INSERT dbo.Categorias(Codigo,Nombre,Descripcion,Estado) VALUES(:codigo,:nombre,:descripcion,:estado)");
            unset($d['id']);
        }
        $s->execute($d);
    }
}
