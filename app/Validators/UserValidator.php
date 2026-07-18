<?php
namespace App\Validators;

final class UserValidator
{
    public static function validate(array $data, bool $passwordRequired): array
    {
        $errors = [];
        $username = trim((string)($data['username'] ?? ''));
        $email = mb_strtolower(trim((string)($data['email'] ?? '')));
        $names = trim((string)($data['names'] ?? ''));
        $surnames = trim((string)($data['surnames'] ?? ''));
        $password = (string)($data['password'] ?? '');

        if (!preg_match('/^[A-Za-z0-9._-]{4,50}$/', $username)) {
            $errors['username'] = 'Use de 4 a 50 caracteres: letras, números, punto, guion o guion bajo.';
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL) || mb_strlen($email) > 150) {
            $errors['email'] = 'Ingrese un correo válido de hasta 150 caracteres.';
        }
        if (mb_strlen($names) < 2 || mb_strlen($names) > 100) {
            $errors['names'] = 'Los nombres deben tener entre 2 y 100 caracteres.';
        }
        if (mb_strlen($surnames) < 2 || mb_strlen($surnames) > 100) {
            $errors['surnames'] = 'Los apellidos deben tener entre 2 y 100 caracteres.';
        }
        if ($passwordRequired || $password !== '') {
            if (!self::strongPassword($password)) {
                $errors['password'] = 'Mínimo 8 caracteres, con mayúscula, minúscula, número y carácter especial.';
            }
            if (strcasecmp($password, $username) === 0) {
                $errors['password'] = 'La contraseña no puede ser igual al usuario.';
            }
        }
        if (empty($data['role_id']) || filter_var($data['role_id'], FILTER_VALIDATE_INT) === false) {
            $errors['role_id'] = 'Seleccione un rol válido.';
        }
        return $errors;
    }

    public static function strongPassword(string $password): bool
    {
        return strlen($password) >= 8
            && strlen($password) <= 128
            && preg_match('/[A-Z]/', $password)
            && preg_match('/[a-z]/', $password)
            && preg_match('/\d/', $password)
            && preg_match('/[^A-Za-z0-9]/', $password);
    }
}
