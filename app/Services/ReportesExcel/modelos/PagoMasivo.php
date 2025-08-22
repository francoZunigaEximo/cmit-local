<?php

namespace App\Services\ReportesExcel\modelos;

use App\Services\ReportesExcel\ReporteInterface;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

use Illuminate\Support\Str;
use App\Helpers\ToolsReportes;

class PagoMasivo implements ReporteInterface {

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
            'A1' => 'Factura',
            'B1' => 'Fecha facturación',
            'C1' => 'Empresa'
        ];

        $columnas = ['A', 'B', 'C'];

        foreach ($columnas as $columna) {
            $sheet->getColumnDimension($columna)->setAutoSize(true);
        }

        // Establecer los encabezados de las celdas
        foreach ($encabezados as $celda => $valor) {
            $sheet->setCellValue($celda, $valor);
        }
    }

    public function datos($sheet, $datos)
    {
        $fila = 2;

        foreach($datos as $dato) {

            

            $sheet->setCellValue('A' . $fila, $dato->Factura);
            $sheet->setCellValue('B' . $fila, $dato->FechaF);
            $sheet->setCellValue('C' . $fila, $dato->Empresa);
            $fila++;
        }
    }

    public function generar($datos)
    {
        $this->columnasYEncabezados($this->sheet);
        $this->datos($this->sheet, $datos);

        $name = 'pagoMasivo_' . Str::random(6) . '.xlsx';
        return $this->generarArchivo($this->spreadsheet, $name);
    }

}