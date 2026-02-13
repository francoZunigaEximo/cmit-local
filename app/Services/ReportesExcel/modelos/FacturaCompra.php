<?php

namespace App\Services\ReportesExcel\modelos;

use App\Services\ReportesExcel\ReporteInterface;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

use Illuminate\Support\Str;
use App\Helpers\ToolsReportes;
use App\Services\ReportesExcel\ReporteInterface2;

class FacturaCompra implements ReporteInterface
{
    protected $spreadsheet;
    protected $sheet;

    use ToolsReportes;

    public function __construct()
    {
        $this->spreadsheet = new Spreadsheet();
        $this->sheet = $this->spreadsheet->getActiveSheet();
        $this->sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
    }

    public function columnasYEncabezados($sheet)
    {
        $encabezados = [
            'A1' => 'Fecha',
            'B1' => 'Nro Factura',
            'C1' => 'Especialidad',
            'D1' => 'Profesional'
        ];

        $columnas = ['A', 'B', 'C', 'D'];

        foreach ($columnas as $columna) {
            $sheet->getColumnDimension($columna)->setAutoSize(true);
        }

        // Establecer los encabezados de las celdas
        foreach ($encabezados as $celda => $valor) {
            $sheet->setCellValue($celda, $valor);
        }
    }

    public function datos($sheet, $facturas)
    {
        $fila = 2;
        foreach($facturas as $factura){
            $nroFactura = $factura->Tipo."-".str_pad($factura->Sucursal, 5, "0", STR_PAD_LEFT)."-".str_pad($factura->NroFactura, 8, "0", STR_PAD_LEFT);

            $sheet->setCellValue('A'.$fila, $factura->Fecha ?? '-');
            $sheet->setCellValue('B'.$fila, $nroFactura ?? '-');
            $sheet->setCellValue('C'.$fila, $factura->Especialidad ?? '-');
            $sheet->setCellValue('D'.$fila, $factura->Profesional ?? '-');
            $fila++;
        }
    }

    public function generar($facturas)
    {
        $this->columnasYEncabezados($this->sheet);
        $this->datos($this->sheet, $facturas);
        
        $name = 'facturas_compra_' . Str::random(6) . '.xlsx';
        return $this->generarArchivo($this->spreadsheet, $name);
    }

}