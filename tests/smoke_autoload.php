<?php
declare(strict_types=1);

require dirname(__DIR__) . '/vendor/autoload.php';

$classes = [
    App\Helpers\Database::class,
    App\Helpers\Csrf::class,
    App\Helpers\Authorization::class,
    App\Models\User::class,
    App\Controllers\AuthController::class,
];

$missing = [];
foreach ($classes as $class) {
    if (!class_exists($class)) {
        $missing[] = $class;
    }
}

if ($missing !== []) {
    fwrite(STDERR, "Clases no encontradas:\n- " . implode("\n- ", $missing) . "\n");
    exit(1);
}

echo "Autoload SIGI correcto.\n";
