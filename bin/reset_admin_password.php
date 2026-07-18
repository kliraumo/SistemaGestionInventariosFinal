<?php
declare(strict_types=1);
require dirname(__DIR__) . '/vendor/autoload.php';
Dotenv\Dotenv::createImmutable(dirname(__DIR__))->safeLoad();
use App\Helpers\Database;
$password = $argv[1] ?? '';
if (strlen($password) < 8 || !preg_match('/[A-Z]/',$password) || !preg_match('/[a-z]/',$password) || !preg_match('/\d/',$password) || !preg_match('/[^A-Za-z0-9]/',$password)) {
    fwrite(STDERR, "Uso: php bin/reset_admin_password.php \"NuevaClave123*\"\n"); exit(1);
}
$stmt=Database::connection()->prepare("UPDATE dbo.Usuarios SET PasswordHash=:hash, DebeCambiarPassword=1, IntentosFallidos=0, BloqueadoHasta=NULL WHERE NombreUsuario='admin'");
$stmt->execute(['hash'=>password_hash($password,PASSWORD_ARGON2ID)]);
echo $stmt->rowCount() ? "Contraseña de admin actualizada.\n" : "No se encontró el usuario admin.\n";
