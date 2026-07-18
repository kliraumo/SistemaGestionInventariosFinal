<?php
namespace App\Controllers;

use App\Helpers\Audit;
use App\Helpers\Authorization;
use App\Helpers\Csrf;
use App\Helpers\Flash;
use App\Helpers\Response;
use App\Helpers\View;
use App\Models\Role;
use App\Models\User;
use App\Services\UserService;

final class UserController
{
    public function index(): void
    {
        Authorization::require('usuarios.ver');
        $search = trim((string)($_GET['q'] ?? ''));
        View::render('usuarios/index', [
            'title' => 'Usuarios',
            'users' => User::paginate($search),
            'search' => $search,
        ]);
    }

    public function create(): void
    {
        Authorization::require('usuarios.crear');
        View::render('usuarios/form', [
            'title' => 'Nuevo usuario', 'roles' => Role::active(), 'user' => null, 'errors' => [],
        ]);
    }

    public function store(): void
    {
        Authorization::require('usuarios.crear');
        $this->validateCsrf();
        $data = $this->input();
        $errors = (new UserService())->validate($data, true);
        if ($errors) {
            View::render('usuarios/form', ['title' => 'Nuevo usuario', 'roles' => Role::active(), 'user' => $data, 'errors' => $errors]);
            return;
        }
        try {
            $id = User::create($data);
            Audit::log('CREAR', 'Usuarios', 'EXITOSO', 'Usuario', $id, null, ['usuario' => $data['username'], 'correo' => $data['email'], 'rol' => $data['role_id']]);
            Flash::set('success', 'Usuario creado correctamente. La contraseña deberá cambiarse en el primer acceso.');
            Response::redirect('index.php?route=usuarios');
        } catch (\Throwable $e) {
            error_log($e->getMessage());
            Audit::log('CREAR', 'Usuarios', 'ERROR', 'Usuario', null);
            Flash::set('danger', 'No se pudo crear el usuario.');
            Response::redirect('index.php?route=usuarios/crear');
        }
    }

    public function edit(): void
    {
        Authorization::require('usuarios.editar');
        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        $user = $id ? User::find($id) : null;
        if (!$user) {
            http_response_code(404); exit('Usuario no encontrado.');
        }
        View::render('usuarios/form', ['title' => 'Editar usuario', 'roles' => Role::active(), 'user' => $user, 'errors' => []]);
    }

    public function update(): void
    {
        Authorization::require('usuarios.editar');
        $this->validateCsrf();
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        $before = $id ? User::find($id) : null;
        if (!$before) { http_response_code(404); exit('Usuario no encontrado.'); }
        $data = $this->input();
        $errors = (new UserService())->validate($data, false, $id);
        if ($errors) {
            $data['IdUsuario'] = $id;
            View::render('usuarios/form', ['title' => 'Editar usuario', 'roles' => Role::active(), 'user' => $data, 'errors' => $errors]);
            return;
        }
        User::update($id, $data);
        Audit::log('EDITAR', 'Usuarios', 'EXITOSO', 'Usuario', $id, $before, ['usuario' => $data['username'], 'correo' => $data['email'], 'rol' => $data['role_id'], 'estado' => $data['state']]);
        Flash::set('success', 'Usuario actualizado correctamente.');
        Response::redirect('index.php?route=usuarios');
    }

    public function toggle(): void
    {
        Authorization::require('usuarios.desactivar');
        $this->validateCsrf();
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        $state = filter_input(INPUT_POST, 'state', FILTER_VALIDATE_BOOL, FILTER_NULL_ON_FAILURE);
        if (!$id || $state === null || $id === (int)($_SESSION['user']['id'] ?? 0)) {
            Flash::set('danger', 'Operación no permitida.');
            Response::redirect('index.php?route=usuarios');
        }
        User::toggle($id, $state);
        Audit::log($state ? 'ACTIVAR' : 'DESACTIVAR', 'Usuarios', 'EXITOSO', 'Usuario', $id);
        Flash::set('success', 'Estado del usuario actualizado.');
        Response::redirect('index.php?route=usuarios');
    }

    public function unlock(): void
    {
        Authorization::require('usuarios.desbloquear');
        $this->validateCsrf();
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        if ($id) {
            User::unlock($id);
            Audit::log('DESBLOQUEAR', 'Usuarios', 'EXITOSO', 'Usuario', $id);
            Flash::set('success', 'Usuario desbloqueado.');
        }
        Response::redirect('index.php?route=usuarios');
    }

    private function input(): array
    {
        return [
            'username' => trim((string)($_POST['username'] ?? '')),
            'email' => mb_strtolower(trim((string)($_POST['email'] ?? ''))),
            'names' => trim((string)($_POST['names'] ?? '')),
            'surnames' => trim((string)($_POST['surnames'] ?? '')),
            'password' => (string)($_POST['password'] ?? ''),
            'role_id' => (int)($_POST['role_id'] ?? 0),
            'state' => isset($_POST['state']) ? 1 : 0,
        ];
    }

    private function validateCsrf(): void
    {
        if (!Csrf::validate($_POST['csrf_token'] ?? null)) {
            http_response_code(419); exit('Solicitud inválida.');
        }
    }
}
