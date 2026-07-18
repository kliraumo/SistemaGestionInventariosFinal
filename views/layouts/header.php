<?php
use App\Helpers\{Authorization,Csrf,Flash};
$flashMessages=Flash::pull();
$current=trim((string)($_GET['route']??'dashboard'),'/');
function activeRoute(string $route,string $current): string { return ($current===$route||str_starts_with($current,$route.'/'))?'active':''; }
?>
<!doctype html><html lang="es"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= htmlspecialchars($title??'SIGI',ENT_QUOTES,'UTF-8') ?> | SIGI</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
<link href="assets/css/app.css" rel="stylesheet"></head><body>
<?php if(!empty($_SESSION['user'])): ?>
<div class="app-shell">
<aside class="sidebar" id="sidebar">
 <a class="brand" href="index.php?route=dashboard"><span class="brand-icon"><i class="bi bi-boxes"></i></span><span><strong>SIGI</strong><small>Control de Inventario</small></span></a>
 <nav class="sidebar-nav">
  <a class="nav-item <?=activeRoute('dashboard',$current)?>" href="index.php?route=dashboard"><i class="bi bi-grid-1x2-fill"></i><span>Dashboard</span></a>
  <div class="nav-section">SEGURIDAD</div>
  <?php if(Authorization::can('usuarios.ver')):?><a class="nav-item <?=activeRoute('usuarios',$current)?>" href="index.php?route=usuarios"><i class="bi bi-people"></i><span>Usuarios</span></a><?php endif;?>
  <?php if(Authorization::can('roles.ver')):?><a class="nav-item <?=activeRoute('roles',$current)?>" href="index.php?route=roles"><i class="bi bi-shield-lock"></i><span>Roles y permisos</span></a><?php endif;?>
  <div class="nav-section">CATÁLOGOS</div>
  <?php if(Authorization::can('categorias.ver')):?><a class="nav-item <?=activeRoute('categorias',$current)?>" href="index.php?route=categorias"><i class="bi bi-tags"></i><span>Categorías</span></a><?php endif;?>
  <?php if(Authorization::can('productos.ver')):?><a class="nav-item <?=activeRoute('productos',$current)?>" href="index.php?route=productos"><i class="bi bi-box-seam"></i><span>Productos</span></a><?php endif;?>
  <?php if(Authorization::can('proveedores.ver')):?><a class="nav-item <?=activeRoute('proveedores',$current)?>" href="index.php?route=proveedores"><i class="bi bi-truck"></i><span>Proveedores</span></a><?php endif;?>
  <div class="nav-section">INVENTARIO</div>
  <?php if(Authorization::can('inventario.entrada')):?><a class="nav-item <?=activeRoute('inventario/entrada',$current)?>" href="index.php?route=inventario/entrada"><i class="bi bi-arrow-down-circle"></i><span>Registrar entrada</span></a><?php endif;?>
  <?php if(Authorization::can('inventario.salida')):?><a class="nav-item <?=activeRoute('inventario/salida',$current)?>" href="index.php?route=inventario/salida"><i class="bi bi-arrow-up-circle"></i><span>Registrar salida</span></a><?php endif;?>
  <?php if(Authorization::can('inventario.stock')):?><a class="nav-item <?=activeRoute('inventario/stock',$current)?>" href="index.php?route=inventario/stock"><i class="bi bi-boxes"></i><span>Existencias</span></a><?php endif;?>
  <?php if(Authorization::can('inventario.movimientos')):?><a class="nav-item <?=activeRoute('inventario/movimientos',$current)?>" href="index.php?route=inventario/movimientos"><i class="bi bi-clock-history"></i><span>Movimientos</span></a><?php endif;?>
  <div class="nav-section">REPORTES</div>
  <?php if(Authorization::can('reportes.inventario')):?><a class="nav-item <?=activeRoute('reportes',$current)?>" href="index.php?route=reportes/inventario-excel"><i class="bi bi-file-earmark-excel"></i><span>Inventario Excel</span></a><?php endif;?>
  <?php if(Authorization::can('auditoria.ver')):?><a class="nav-item <?=activeRoute('auditoria',$current)?>" href="index.php?route=auditoria"><i class="bi bi-shield-check"></i><span>Auditoría</span></a><?php endif;?>
 </nav>
</aside>
<div class="app-main">
<header class="topbar"><button class="icon-btn" id="sidebarToggle" type="button" aria-label="Abrir menú"><i class="bi bi-list"></i></button><div><h1><?=htmlspecialchars($title??'SIGI',ENT_QUOTES,'UTF-8')?></h1><small>Gestión centralizada de inventario</small></div><div class="topbar-actions ms-auto"><button class="icon-btn" id="themeToggle" type="button" title="Cambiar tema"><i class="bi bi-moon-stars"></i></button><div class="user-chip"><span class="avatar"><?=strtoupper(substr($_SESSION['user']['name']??'U',0,1))?></span><span class="d-none d-md-block"><strong><?=htmlspecialchars($_SESSION['user']['name'],ENT_QUOTES,'UTF-8')?></strong><small><?=htmlspecialchars($_SESSION['user']['role'],ENT_QUOTES,'UTF-8')?></small></span></div><form method="post" action="index.php?route=logout"><input type="hidden" name="csrf_token" value="<?=Csrf::token()?>"><button class="icon-btn text-danger" title="Cerrar sesión"><i class="bi bi-box-arrow-right"></i></button></form></div></header>
<main class="content-area">
<?php foreach($flashMessages as $flash):?><div class="alert alert-<?=htmlspecialchars($flash['type'],ENT_QUOTES,'UTF-8')?> alert-dismissible fade show shadow-sm" role="alert"><?=htmlspecialchars($flash['message'],ENT_QUOTES,'UTF-8')?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div><?php endforeach;?>
<?php else:?><main class="container py-4"><?php endif;?>
