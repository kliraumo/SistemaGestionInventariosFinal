<?php use App\Helpers\Csrf; ?>
<div class="row justify-content-center"><div class="col-lg-6"><div class="card shadow-sm"><div class="card-body p-4">
<h1 class="h4 mb-3">Cambiar contraseña</h1>
<?php if (!empty($errors)): ?><div class="alert alert-danger"><ul class="mb-0"><?php foreach ($errors as $error): ?><li><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></li><?php endforeach; ?></ul></div><?php endif; ?>
<form method="post" action="index.php?route=perfil/password/guardar" novalidate>
<?= Csrf::field() ?>
<div class="mb-3"><label class="form-label">Contraseña actual</label><input type="password" name="current_password" class="form-control" required maxlength="128" autocomplete="current-password"></div>
<div class="mb-3"><label class="form-label">Nueva contraseña</label><input type="password" name="new_password" class="form-control" required maxlength="128" autocomplete="new-password"><div class="form-text">Mínimo 8 caracteres, con mayúscula, minúscula, número y símbolo.</div></div>
<div class="mb-3"><label class="form-label">Confirmar contraseña</label><input type="password" name="confirm_password" class="form-control" required maxlength="128" autocomplete="new-password"></div>
<button class="btn btn-primary">Actualizar contraseña</button>
</form></div></div></div></div>
