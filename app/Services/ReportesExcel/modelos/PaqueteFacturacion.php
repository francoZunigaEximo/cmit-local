<?php

namespace App\Services\ReportesExcel\modelos;

use App\Services\ReportesExcel\ReporteInterface;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

use Illuminate\Support\Str;
use App\Helpers\ToolsReportes;

class PaqueteFacturacion implements ReporteInterface
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
            'A1' => 'Nombre',
            'B1' => 'Examenes',
            'C1' => 'Empresa',
            'D1' => 'Grupo',
            'E1' => 'Codigo'
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

    public function datos($sheet, $paquetes)
    {
        $fila = 2;
        foreach ($paquetes as $paquete) {            
            $sheet->setCellValue('A' . $fila, $paquete->Nombre);
            $sheet->setCellValue('B' . $fila, $paquete->CantExamenes);
            $sheet->setCellValue('C' . $fila, $paquete->NombreEmpresa);
            $sheet->setCellValue('D' . $fila, $paquete->NombreGrupo);
            $sheet->setCellValue('E' . $fila, $paquete->Codigo);
            $fila++;
        }
    }

    public function generar($paquetes)
    {
        $this->columnasYEncabezados($this->sheet);
        $this->datos($this->sheet, $paquetes);
        
        $name = 'paquetes_facturacion_' . Str::random(6) . '.xlsx';
        return $this->generarArchivo($this->spreadsheet, $name);
    }

    //paquete de facturacion
    
}
