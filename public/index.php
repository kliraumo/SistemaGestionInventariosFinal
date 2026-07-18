<?php
declare(strict_types=1);

require dirname(__DIR__) . '/vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__));
$dotenv->safeLoad();

session_name('SIGI_SESSION');
session_set_cookie_params([
    'lifetime' => 0, 'path' => '/',
    'secure' => (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off'),
    'httponly' => true, 'samesite' => 'Lax',
]);
ini_set('session.use_strict_mode', '1');
session_start();
date_default_timezone_set($_ENV['APP_TIMEZONE'] ?? 'America/Guayaquil');

header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: SAMEORIGIN');
header('Referrer-Policy: strict-origin-when-cross-origin');
header('Permissions-Policy: camera=(), microphone=(), geolocation=()');
header("Content-Security-Policy: default-src 'self'; style-src 'self' https://cdn.jsdelivr.net; script-src 'self' https://cdn.jsdelivr.net; img-src 'self' data:; font-src 'self' https://cdn.jsdelivr.net");
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');

$route = trim((string)($_GET['route'] ?? 'login'), '/');
$method = strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');

$routes = [
    'GET' => [
        'login' => [App\Controllers\AuthController::class, 'show', false],
        'dashboard' => [App\Controllers\DashboardController::class, 'index', true],
        'productos' => [App\Controllers\ProductController::class, 'index', true],
        'productos/crear' => [App\Controllers\ProductController::class, 'create', true],
        'productos/editar' => [App\Controllers\ProductController::class, 'edit', true],
        'categorias' => [App\Controllers\CategoryController::class, 'index', true],
        'proveedores' => [App\Controllers\SupplierController::class, 'index', true],
        'inventario/entrada' => [App\Controllers\InventoryController::class, 'entry', true],
        'inventario/salida' => [App\Controllers\InventoryController::class, 'exit', true],
        'inventario/stock' => [App\Controllers\InventoryController::class, 'stock', true],
        'inventario/movimientos' => [App\Controllers\InventoryController::class, 'movements', true],
        'reportes/inventario-excel' => [App\Controllers\ReportController::class, 'inventoryExcel', true],
        'reportes/movimientos-excel' => [App\Controllers\ReportController::class, 'movementsExcel', true],
        'usuarios' => [App\Controllers\UserController::class, 'index', true],
        'usuarios/crear' => [App\Controllers\UserController::class, 'create', true],
        'usuarios/editar' => [App\Controllers\UserController::class, 'edit', true],
        'roles' => [App\Controllers\RoleController::class, 'index', true],
        'auditoria' => [App\Controllers\AuditController::class, 'index', true],
        'roles/permisos' => [App\Controllers\RoleController::class, 'permissions', true],
        'perfil/password' => [App\Controllers\ProfileController::class, 'password', 'password'],
    ],
    'POST' => [
        'login' => [App\Controllers\AuthController::class, 'login', false],
        'logout' => [App\Controllers\AuthController::class, 'logout', true],
        'productos/guardar' => [App\Controllers\ProductController::class, 'save', true],
        'productos/estado' => [App\Controllers\ProductController::class, 'toggle', true],
        'categorias/guardar' => [App\Controllers\CategoryController::class, 'save', true],
        'proveedores/guardar' => [App\Controllers\SupplierController::class, 'save', true],
        'inventario/guardar' => [App\Controllers\InventoryController::class, 'store', true],
        'usuarios/guardar' => [App\Controllers\UserController::class, 'store', true],
        'usuarios/actualizar' => [App\Controllers\UserController::class, 'update', true],
        'usuarios/estado' => [App\Controllers\UserController::class, 'toggle', true],
        'usuarios/desbloquear' => [App\Controllers\UserController::class, 'unlock', true],
        'roles/permisos/guardar' => [App\Controllers\RoleController::class, 'updatePermissions', true],
        'perfil/password/guardar' => [App\Controllers\ProfileController::class, 'updatePassword', 'password'],
    ],
];

if (!isset($routes[$method][$route])) {
    http_response_code(404); echo 'Ruta no encontrada.'; exit;
}
[$controller, $action, $auth] = $routes[$method][$route];
if ($auth === true) App\Middleware\AuthMiddleware::handle();
if ($auth === 'password') App\Middleware\AuthMiddleware::handle(true);
(new $controller())->$action();
