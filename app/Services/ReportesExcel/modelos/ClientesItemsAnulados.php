<?php

namespace App\Services\ReportesExcel\modelos;

use App\Services\ReportesExcel\ReporteInterface;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

use Illuminate\Support\Str;
use App\Helpers\ToolsReportes;

class ClientesItemsAnulados implements ReporteInterface
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
            'A1' => 'Cliente',
            'B1' => 'CUIT'
        ];

        $columnas = ['A', 'B'];

        foreach ($columnas as $columna) {
            $sheet->getColumnDimension($columna)->setAutoSize(true);
        }

        // Establecer los encabezados de las celdas
        foreach ($encabezados as $celda => $valor) {
            $sheet->setCellValue($celda, $valor);
        }
    }

    public function datos($sheet, $clientes)
    {
        $fila = 2;
        foreach ($clientes as $cliente) {
            $sheet->setCellValue('A' . $fila, $cliente->Cliente);
            $sheet->setCellValue('B' . $fila, $cliente->CUIT);
            $fila++;
        }
    }

    public function generar($clientes)
    {
        $this->columnasYEncabezados($this->sheet);
        $this->datos($this->sheet, $clientes);

        $name = 'clientes_items_anulados_' . Str::random(6) . '.xlsx';
        return $this->generarArchivo($this->spreadsheet, $name);
    }

    
}
