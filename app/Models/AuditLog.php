<?php
declare(strict_types=1);
namespace App\Models;
use App\Helpers\Database;
final class AuditLog
{
    public static function recent(string $module = '', int $limit = 200): array
    {
        $limit = max(1, min($limit, 500));
        $sql = "SELECT TOP {$limit} a.IdAuditoria, a.Accion, a.Modulo, a.Entidad, a.IdEntidad,
                       a.DireccionIP, a.Resultado, a.FechaRegistro,
                       COALESCE(u.NombreUsuario, 'SISTEMA') AS Usuario
                FROM dbo.Auditoria a
                LEFT JOIN dbo.Usuarios u ON u.IdUsuario = a.IdUsuario
                WHERE (:moduleValue = '' OR a.Modulo = :moduleFilter)
                ORDER BY a.IdAuditoria DESC";
        $stmt = Database::connection()->prepare($sql);
        $stmt->execute(['moduleValue' => $module, 'moduleFilter' => $module]);
        return $stmt->fetchAll();
    }
}
