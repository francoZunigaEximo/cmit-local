<?php

namespace App\Services\ReportesExcel\modelos;

use App\Services\ReportesExcel\ReporteInterface;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

use Illuminate\Support\Str;
use App\Helpers\ToolsReportes;

class PaqueteFacturacionDetalle implements ReporteInterface
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
            'A1' => 'Codigo',
            'B1' => 'Nombre',
            'C1' => 'Especialidad',
            'D1' => 'Examen',
            'E1' => 'Empresa',
            'F1' => 'Grupo'
        ];

        $columnas = ['A', 'B', 'C', 'D', 'E', 'F'];

        foreach ($columnas as $columna) {
            $sheet->getColumnDimension($columna)->setAutoSize(true);
        }

        // Establecer los encabezados de las celdas
        foreach ($encabezados as $celda => $valor) {
            $sheet->setCellValue($celda, $valor);
        }
    }

    public function datos($sheet, $paquetes)
    {
        $fila = 2;
        foreach ($paquetes as $paquete) {            
            $sheet->setCellValue('A' . $fila, $paquete->Id);
            $sheet->setCellValue('B' . $fila, $paquete->Nombre);
            $sheet->setCellValue('C' . $fila, $paquete->Especialidad);
            $sheet->setCellValue('D' . $fila, $paquete->NombreExamen);
            $sheet->setCellValue('E' . $fila, $paquete->Empresa == null? "": $paquete->Empresa );
            $sheet->setCellValue('F' . $fila, $paquete->Grupo == null ? "": $paquete->Grupo);
            $fila++;
        }
    }

    public function generar($paquetes)
    {
        $this->columnasYEncabezados($this->sheet);
        $this->datos($this->sheet, $paquetes);
        
        $name = 'paquetes_facturacion_detalles_' . Str::random(6) . '.xlsx';
        return $this->generarArchivo($this->spreadsheet, $name);
    }

    //paquete de facturacion
    
}
