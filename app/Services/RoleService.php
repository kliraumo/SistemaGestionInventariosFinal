<?php
declare(strict_types=1);
namespace App\Services;
use App\Helpers\Database;
use App\Models\Role;
final class RoleService
{
    public function updatePermissions(int $roleId, array $permissionIds): void
    {
        $clean = array_values(array_unique(array_filter(array_map('intval', $permissionIds), fn(int $id) => $id > 0)));
        if ($clean) {
            $marks = implode(',', array_fill(0, count($clean), '?'));
            $stmt = Database::connection()->prepare("SELECT IdPermiso FROM dbo.Permisos WHERE Estado = 1 AND IdPermiso IN ($marks)");
            $stmt->execute($clean);
            $clean = array_map('intval', array_column($stmt->fetchAll(), 'IdPermiso'));
        }
        Role::updatePermissions($roleId, $clean);
    }
}
