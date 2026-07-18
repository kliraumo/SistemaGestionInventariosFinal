<?php
declare(strict_types=1);
use PHPUnit\Framework\TestCase;
use App\Validators\UserValidator;
final class UserValidatorTest extends TestCase
{
    public function test_datos_validos_no_generan_errores(): void
    {
        $errors=UserValidator::validate(['username'=>'bodeguero1','email'=>'bodega@sigi.local','names'=>'Usuario','surnames'=>'Bodega','password'=>'Clave123*','role_id'=>2],true);
        self::assertSame([], $errors);
    }
    public function test_usuario_invalido_es_rechazado(): void
    {
        $errors=UserValidator::validate(['username'=>'x','email'=>'bodega@sigi.local','names'=>'Usuario','surnames'=>'Bodega','password'=>'Clave123*','role_id'=>2],true);
        self::assertArrayHasKey('username',$errors);
    }
    public function test_correo_invalido_es_rechazado(): void
    {
        $errors=UserValidator::validate(['username'=>'usuario1','email'=>'correo-invalido','names'=>'Usuario','surnames'=>'Bodega','password'=>'Clave123*','role_id'=>2],true);
        self::assertArrayHasKey('email',$errors);
    }
    public function test_password_debil_es_rechazado(): void
    {
        self::assertFalse(UserValidator::strongPassword('12345678'));
    }
    public function test_password_seguro_es_aceptado(): void
    {
        self::assertTrue(UserValidator::strongPassword('Clave123*'));
    }
    public function test_password_igual_al_usuario_es_rechazado(): void
    {
        $errors=UserValidator::validate(['username'=>'Admin123*','email'=>'admin@sigi.local','names'=>'Admin','surnames'=>'Sistema','password'=>'Admin123*','role_id'=>1],true);
        self::assertArrayHasKey('password',$errors);
    }
    public function test_rol_vacio_es_rechazado(): void
    {
        $errors=UserValidator::validate(['username'=>'usuario1','email'=>'u@sigi.local','names'=>'Usuario','surnames'=>'Prueba','password'=>'Clave123*','role_id'=>0],true);
        self::assertArrayHasKey('role_id',$errors);
    }
}
