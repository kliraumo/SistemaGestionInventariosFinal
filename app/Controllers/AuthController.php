<?php
declare(strict_types=1);
namespace App\Controllers;
use App\Helpers\Audit;
use App\Helpers\Csrf;
use App\Helpers\Response;
use App\Helpers\View;
use App\Models\User;
use App\Services\AuthService;
final class AuthController
{
    public function show(): void
    {
        if (!empty($_SESSION['user'])) Response::redirect('index.php?route=dashboard');
        View::render('auth/login', ['title' => 'Iniciar sesión']);
    }
    public function login(): void
    {
        if (!Csrf::validate($_POST['csrf_token'] ?? null)) { http_response_code(419); exit('Solicitud inválida.'); }
        $login = trim((string)($_POST['login'] ?? ''));
        $password = (string)($_POST['password'] ?? '');
        if ($login === '' || mb_strlen($login) > 150 || $password === '' || strlen($password) > 128) {
            View::render('auth/login', ['title' => 'Iniciar sesión', 'error' => 'Credenciales incorrectas o acceso no disponible.']); return;
        }
        $result = (new AuthService())->authenticate($login, $password);
        if (!$result['ok']) {
            $user = $result['user'];
            Audit::log('LOGIN_FALLIDO', 'Seguridad', 'DENEGADO', 'Usuario', $user['IdUsuario'] ?? null, null, ['login' => $login]);
            usleep(350000);
            View::render('auth/login', ['title' => 'Iniciar sesión', 'error' => 'Credenciales incorrectas o acceso no disponible.']); return;
        }
        $user = $result['user'];
        session_regenerate_id(true);
        $_SESSION['user'] = [
            'id' => (int)$user['IdUsuario'], 'name' => trim($user['Nombres'] . ' ' . $user['Apellidos']),
            'username' => $user['NombreUsuario'], 'role' => $user['Rol'],
            'permissions' => User::permissions((int)$user['IdUsuario']),
            'must_change_password' => (bool)$user['DebeCambiarPassword'],
        ];
        $_SESSION['last_activity'] = time();
        Audit::log('LOGIN_EXITOSO', 'Seguridad', 'EXITOSO', 'Usuario', $user['IdUsuario']);
        Response::redirect((bool)$user['DebeCambiarPassword'] ? 'index.php?route=perfil/password' : 'index.php?route=dashboard');
    }
    public function logout(): void
    {
        if (!Csrf::validate($_POST['csrf_token'] ?? null)) { http_response_code(419); exit('Solicitud inválida.'); }
        Audit::log('LOGOUT', 'Seguridad', 'EXITOSO', 'Usuario', $_SESSION['user']['id'] ?? null);
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $p = session_get_cookie_params(); setcookie(session_name(), '', time()-42000, $p['path'], $p['domain'], $p['secure'], $p['httponly']);
        }
        session_destroy(); Response::redirect('index.php?route=login');
    }
}
