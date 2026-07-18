<?php
namespace App\Helpers;

final class Authorization
{
    public static function can(string $permission): bool
    {
        if (empty($_SESSION['user'])) {
            return false;
        }
        if (($_SESSION['user']['role'] ?? '') === 'SUPERADMINISTRADOR') {
            return true;
        }
        return in_array($permission, $_SESSION['user']['permissions'] ?? [], true);
    }

    public static function require(string $permission): void
    {
        if (!self::can($permission)) {
            Audit::log('ACCESO_DENEGADO', 'Seguridad', 'DENEGADO', 'Permiso', $permission);
            http_response_code(403);
            View::render('errors/403', ['title' => 'Acceso denegado']);
            exit;
        }
    }
}
