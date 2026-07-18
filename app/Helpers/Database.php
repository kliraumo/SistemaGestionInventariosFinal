<?php
declare(strict_types=1);

namespace App\Helpers;

use PDO;
use PDOException;
use RuntimeException;

final class Database
{
    private static ?PDO $connection = null;

    private function __construct()
    {
    }

    public static function connection(): PDO
    {
        if (self::$connection instanceof PDO) {
            return self::$connection;
        }

        $host = trim((string) ($_ENV['DB_HOST'] ?? 'localhost'));
        $port = trim((string) ($_ENV['DB_PORT'] ?? '1433'));
        $database = trim((string) ($_ENV['DB_DATABASE'] ?? 'SIGI'));
        $username = (string) ($_ENV['DB_USERNAME'] ?? '');
        $password = (string) ($_ENV['DB_PASSWORD'] ?? '');
        $encrypt = filter_var($_ENV['DB_ENCRYPT'] ?? false, FILTER_VALIDATE_BOOL) ? 'yes' : 'no';

        if ($host === '' || $database === '') {
            throw new RuntimeException('La configuración de la base de datos está incompleta.');
        }

        // $server = $port !== '' ? "{$host},{$port}" : $host;
        $server = $port !== ''    ? $host . ',' . $port    : $host;
        $dsn = "sqlsrv:Server={$server};Database={$database};Encrypt={$encrypt};TrustServerCertificate=yes";

        try {
            self::$connection = new PDO($dsn, $username, $password, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);
        } catch (PDOException $exception) {
            error_log('SIGI database connection error: ' . $exception->getMessage());
            throw new RuntimeException(
                'No se pudo conectar con SQL Server. Revise el archivo .env y la extensión pdo_sqlsrv.',
                0,
                $exception
            );
        }

        return self::$connection;
    }

    public static function disconnect(): void
    {
        self::$connection = null;
    }
}
