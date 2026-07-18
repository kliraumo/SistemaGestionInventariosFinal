<?php
namespace App\Helpers;

final class Response
{
    public static function redirect(string $path): never
    {
        header('Location: ' . $path, true, 302);
        exit;
    }
}
