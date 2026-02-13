<?php

namespace App\Services\ReportesExcel\modelos;

use App\Services\ReportesExcel\ReporteInterface;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

use Illuminate\Support\Str;
use App\Helpers\ToolsReportes;

class CuentaCte implements ReporteInterface
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
            'B1' => 'Pago',
            'C1' => 'Fecha',
            'D1' => 'Cliente',
            'E1' => 'Empresa',
            'F1' => 'Cant',
            'G1' => 'Examen'
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

    public function datos($sheet, $examenes)
    {
        $sheet->getStyle('A1:G1')->getFill()
                ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                ->getStartColor()->setARGB('CCCCCCCC'); 
        $fila = 2;
        foreach ($examenes as $examen) {
            $factura = $examen->Tipo . sprintf('%04d', $examen->Suc) . sprintf('%08d', $examen->Nro);

            $sheet->setCellValue('A'.$fila, $examen->IdEx);
            $sheet->setCellValue('B'.$fila, $factura);
            $sheet->setCellValue('C'.$fila, $examen->Fecha);
            $sheet->setCellValue('D'.$fila, $examen->Empresa);
            $sheet->setCellValue('E'.$fila, $examen->ParaEmpresa);
            $sheet->setCellValue('F'.$fila, $examen->contadorSaldos);
            $sheet->setCellValue('G'.$fila, $examen->Examen);
            $fila++;
        }
    }

    public function generar($examenes)
    {
        $this->columnasYEncabezados($this->sheet);
        $this->datos($this->sheet, $examenes);

        $name = 'cuenta_cta_' . Str::random(6) . '.xlsx';
        return $this->generarArchivo($this->spreadsheet, $name);
    }

    
}
