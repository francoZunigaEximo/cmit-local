<?php

namespace App\Services\ReportesExcel\modelos;

use App\Services\ReportesExcel\ReporteInterface;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

use Illuminate\Support\Str;
use App\Helpers\ToolsReportes;

class Cliente implements ReporteInterface
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
            'B1' => 'Razón Social',
            'C1' => 'Identificación',
            'D1' => 'Condición IVA',
            'E1' => 'Para Empresa',
            'F1' => 'Dirección',
            'G1' => 'Provincia',
            'H1' => 'Localidad',
            'I1' => 'CódigoPostal'
        ];

        $columnas = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I'];

        foreach ($columnas as $columna) {
            $sheet->getColumnDimension($columna)->setAutoSize(true);
        }

        foreach ($encabezados as $celda => $valor) {
            $sheet->setCellValue($celda, $valor);
        }
    }

    public function datos($sheet, $clientes)
    {
        $fila = 2;
        foreach($clientes as $cliente){
            $sheet->setCellValue('A'.$fila, $cliente->Id);
            $sheet->setCellValue('B'.$fila, $cliente->RazonSocial);
            $sheet->setCellValue('C'.$fila, $cliente->Identificacion);
            $sheet->setCellValue('D'.$fila, $cliente->CondicionIva);
            $sheet->setCellValue('E'.$fila, $cliente->ParaEmpresa);
            $sheet->setCellValue('F'.$fila, $cliente->Direccion);
            $sheet->setCellValue('G'.$fila, $cliente->Provincia);
            $sheet->setCellValue('H'.$fila, $cliente->localidad->Nombre);
            $sheet->setCellValue('I'.$fila, $cliente->localidad->CP);
            $fila++;
        }
    }

    public function generar($clientes)
    {
        $this->columnasYEncabezados($this->sheet);
        $this->datos($this->sheet, $clientes);
        
        $name = 'clientes_' . Str::random(6) . '.xlsx';
        return $this->generarArchivo($this->spreadsheet, $name);
    }


}