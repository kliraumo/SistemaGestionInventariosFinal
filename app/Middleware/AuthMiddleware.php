<?php
namespace App\Middleware;

use App\Helpers\Response;

final class AuthMiddleware
{
    public static function handle(bool $allowPasswordChange = false): void
    {
        if (empty($_SESSION['user'])) {
            Response::redirect('index.php?route=login');
        }
        $lastActivity = (int)($_SESSION['last_activity'] ?? time());
        $maxIdle = max(5, (int)($_ENV['SESSION_IDLE_MINUTES'] ?? 30)) * 60;
        if ((time() - $lastActivity) > $maxIdle) {
            $_SESSION = [];
            session_destroy();
            Response::redirect('index.php?route=login&expired=1');
        }
        $_SESSION['last_activity'] = time();
        if (!$allowPasswordChange && !empty($_SESSION['user']['must_change_password'])) {
            Response::redirect('index.php?route=perfil/password');
        }
    }
}
