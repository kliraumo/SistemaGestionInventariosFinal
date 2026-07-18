<?php
namespace App\Controllers;

use App\Helpers\Authorization;
use App\Middleware\AuthMiddleware;
use App\Models\Inventory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

final class ReportController
{
    public function inventoryExcel(): void
    {
        AuthMiddleware::handle(); Authorization::require('reportes.inventario');
        $rows=Inventory::stock(['q'=>'','categoria'=>0,'estado'=>'']);
        $book=new Spreadsheet(); $sheet=$book->getActiveSheet(); $sheet->setTitle('Inventario');
        $headers=['Código','Código de barras','Producto','Categoría','Unidad','Stock actual','Stock mínimo','Stock máximo','Estado'];
        $sheet->fromArray($headers,null,'A1'); $r=2;
        foreach($rows as $x){ $sheet->fromArray([$x['Codigo'],$x['CodigoBarras'],$x['Nombre'],$x['Categoria'],$x['Unidad'],(float)$x['StockActual'],(float)$x['StockMinimo'],(float)$x['StockMaximo'],$x['EstadoStock']],null,'A'.$r++); }
        $sheet->getStyle('A1:I1')->getFont()->setBold(true); $sheet->getStyle('A1:I1')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FF2563EB'); $sheet->getStyle('A1:I1')->getFont()->getColor()->setARGB('FFFFFFFF');
        $sheet->freezePane('A2'); $sheet->setAutoFilter('A1:I'.max(1,$r-1)); foreach(range('A','I') as $c)$sheet->getColumnDimension($c)->setAutoSize(true); $sheet->getStyle('F2:H'.$r)->getNumberFormat()->setFormatCode('#,##0.00');
        $sheet->getHeaderFooter()->setOddHeader('&C&BReporte de Inventario SIGI'); $sheet->getHeaderFooter()->setOddFooter('&LGenerado: '.date('d/m/Y H:i').'&RPágina &P de &N');
        $filename='Inventario_SIGI_'.date('Ymd_His').'.xlsx'; header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'); header('Content-Disposition: attachment; filename="'.$filename.'"'); header('Cache-Control: max-age=0'); (new Xlsx($book))->save('php://output'); exit;
    }
    public function movementsExcel(): void
    {
        AuthMiddleware::handle(); Authorization::require('reportes.movimientos');
        $rows=Inventory::movements(500); $book=new Spreadsheet(); $sheet=$book->getActiveSheet(); $sheet->setTitle('Movimientos');
        $sheet->fromArray(['Número','Fecha','Tipo','Código','Producto','Cantidad','Stock anterior','Stock posterior','Referencia','Usuario'],null,'A1'); $r=2;
        foreach($rows as $x){$sheet->fromArray([$x['NumeroMovimiento'],$x['FechaMovimiento'],$x['Tipo'],$x['Codigo'],$x['Producto'],(float)$x['Cantidad'],(float)$x['StockAnterior'],(float)$x['StockPosterior'],$x['Referencia'],$x['NombreUsuario']],null,'A'.$r++);} $sheet->getStyle('A1:J1')->getFont()->setBold(true); $sheet->freezePane('A2'); foreach(range('A','J') as $c)$sheet->getColumnDimension($c)->setAutoSize(true);
        $filename='Movimientos_SIGI_'.date('Ymd_His').'.xlsx'; header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'); header('Content-Disposition: attachment; filename="'.$filename.'"'); header('Cache-Control: max-age=0'); (new Xlsx($book))->save('php://output'); exit;
    }
}
