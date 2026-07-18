<?php
declare(strict_types=1);

return [
    'host' => $_ENV['DB_HOST'] ?? 'localhost',
    'port' => $_ENV['DB_PORT'] ?? '1433',
    'database' => $_ENV['DB_DATABASE'] ?? 'SIGI',
    'username' => $_ENV['DB_USERNAME'] ?? '',
    'password' => $_ENV['DB_PASSWORD'] ?? '',
    'encrypt' => filter_var($_ENV['DB_ENCRYPT'] ?? false, FILTER_VALIDATE_BOOL),
];
