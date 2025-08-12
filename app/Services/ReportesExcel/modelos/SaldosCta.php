<?php

namespace App\Services\ReportesExcel\modelos;

use App\Services\ReportesExcel\ReporteInterface;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

use Illuminate\Support\Str;
use App\Helpers\ToolsReportes;

class SaldosCta implements ReporteInterface
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
            'B1' => 'Para Empresa',
            'C1' => 'Cantidad',
            'D1' => 'Examen'
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

    public function datos($sheet, $examenes)
    {
         $sheet->getStyle('A1:D1')->getFill()
                ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                ->getStartColor()->setARGB('CCCCCCCC'); 
        $fila = 2;
        foreach ($examenes as $examen) {
            $sheet->setCellValue('A' . $fila, $examen->Empresa);
            $sheet->setCellValue('B' . $fila, $examen->ParaEmpresa);
            $sheet->setCellValue('C' . $fila, $examen->contadorSaldos);
            $sheet->setCellValue('D' . $fila, $examen->Examen);
            $fila++;
        }
    }

    public function generar($examenes)
    {
        $this->columnasYEncabezados($this->sheet);
        $this->datos($this->sheet, $examenes);

        $name = 'saldos_cta_' . Str::random(6) . '.xlsx';
        return $this->generarArchivo($this->spreadsheet, $name);
    }
}
