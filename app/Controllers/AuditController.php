<?php
declare(strict_types=1);
namespace App\Controllers;
use App\Helpers\Authorization;
use App\Helpers\View;
use App\Models\AuditLog;
final class AuditController
{
    public function index(): void
    {
        Authorization::require('auditoria.ver');
        $module = trim((string)($_GET['modulo'] ?? ''));
        View::render('auditoria/index', ['title' => 'Auditoría', 'rows' => AuditLog::recent($module), 'module' => $module]);
    }
}
