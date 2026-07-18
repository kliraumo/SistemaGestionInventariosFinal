<?php
return [
    'name' => 'SIGI',
    'timezone' => $_ENV['APP_TIMEZONE'] ?? 'America/Guayaquil',
    'debug' => filter_var($_ENV['APP_DEBUG'] ?? false, FILTER_VALIDATE_BOOL),
    'session_idle_minutes' => (int)($_ENV['SESSION_IDLE_MINUTES'] ?? 30),
];
