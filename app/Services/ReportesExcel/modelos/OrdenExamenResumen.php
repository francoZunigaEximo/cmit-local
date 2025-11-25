<?php

namespace App\Services\ReportesExcel\modelos;

use App\Services\ReportesExcel\ReporteInterface;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

use App\Helpers\ToolsReportes;
use Illuminate\Support\Str;

class OrdenExamenResumen implements ReporteInterface
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
            'A1' => 'Avance',
            'B1' => 'Especialidad',
            'C1' => 'Fecha',
            'D1' => 'Prestación',
            'E1' => 'Empresa',
            'F1' => 'Paciente',
            'G1' => 'DNI',
            'H1' => 'Efector',
            'I1' => 'E_EFE',
            'J1' => 'Adjunto',
        ];

        $columnas = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J'];

        foreach ($columnas as $columna) {
            $sheet->getColumnDimension($columna)->setAutoSize(true);
        }
        foreach ($encabezados as $celda => $valor) {
            $sheet->setCellValue($celda, $valor);
        }
    }

    public function datos($sheet, $prestaciones)
    {
        $fila = 2;

        foreach($prestaciones as $prestacion) {
            $sheet->setCellValue('A' . $fila, $prestacion->avance);
            $sheet->setCellValue('B' . $fila, $prestacion->especialidad);
            $sheet->setCellValue('C' . $fila, $prestacion->fecha);
            $sheet->setCellValue('D' . $fila, $prestacion->prestacion);
            $sheet->setCellValue('E' . $fila, $prestacion->empresa);
            $sheet->setCellValue('F' . $fila, $prestacion->nombreCompleto);
            $sheet->setCellValue('G' . $fila, $prestacion->dni);
            $sheet->setCellValue('H' . $fila, $prestacion->efector);
            $sheet->setCellValue('I' . $fila, $prestacion->estado);
            $sheet->setCellValue('J' . $fila, $prestacion->archivos);
            $fila++;
        }
    }

    public function generar($prestaciones)
    {
        $this->columnasYEncabezados($this->sheet);
        $this->datos($this->sheet, $prestaciones);

        $name = 'ordenesExResumen' . Str::random(6) . '.xlsx';
        return $this->generarArchivo($this->spreadsheet, $name);
    }

}