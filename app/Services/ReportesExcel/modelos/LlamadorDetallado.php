<?php

namespace App\Services\ReportesExcel\modelos;

use App\Services\ReportesExcel\ReporteInterface;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

use Illuminate\Support\Str;
use App\Helpers\ToolsReportes;

class LlamadorDetallado implements ReporteInterface
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
            'B1' => 'Prestación',
            'C1' => 'Empresa',
            'D1' => 'ParaEmpresa',
            'E1' => 'ART',
            'F1' => 'Paciente',
            'G1' => 'DNI',
            'H1' => 'Tipo',
            'I1' => 'Telefono',
            'J1' => 'Estado del Examen',
            'K1' => 'Nombre del Examen',
            'L1' => 'Anulado',
            'M1' => 'Estado EFE',
            'N1' => 'Estado ADJ',
            'O1' => 'Estado INF',
            'P1' => 'Obs Examen'
        ];

        $columnas = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P'];

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
        foreach($datos as $efector){
            $sheet->setCellValue('A'.$fila, $efector->fecha);
            $sheet->setCellValue('B'.$fila, $efector->prestacion ?? 0);
            $sheet->setCellValue('C'.$fila, $efector->empresa ?? '');
            $sheet->setCellValue('D'.$fila, $efector->paraEmpresa ?? '');
            $sheet->setCellValue('E'.$fila, $efector->art ?? '');
            $sheet->setCellValue('F'.$fila, $efector->paciente ?? '');
            $sheet->setCellValue('G'.$fila, $efector->dni ?? '');
            $sheet->setCellValue('H'.$fila, $efector->tipo ?? '');
            $sheet->setCellValue('I'.$fila, $efector->telefono ?? '');
            $sheet->setCellValue('J'.$fila, $efector->estadoExamen ?? '');
            $sheet->setCellValue('K'.$fila, $efector->nombreExamen ?? '');
            $sheet->setCellValue('L'.$fila, $efector->Anulado === 0 ? 'NO' : 'SI');
            $sheet->setCellValue('M'.$fila, $efector->estadoEfector ?? '');
            $sheet->setCellValue('N'.$fila, $efector->estadoAdj ?? '');
            $sheet->setCellValue('O'.$fila, $efector->estadoInformador ?? '');
            $sheet->setCellValue('P'.$fila, strip_tags($efector->obsExamen) ?? '');
            $fila++;
        }
    }

    public function generar($efector)
    {
        $this->columnasYEncabezados($this->sheet);
        $this->datos($this->sheet, $efector);
        
        $name = 'detalle_' . Str::random(6) . '.xlsx';
        return $this->generarArchivo($this->spreadsheet, $name);
    }
}