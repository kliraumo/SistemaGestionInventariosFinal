<?php
declare(strict_types=1);
use PHPUnit\Framework\TestCase;
use App\Helpers\Csrf;
final class CsrfTest extends TestCase
{
    protected function setUp(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) session_start();
        $_SESSION=[];
    }
    public function test_token_generado_es_valido(): void
    {
        $token=Csrf::token(); self::assertTrue(Csrf::validate($token));
    }
    public function test_token_incorrecto_es_rechazado(): void
    {
        Csrf::token(); self::assertFalse(Csrf::validate('token-falso'));
    }
    public function test_token_vacio_es_rechazado(): void
    {
        self::assertFalse(Csrf::validate(null));
    }
}
