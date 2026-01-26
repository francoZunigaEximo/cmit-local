<?php

namespace App\Services\ReportesExcel\modelos;

use App\Services\ReportesExcel\ReporteInterface;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

use Illuminate\Support\Str;
use App\Helpers\ToolsReportes;

class Especialidad implements ReporteInterface
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
            'A1' => 'Id',
            'B1' => 'Proveedor',
            'C1' => 'Ubicacion',
            'D1' => 'Telefono',
            'E1' => 'Adjunto',
            'F1' => 'Examen',
            'G1' => 'Informe'
        ];

        $columnas = ['A', 'B', 'C', 'D', 'E', 'F', 'G'];

        foreach ($columnas as $columna) {
            $sheet->getColumnDimension($columna)->setAutoSize(true);
        }

        // Establecer los encabezados de las celdas
        foreach ($encabezados as $celda => $valor) {
            $sheet->setCellValue($celda, $valor);
        }
    }

    public function datos($sheet, $especialidades)
    {
        $fila = 2;
        foreach($especialidades as $especialidad){
            $sheet->setCellValue('A'.$fila, $especialidad->IdEspecialidad ?? '-');
            $sheet->setCellValue('B'.$fila, $especialidad->Nombre ?? '-');
            $sheet->setCellValue('C'.$fila, $especialidad->Ubicacion === 0 ? 'Interno':($especialidad->Ubicacion === 1 ? 'Externo' : '-'));
            $sheet->setCellValue('D'.$fila, $especialidad->Telefono ?? '-');
            $sheet->setCellValue('E'.$fila, $especialidad->Adjunto === 0 ? 'Simple' : ($especialidad->Adjunto === 1 ? 'Multiple' : '-'));
            $sheet->setCellValue('F'.$fila, $especialidad->Examen === 0 ? 'Simple' : ($especialidad->Examen === 1 ? 'Multiple' : '-'));
            $sheet->setCellValue('G'.$fila, $especialidad->Informe === 0 ? 'Simple' : ($especialidad->Informe === 1 ? 'Multiple' : '-'));
            $fila++;
        }
    }

    public function generar($especialidades)
    {
        $this->columnasYEncabezados($this->sheet);
        $this->datos($this->sheet, $especialidades);
        
        $name = 'especialidades_' . Str::random(6) . '.xlsx';
        return $this->generarArchivo($this->spreadsheet, $name);
    }

}