<?php
namespace App\Helpers;

final class Audit
{
    public static function log(
        string $action,
        string $module,
        string $result = 'EXITOSO',
        ?string $entity = null,
        string|int|null $entityId = null,
        mixed $before = null,
        mixed $after = null
    ): void {
        try {
            $sql = "INSERT INTO dbo.Auditoria
                    (IdUsuario, Accion, Modulo, Entidad, IdEntidad, DatosAnteriores, DatosNuevos,
                     DireccionIP, AgenteUsuario, Resultado)
                    VALUES (:userId, :action, :module, :entity, :entityId, :beforeData, :afterData,
                            :ip, :agent, :result)";
            $stmt = Database::connection()->prepare($sql);
            $stmt->execute([
                'userId' => $_SESSION['user']['id'] ?? null,
                'action' => mb_substr($action, 0, 80),
                'module' => mb_substr($module, 0, 80),
                'entity' => $entity ? mb_substr($entity, 0, 80) : null,
                'entityId' => $entityId !== null ? mb_substr((string)$entityId, 0, 80) : null,
                'beforeData' => self::json($before),
                'afterData' => self::json($after),
                'ip' => mb_substr($_SERVER['REMOTE_ADDR'] ?? '', 0, 45),
                'agent' => mb_substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 500),
                'result' => mb_substr($result, 0, 30),
            ]);
        } catch (\Throwable $e) {
            error_log('Audit error: ' . $e->getMessage());
        }
    }

    private static function json(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }
        $encoded = json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_INVALID_UTF8_SUBSTITUTE);
        return $encoded === false ? null : $encoded;
    }
}
