<?php
declare(strict_types=1);
namespace App\Services;
use App\Models\User;
use App\Validators\UserValidator;
final class UserService
{
    public function validate(array $data, bool $passwordRequired, ?int $excludeId = null): array
    {
        $errors = UserValidator::validate($data, $passwordRequired);
        if (!User::existsActiveRole((int)($data['role_id'] ?? 0))) $errors['role_id'] = 'Seleccione un rol activo válido.';
        if (User::duplicate((string)$data['username'], (string)$data['email'], $excludeId)) $errors['duplicate'] = 'El usuario o correo ya se encuentra registrado.';
        return $errors;
    }
    public function changePassword(int $userId, string $current, string $new, string $confirm): array
    {
        $errors = []; $hash = User::passwordHash($userId);
        if (!$hash || !password_verify($current, $hash)) $errors[] = 'La contraseña actual no es correcta.';
        if (!UserValidator::strongPassword($new)) $errors[] = 'La nueva contraseña no cumple la política de seguridad.';
        if ($new !== $confirm) $errors[] = 'Las contraseñas no coinciden.';
        if ($hash && password_verify($new, $hash)) $errors[] = 'La nueva contraseña debe ser diferente de la actual.';
        if (!$errors) User::changeOwnPassword($userId, $new);
        return $errors;
    }
}
