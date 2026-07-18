<?php
namespace App\Models;

use App\Helpers\Database;
use PDO;

final class User
{
    public static function findByLogin(string $login): ?array
    {
        $sql = "SELECT TOP 1 u.IdUsuario, u.NombreUsuario, u.Correo, u.Nombres, u.Apellidos,
                       u.PasswordHash, u.Estado, u.BloqueadoHasta, u.IntentosFallidos,
                       u.DebeCambiarPassword, r.IdRol, r.Nombre AS Rol
                FROM dbo.Usuarios u
                INNER JOIN dbo.UsuarioRol ur ON ur.IdUsuario = u.IdUsuario
                INNER JOIN dbo.Roles r ON r.IdRol = ur.IdRol AND r.Estado = 1
                WHERE (u.NombreUsuario = :loginUsuario OR u.Correo = :loginCorreo)";
        $stmt = Database::connection()->prepare($sql);
        $stmt->execute(['loginUsuario' => $login, 'loginCorreo' => $login]);
        return $stmt->fetch() ?: null;
    }

    public static function permissions(int $userId): array
    {
        $sql = "SELECT DISTINCT p.Codigo
                FROM dbo.UsuarioRol ur
                INNER JOIN dbo.RolPermiso rp ON rp.IdRol = ur.IdRol
                INNER JOIN dbo.Permisos p ON p.IdPermiso = rp.IdPermiso AND p.Estado = 1
                WHERE ur.IdUsuario = :id";
        $stmt = Database::connection()->prepare($sql);
        $stmt->execute(['id' => $userId]);
        return array_column($stmt->fetchAll(), 'Codigo');
    }

    public static function paginate(string $search = ''): array
    {
        $sql = "SELECT u.IdUsuario, u.NombreUsuario, u.Correo, u.Nombres, u.Apellidos, u.Estado,
                       u.BloqueadoHasta, u.IntentosFallidos, u.DebeCambiarPassword, u.UltimoAcceso,
                       u.FechaCreacion, r.Nombre AS Rol
                FROM dbo.Usuarios u
                INNER JOIN dbo.UsuarioRol ur ON ur.IdUsuario = u.IdUsuario
                INNER JOIN dbo.Roles r ON r.IdRol = ur.IdRol
                WHERE (:searchValue = '' OR u.NombreUsuario LIKE :likeUsuario OR u.Correo LIKE :likeCorreo
                       OR u.Nombres LIKE :likeNombres OR u.Apellidos LIKE :likeApellidos)
                ORDER BY u.IdUsuario DESC";
        $stmt = Database::connection()->prepare($sql);
        $like = '%' . $search . '%';
        $stmt->execute([
            'searchValue' => $search,
            'likeUsuario' => $like,
            'likeCorreo' => $like,
            'likeNombres' => $like,
            'likeApellidos' => $like,
        ]);
        return $stmt->fetchAll();
    }

    public static function find(int $id): ?array
    {
        $sql = "SELECT TOP 1 u.IdUsuario, u.NombreUsuario, u.Correo, u.Nombres, u.Apellidos,
                       u.Estado, u.DebeCambiarPassword, r.IdRol
                FROM dbo.Usuarios u
                INNER JOIN dbo.UsuarioRol ur ON ur.IdUsuario = u.IdUsuario
                INNER JOIN dbo.Roles r ON r.IdRol = ur.IdRol
                WHERE u.IdUsuario = :id";
        $stmt = Database::connection()->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch() ?: null;
    }

    public static function duplicate(string $username, string $email, ?int $excludeId = null): bool
    {
        $sql = "SELECT COUNT(*) FROM dbo.Usuarios
                WHERE (NombreUsuario = :username OR Correo = :email)" . ($excludeId ? " AND IdUsuario <> :id" : '');
        $params = ['username' => $username, 'email' => $email];
        if ($excludeId) $params['id'] = $excludeId;
        $stmt = Database::connection()->prepare($sql);
        $stmt->execute($params);
        return (int)$stmt->fetchColumn() > 0;
    }

