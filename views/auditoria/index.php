<?php $esc = fn($v) => htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); ?>
<div class="d-flex justify-content-between align-items-center mb-3"><h1 class="h3 mb-0">Auditoría</h1></div>
<form class="row g-2 mb-3" method="get"><input type="hidden" name="route" value="auditoria"><div class="col-md-4"><input class="form-control" name="modulo" value="<?= $esc($module) ?>" placeholder="Filtrar por módulo"></div><div class="col-auto"><button class="btn btn-outline-primary">Buscar</button></div></form>
<div class="card shadow-sm"><div class="table-responsive"><table class="table table-hover mb-0"><thead><tr><th>Fecha</th><th>Usuario</th><th>Módulo</th><th>Acción</th><th>Entidad</th><th>Resultado</th><th>IP</th></tr></thead><tbody>
<?php foreach ($rows as $r): ?><tr><td><?= $esc($r['FechaRegistro']) ?></td><td><?= $esc($r['Usuario']) ?></td><td><?= $esc($r['Modulo']) ?></td><td><?= $esc($r['Accion']) ?></td><td><?= $esc(trim(($r['Entidad'] ?? '').' '.($r['IdEntidad'] ?? ''))) ?></td><td><span class="badge <?= $r['Resultado']==='EXITOSO'?'text-bg-success':'text-bg-danger' ?>"><?= $esc($r['Resultado']) ?></span></td><td><?= $esc($r['DireccionIP']) ?></td></tr><?php endforeach; ?>
<?php if (!$rows): ?><tr><td colspan="7" class="text-center py-4 text-muted">No existen registros.</td></tr><?php endif; ?>
</tbody></table></div></div>
