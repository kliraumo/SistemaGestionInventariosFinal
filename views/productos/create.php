<?php use App\Helpers\Csrf; ?>
<h1 class="h3 mb-3">Nuevo producto</h1>
<form method="post" action="index.php?route=productos/guardar" class="card card-body shadow-sm">
<input type="hidden" name="csrf_token" value="<?= Csrf::token() ?>">
<div class="row g-3">
<div class="col-md-4"><label class="form-label">Código</label><input class="form-control" name="codigo" pattern="[A-Za-z0-9_-]{2,50}" required></div>
<div class="col-md-8"><label class="form-label">Nombre</label><input class="form-control" name="nombre" maxlength="150" required></div>
<div class="col-md-6"><label class="form-label">Categoría</label><select class="form-select" name="categoria" required><?php foreach($categories as $c): ?><option value="<?= (int)$c['IdCategoria'] ?>"><?= htmlspecialchars($c['Nombre']) ?></option><?php endforeach; ?></select></div>
<div class="col-md-6"><label class="form-label">Unidad</label><select class="form-select" name="unidad" required><?php foreach($units as $u): ?><option value="<?= (int)$u['IdUnidad'] ?>"><?= htmlspecialchars($u['Nombre']) ?></option><?php endforeach; ?></select></div>
<div class="col-md-6"><label class="form-label">Stock mínimo</label><input class="form-control" type="number" min="0" step="0.01" name="stock_minimo" value="0" required></div>
<div class="col-md-6"><label class="form-label">Stock máximo</label><input class="form-control" type="number" min="0" step="0.01" name="stock_maximo" value="100" required></div>
<div class="col-12"><label class="form-label">Descripción</label><textarea class="form-control" name="descripcion" maxlength="500"></textarea></div>
</div>
<div class="mt-3"><button class="btn btn-primary">Guardar</button> <a class="btn btn-secondary" href="index.php?route=productos">Cancelar</a></div>
</form>
