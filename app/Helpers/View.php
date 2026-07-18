<?php
namespace App\Helpers;

final class View
{
    public static function render(string $view, array $data = []): void
    {
        extract($data, EXTR_SKIP);
        $viewFile = dirname(__DIR__, 2) . '/views/' . $view . '.php';
        if (!is_file($viewFile)) {
            http_response_code(500);
            echo 'Vista no encontrada.';
            return;
        }
        require dirname(__DIR__, 2) . '/views/layouts/header.php';
        require $viewFile;
        require dirname(__DIR__, 2) . '/views/layouts/footer.php';
    }
}
