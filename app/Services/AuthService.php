<?php
declare(strict_types=1);
namespace App\Services;
use App\Helpers\Database;
use App\Models\User;
final class AuthService
{
    public function authenticate(string $login, string $password): array
    {
        $user = User::findByLogin($login);
        $blocked = $user && !empty($user['BloqueadoHasta']) && strtotime((string)$user['BloqueadoHasta']) > time();
        $valid = $user && !$blocked && (int)$user['Estado'] === 1 && password_verify($password, (string)$user['PasswordHash']);
        $this->recordAttempt($login, $user['IdUsuario'] ?? null, $valid);
        if (!$valid) {
            User::registerFailedAttempt(isset($user['IdUsuario']) ? (int)$user['IdUsuario'] : null);
            return ['ok' => false, 'user' => $user, 'blocked' => $blocked];
        }
        User::resetFailedAttempts((int)$user['IdUsuario']);
        return ['ok' => true, 'user' => $user, 'blocked' => false];
    }
    private function recordAttempt(string $login, int|string|null $userId, bool $success): void
    {
        try {
            $stmt = Database::connection()->prepare('INSERT INTO dbo.IntentosLogin (UsuarioIngresado, IdUsuario, DireccionIP, AgenteUsuario, Exitoso) VALUES (:login, :user, :ip, :agent, :success)');
            $stmt->execute([
                'login' => mb_substr($login, 0, 150), 'user' => $userId !== null ? (int)$userId : null,
                'ip' => mb_substr($_SERVER['REMOTE_ADDR'] ?? '', 0, 45),
                'agent' => mb_substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 500), 'success' => $success ? 1 : 0,
            ]);
        } catch (\Throwable $e) { error_log('Login attempt log error: ' . $e->getMessage()); }
    }
}
