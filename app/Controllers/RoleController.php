<?php
namespace App\Controllers;

use App\Helpers\Audit;
use App\Helpers\Authorization;
use App\Helpers\Csrf;
use App\Helpers\Flash;
use App\Helpers\Response;
use App\Helpers\View;
use App\Models\Role;
use App\Services\RoleService;

final class RoleController
{
    public function index(): void
    {
        Authorization::require('roles.ver');
        View::render('roles/index', ['title' => 'Roles y permisos', 'roles' => Role::allWithCounts()]);
    }

    public function permissions(): void
    {
        Authorization::require('roles.editar');
        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        $role = $id ? Role::find($id) : null;
        if (!$role) { http_response_code(404); exit('Rol no encontrado.'); }
        View::render('roles/permissions', [
            'title' => 'Permisos del rol', 'role' => $role,
            'groups' => Role::permissionsGrouped(), 'selected' => Role::permissionIds($id),
        ]);
    }

    public function updatePermissions(): void
    {
        Authorization::require('roles.editar');
        if (!Csrf::validate($_POST['csrf_token'] ?? null)) { http_response_code(419); exit('Solicitud inválida.'); }
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        $role = $id ? Role::find($id) : null;
        if (!$role) { http_response_code(404); exit('Rol no encontrado.'); }
        $permissions = $_POST['permissions'] ?? [];
        (new RoleService())->updatePermissions($id, is_array($permissions) ? $permissions : []);
        Audit::log('ACTUALIZAR_PERMISOS', 'Roles', 'EXITOSO', 'Rol', $id, null, ['permisos' => $permissions]);
        Flash::set('success', 'Permisos actualizados correctamente.');
        Response::redirect('index.php?route=roles');
    }
}
