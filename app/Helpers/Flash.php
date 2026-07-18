<?php
namespace App\Helpers;

final class Flash
{
    public static function set(string $type, string $message): void
    {
        $_SESSION['flash'][] = ['type' => $type, 'message' => $message];
    }

    public static function add(string $type, string $message): void
    {
        self::set($type, $message);
    }

    public static function success(string $message): void { self::set('success', $message); }
    public static function danger(string $message): void { self::set('danger', $message); }
    public static function warning(string $message): void { self::set('warning', $message); }
    public static function info(string $message): void { self::set('info', $message); }

    public static function pull(): array
    {
        $messages = $_SESSION['flash'] ?? [];
        unset($_SESSION['flash']);
        return is_array($messages) ? $messages : [];
    }
}
