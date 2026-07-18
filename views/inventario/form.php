<?php use App\Helpers\Csrf; ?>
<h1 class="h3 mb-3">Registrar movimiento</h1>
<?php if (!empty($error)): ?><div class="alert alert-danger"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div><?php endif; ?>
<form method="post" action="index.php?route=inventario/guardar" class="card card-body shadow-sm">
<input type="hidden" name="csrf_token" value="<?= Csrf::token() ?>">
<input type="hidden" name="token_idempotencia" value="<?= htmlspecialchars($token, ENT_QUOTES, 'UTF-8') ?>">
<div class="row g-3">
<div class="col-md-6"><label class="form-label">Tipo</label><select class="form-select" name="tipo" required><?php foreach($types as $t): ?><option value="<?= (int)$t['IdTipoMovimiento'] ?>"><?= htmlspecialchars($t['Nombre']) ?> (<?= htmlspecialchars($t['Naturaleza']) ?>)</option><?php endforeach; ?></select></div>
<div class="col-md-6"><label class="form-label">Producto</label><select class="form-select" name="producto" required><?php foreach($products as $p): ?><option value="<?= (int)$p['IdProducto'] ?>"><?= htmlspecialchars($p['Codigo'].' - '.$p['Nombre'].' | Stock: '.$p['StockActual']) ?></option><?php endforeach; ?></select></div>
<div class="col-md-4"><label class="form-label">Cantidad</label><input class="form-control" type="number" name="cantidad" min="0.01" step="0.01" required></div>
<div class="col-md-8"><label class="form-label">Referencia</label><input class="form-control" name="referencia" maxlength="100"></div>
<div class="col-12"><label class="form-label">Observación</label><textarea class="form-control" name="observacion" maxlength="500"></textarea></div>
<div class="col-12"><label class="form-label">Observación del detalle</label><textarea class="form-control" name="detalle_observacion" maxlength="500"></textarea></div>
</div>
<div class="mt-3"><button class="btn btn-success">Confirmar movimiento</button></div>
</form>
