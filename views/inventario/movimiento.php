<?php use App\Helpers\Csrf; $isEntry=$nature==='E'; ?>
<div class="page-heading"><div><h2><?= $isEntry?'Entrada de inventario':'Salida de inventario' ?></h2><p><?= $isEntry?'Incrementa el stock disponible del producto.':'Descuenta existencias con validación de stock suficiente.' ?></p></div><span class="badge <?= $isEntry?'text-bg-success':'text-bg-danger' ?> fs-6"><i class="bi <?= $isEntry?'bi-arrow-down-circle':'bi-arrow-up-circle' ?>"></i> <?= $isEntry?'ENTRADA':'SALIDA' ?></span></div>
<div class="card border-0 shadow-sm"><div class="card-body p-4">
<form method="post" action="index.php?route=inventario/guardar" class="row g-3">
<input type="hidden" name="csrf_token" value="<?=Csrf::token()?>"><input type="hidden" name="token_idempotencia" value="<?=htmlspecialchars($token,ENT_QUOTES,'UTF-8')?>"><input type="hidden" name="tipo" value="<?=(int)($type['IdTipoMovimiento']??0)?>"><input type="hidden" name="naturaleza" value="<?=htmlspecialchars($nature)?>">
<div class="col-md-8"><label class="form-label">Producto</label><select class="form-select" name="producto" required><option value="">Seleccione...</option><?php foreach($products as $p):?><option value="<?=(int)$p['IdProducto']?>"><?=htmlspecialchars($p['Codigo'].' - '.$p['Nombre'].' | Stock: '.number_format((float)$p['StockActual'],2))?></option><?php endforeach;?></select></div>
<div class="col-md-4"><label class="form-label">Cantidad</label><input class="form-control" type="number" name="cantidad" min="0.01" step="0.01" required></div>
<div class="col-md-6"><label class="form-label">Referencia</label><input class="form-control" name="referencia" maxlength="100" placeholder="Factura, orden o documento"></div>
<div class="col-md-6"><label class="form-label">Observación del detalle</label><input class="form-control" name="detalle_observacion" maxlength="300"></div>
<div class="col-12"><label class="form-label">Observación general</label><textarea class="form-control" name="observacion" rows="3" maxlength="500"></textarea></div>
<div class="col-12 d-flex gap-2"><button class="btn <?= $isEntry?'btn-success':'btn-danger' ?>"><i class="bi bi-check2-circle"></i> Confirmar <?= $isEntry?'entrada':'salida' ?></button><a class="btn btn-outline-secondary" href="index.php?route=inventario/stock">Consultar stock</a></div>
</form></div></div>
