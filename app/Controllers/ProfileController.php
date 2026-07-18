<?php
declare(strict_types=1);
namespace App\Controllers;
use App\Helpers\Audit;
use App\Helpers\Csrf;
use App\Helpers\Flash;
use App\Helpers\Response;
use App\Helpers\View;
use App\Services\UserService;
final class ProfileController
{
    public function password(): void { View::render('perfil/password', ['title' => 'Cambiar contraseña', 'errors' => []]); }
    public function updatePassword(): void
    {
        if (!Csrf::validate($_POST['csrf_token'] ?? null)) { http_response_code(419); exit('Solicitud inválida.'); }
        $errors = (new UserService())->changePassword(
            (int)$_SESSION['user']['id'], (string)($_POST['current_password'] ?? ''),
            (string)($_POST['new_password'] ?? ''), (string)($_POST['confirm_password'] ?? '')
        );
        if ($errors) { View::render('perfil/password', ['title' => 'Cambiar contraseña', 'errors' => $errors]); return; }
        $_SESSION['user']['must_change_password'] = false;
        Audit::log('CAMBIO_CONTRASENA', 'Seguridad', 'EXITOSO', 'Usuario', $_SESSION['user']['id']);
        Flash::set('success', 'Contraseña actualizada correctamente.');
        Response::redirect('index.php?route=dashboard');
    }
}