    public static function create(array $data): int
    {
        $pdo = Database::connection();
        $pdo->beginTransaction();
        try {
            $sql = "INSERT INTO dbo.Usuarios
                    (NombreUsuario, Correo, Nombres, Apellidos, PasswordHash, Estado, DebeCambiarPassword)
                    OUTPUT INSERTED.IdUsuario
                    VALUES (:username, :email, :names, :surnames, :hash, 1, 1)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                'username' => $data['username'], 'email' => $data['email'],
                'names' => $data['names'], 'surnames' => $data['surnames'],
                'hash' => password_hash($data['password'], PASSWORD_ARGON2ID),
            ]);
            $id = (int)$stmt->fetchColumn();
            $role = $pdo->prepare("INSERT INTO dbo.UsuarioRol (IdUsuario, IdRol) VALUES (:user, :role)");
            $role->execute(['user' => $id, 'role' => $data['role_id']]);
            $pdo->commit();
            return $id;
        } catch (\Throwable $e) {
            if ($pdo->inTransaction()) $pdo->rollBack();
            throw $e;
        }
    }

    public static function update(int $id, array $data): void
    {
        $pdo = Database::connection();
        $pdo->beginTransaction();
        try {
            $params = [
                'id' => $id, 'username' => $data['username'], 'email' => $data['email'],
                'names' => $data['names'], 'surnames' => $data['surnames'],
                'state' => $data['state'],
            ];
            $passwordSql = '';
            if ($data['password'] !== '') {
                $passwordSql = ', PasswordHash = :hash, DebeCambiarPassword = 1';
                $params['hash'] = password_hash($data['password'], PASSWORD_ARGON2ID);
            }
            $stmt = $pdo->prepare("UPDATE dbo.Usuarios SET NombreUsuario=:username, Correo=:email,
                Nombres=:names, Apellidos=:surnames, Estado=:state, FechaModificacion=SYSDATETIME()
                {$passwordSql} WHERE IdUsuario=:id");
            $stmt->execute($params);
            $pdo->prepare("DELETE FROM dbo.UsuarioRol WHERE IdUsuario = :id")->execute(['id' => $id]);
            $pdo->prepare("INSERT INTO dbo.UsuarioRol (IdUsuario, IdRol) VALUES (:user, :role)")
                ->execute(['user' => $id, 'role' => $data['role_id']]);
            $pdo->commit();
        } catch (\Throwable $e) {
            if ($pdo->inTransaction()) $pdo->rollBack();
            throw $e;
        }
    }

    public static function toggle(int $id, bool $state): void
    {
        $stmt = Database::connection()->prepare("UPDATE dbo.Usuarios SET Estado=:state, FechaModificacion=SYSDATETIME() WHERE IdUsuario=:id");
        $stmt->execute(['state' => $state ? 1 : 0, 'id' => $id]);
    }

    public static function unlock(int $id): void
    {
        $stmt = Database::connection()->prepare("UPDATE dbo.Usuarios SET IntentosFallidos=0, BloqueadoHasta=NULL WHERE IdUsuario=:id");
        $stmt->execute(['id' => $id]);
    }

    public static function passwordHash(int $id): ?string
    {
        $stmt = Database::connection()->prepare("SELECT PasswordHash FROM dbo.Usuarios WHERE IdUsuario = :id");
        $stmt->execute(['id' => $id]);
        $hash = $stmt->fetchColumn();
        return is_string($hash) ? $hash : null;
    }

    public static function existsActiveRole(int $roleId): bool
    {
        $stmt = Database::connection()->prepare("SELECT COUNT(*) FROM dbo.Roles WHERE IdRol = :id AND Estado = 1");
        $stmt->execute(['id' => $roleId]);
        return (int)$stmt->fetchColumn() > 0;
    }

    public static function changeOwnPassword(int $id, string $newPassword): void
    {
        $stmt = Database::connection()->prepare("UPDATE dbo.Usuarios SET PasswordHash=:hash, DebeCambiarPassword=0,
            IntentosFallidos=0, BloqueadoHasta=NULL, FechaModificacion=SYSDATETIME() WHERE IdUsuario=:id");
        $stmt->execute(['hash' => password_hash($newPassword, PASSWORD_ARGON2ID), 'id' => $id]);
    }

    public static function resetFailedAttempts(int $id): void
    {
        Database::connection()->prepare("UPDATE dbo.Usuarios SET IntentosFallidos=0, BloqueadoHasta=NULL, UltimoAcceso=SYSDATETIME() WHERE IdUsuario=:id")
            ->execute(['id' => $id]);
    }

    public static function registerFailedAttempt(?int $id): void
    {
        if (!$id) return;
        Database::connection()->prepare("UPDATE dbo.Usuarios SET IntentosFallidos=IntentosFallidos+1,
            BloqueadoHasta=CASE WHEN IntentosFallidos+1>=5 THEN DATEADD(MINUTE,15,SYSDATETIME()) ELSE BloqueadoHasta END
            WHERE IdUsuario=:id")->execute(['id' => $id]);
    }
}
