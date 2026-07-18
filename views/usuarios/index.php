<?php
use App\Helpers\Authorization;
use App\Helpers\Csrf;
?>
<div class="d-flex justify-content-between align-items-center mb-4">
  <div><h1 class="h3 mb-1">Usuarios</h1><p class="text-secondary mb-0">Administración de accesos, roles y bloqueos.</p></div>
  <?php if (Authorization::can('usuarios.crear')): ?><a class="btn btn-primary" href="index.php?route=usuarios/crear">Nuevo usuario</a><?php endif; ?>
</div>
<form class="row g-2 mb-3" method="get">
  <input type="hidden" name="route" value="usuarios">
  <div class="col-md-5"><input class="form-control" name="q" maxlength="150" value="<?= htmlspecialchars($search, ENT_QUOTES, 'UTF-8') ?>" placeholder="Buscar por usuario, nombre o correo"></div>
  <div class="col-auto"><button class="btn btn-outline-primary">Buscar</button></div>
</form>
<div class="card shadow-sm"><div class="table-responsive"><table class="table table-hover align-middle mb-0">
<thead><tr><th>Usuario</th><th>Nombre</th><th>Rol</th><th>Estado</th><th>Último acceso</th><th class="text-end">Acciones</th></tr></thead>
<tbody>
<?php foreach ($users as $item): ?>
<tr>
<td><strong><?= htmlspecialchars($item['NombreUsuario'], ENT_QUOTES, 'UTF-8') ?></strong><div class="small text-secondary"><?= htmlspecialchars($item['Correo'], ENT_QUOTES, 'UTF-8') ?></div></td>
<td><?= htmlspecialchars($item['Nombres'].' '.$item['Apellidos'], ENT_QUOTES, 'UTF-8') ?></td>
<td><span class="badge text-bg-secondary"><?= htmlspecialchars($item['Rol'], ENT_QUOTES, 'UTF-8') ?></span></td>
<td><?php if (!(int)$item['Estado']): ?><span class="badge text-bg-danger">Inactivo</span><?php elseif (!empty($item['BloqueadoHasta']) && strtotime($item['BloqueadoHasta']) > time()): ?><span class="badge text-bg-warning">Bloqueado</span><?php else: ?><span class="badge text-bg-success">Activo</span><?php endif; ?></td>
<td><?= $item['UltimoAcceso'] ? htmlspecialchars(date('d/m/Y H:i', strtotime($item['UltimoAcceso'])), ENT_QUOTES, 'UTF-8') : 'Nunca' ?></td>
<td class="text-end text-nowrap">
<?php if (Authorization::can('usuarios.editar')): ?><a class="btn btn-sm btn-outline-primary" href="index.php?route=usuarios/editar&id=<?= (int)$item['IdUsuario'] ?>">Editar</a><?php endif; ?>
<?php if (Authorization::can('usuarios.desbloquear') && !empty($item['BloqueadoHasta'])): ?><form class="d-inline" method="post" action="index.php?route=usuarios/desbloquear"><input type="hidden" name="csrf_token" value="<?= Csrf::token() ?>"><input type="hidden" name="id" value="<?= (int)$item['IdUsuario'] ?>"><button class="btn btn-sm btn-outline-warning">Desbloquear</button></form><?php endif; ?>
<?php if (Authorization::can('usuarios.desactivar') && (int)$item['IdUsuario'] !== (int)$_SESSION['user']['id']): ?><form class="d-inline" method="post" action="index.php?route=usuarios/estado" onsubmit="return confirm('¿Confirma el cambio de estado?')"><input type="hidden" name="csrf_token" value="<?= Csrf::token() ?>"><input type="hidden" name="id" value="<?= (int)$item['IdUsuario'] ?>"><input type="hidden" name="state" value="<?= (int)$item['Estado'] ? 0 : 1 ?>"><button class="btn btn-sm btn-outline-<?= (int)$item['Estado'] ? 'danger' : 'success' ?>"><?= (int)$item['Estado'] ? 'Desactivar' : 'Activar' ?></button></form><?php endif; ?>
</td></tr>
<?php endforeach; ?>
<?php if (!$users): ?><tr><td colspan="6" class="text-center py-4 text-secondary">No se encontraron usuarios.</td></tr><?php endif; ?>
</tbody></table></div></div>
