<?php

namespace App\Services\ReportesExcel\modelos;

use App\Services\ReportesExcel\ReporteInterface;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

use Illuminate\Support\Str;
use App\Helpers\ToolsReportes;

class NotaCreditoReporte implements ReporteInterface
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
            'A1' => 'Numero',
            'B1' => 'Fecha',   
            'C1' => 'Empresa',
            'D1' => 'CUIT',
            'E1' => 'Observacion'
        ];

        $columnas = ['A', 'B', 'C', 'D', 'E'];

        foreach ($columnas as $columna) {
            $sheet->getColumnDimension($columna)->setAutoSize(true);
        }

        // Establecer los encabezados de las celdas
        foreach ($encabezados as $celda => $valor) {
            $sheet->setCellValue($celda, $valor);
        }
    }

    public function datos($sheet, $notasCredito)
    {
        $fila = 2;
        foreach ($notasCredito as $nota) {
            $sheet->setCellValue('A' . $fila, $nota->Id);
            $sheet->setCellValue('B' . $fila, $nota->Fecha);
            $sheet->setCellValue('C' . $fila, $nota->Empresa);
            $sheet->setCellValue('D' . $fila, $nota->CUIT);
            $sheet->setCellValue('E' . $fila, $nota->Observacion);
            $fila++;
        }
    }

    public function generar($notasCredito)
    {
        $this->columnasYEncabezados($this->sheet);
        $this->datos($this->sheet, $notasCredito);

        $name = 'notas_credito_' . Str::random(6) . '.xlsx';
        return $this->generarArchivo($this->spreadsheet, $name);
    }

    
}
