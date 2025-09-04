<?php

namespace App\Services\ReportesExcel\modelos;

use App\Services\ReportesExcel\ReporteInterface;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

use Illuminate\Support\Str;
use App\Helpers\ToolsReportes;

class GrupoClientesDetalleFull implements ReporteInterface
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
            'A1' => 'Grupo',
            'B1' => 'Nro Cliente',
            'C1' => 'Razon Social',
            'D1' => 'Para Empresa',
            'E1' => 'CUIT'
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

    public function datos($sheet, $grupos)
    {
        $fila = 2;
        foreach ($grupos as $grupo) {            
            $sheet->setCellValue('A' . $fila, $grupo->NombreGrupo);
            $sheet->setCellValue('B' . $fila, $grupo->NroCliente);
            $sheet->setCellValue('C' . $fila, $grupo->RazonSocial);
            $sheet->setCellValue('D' . $fila, $grupo->ParaEmpresa);
            $sheet->setCellValue('E' . $fila, $grupo->CUIT);

            $fila++;
        }
    }

    public function generar($grupos)
    {
        $this->columnasYEncabezados($this->sheet);
        $this->datos($this->sheet, $grupos);
        
        $name = 'grupo_clientes_detalle_' . Str::random(6) . '.xlsx';
        return $this->generarArchivo($this->spreadsheet, $name);
    }

    
}
