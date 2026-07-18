<?php
namespace App\Models;

use App\Helpers\Database;
use PDO;

final class Role
{
    public static function active(): array
    {
        return Database::connection()->query("SELECT IdRol, Nombre, Descripcion FROM dbo.Roles WHERE Estado = 1 ORDER BY Nombre")->fetchAll();
    }

    public static function allWithCounts(): array
    {
        $sql = "SELECT r.IdRol, r.Nombre, r.Descripcion, r.Estado,
                       COUNT(DISTINCT ur.IdUsuario) AS Usuarios,
                       COUNT(DISTINCT rp.IdPermiso) AS Permisos
                FROM dbo.Roles r
                LEFT JOIN dbo.UsuarioRol ur ON ur.IdRol = r.IdRol
                LEFT JOIN dbo.RolPermiso rp ON rp.IdRol = r.IdRol
                GROUP BY r.IdRol, r.Nombre, r.Descripcion, r.Estado
                ORDER BY r.Nombre";
        return Database::connection()->query($sql)->fetchAll();
    }

    public static function find(int $id): ?array
    {
        $stmt = Database::connection()->prepare("SELECT IdRol, Nombre, Descripcion, Estado FROM dbo.Roles WHERE IdRol = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch() ?: null;
    }

    public static function permissionsGrouped(): array
    {
        $rows = Database::connection()->query("SELECT IdPermiso, Codigo, Nombre, Modulo FROM dbo.Permisos WHERE Estado = 1 ORDER BY Modulo, Nombre")->fetchAll();
        $grouped = [];
        foreach ($rows as $row) {
            $grouped[$row['Modulo']][] = $row;
        }
        return $grouped;
    }

    public static function permissionIds(int $roleId): array
    {
        $stmt = Database::connection()->prepare("SELECT IdPermiso FROM dbo.RolPermiso WHERE IdRol = :id");
        $stmt->execute(['id' => $roleId]);
        return array_map('intval', array_column($stmt->fetchAll(), 'IdPermiso'));
    }

    public static function updatePermissions(int $roleId, array $permissionIds): void
    {
        $pdo = Database::connection();
        $pdo->beginTransaction();
        try {
            $delete = $pdo->prepare("DELETE FROM dbo.RolPermiso WHERE IdRol = :id");
            $delete->execute(['id' => $roleId]);
            $insert = $pdo->prepare("INSERT INTO dbo.RolPermiso (IdRol, IdPermiso) VALUES (:role, :permission)");
            foreach (array_unique(array_map('intval', $permissionIds)) as $permissionId) {
                if ($permissionId > 0) {
                    $insert->execute(['role' => $roleId, 'permission' => $permissionId]);
                }
            }
            $pdo->commit();
        } catch (\Throwable $e) {
            if ($pdo->inTransaction()) $pdo->rollBack();
            throw $e;
        }
    }
}
