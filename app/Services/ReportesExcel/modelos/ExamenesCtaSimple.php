<?php

namespace App\Services\ReportesExcel\modelos;

use App\Services\ReportesExcel\ReporteInterface;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

use Illuminate\Support\Str;
use App\Helpers\ToolsReportes;
use App\Services\ReportesExcel\ReporteInterface2;

class ExamenesCtaSimple implements ReporteInterface
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
            'A1' => 'Nro',
            'B1' => 'Pago',
            'C1' => 'Fecha',
            'D1' => 'Cliente',
            'E1' => 'CUIT',
            'F1' => 'Empresa',
            'G1' => 'Pagado',
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

    public function datos($sheet, $examenesCta)
    {
        $fila = 2;
        foreach($examenesCta as $examen){
            $factura = $examen->Tipo.'-'.str_pad($examen->Sucursal,5,'0',STR_PAD_LEFT).'-'.str_pad($examen->Numero,8,'0',STR_PAD_LEFT);
            $sheet->setCellValue('A'.$fila, $examen->IdEx ?? '-');
            $sheet->setCellValue('B'.$fila, $factura);
            $sheet->setCellValue('C'.$fila, $examen->Fecha ?? '-');
            $sheet->setCellValue('D'.$fila, $examen->Cliente ?? '-');
            $sheet->setCellValue('E'.$fila, $examen->Cuit ?? '-');
            $sheet->setCellValue('F'.$fila, $examen->ParaEmpresa ?? '-');
            $sheet->setCellValue('G'.$fila, $examen->Pagado ?? '-');
            $fila++;
        }
    }

    public function generar($examenesCta)
    {
        $this->columnasYEncabezados($this->sheet);
        $this->datos($this->sheet, $examenesCta);

        $name = 'examenes_cta_simple_' . Str::random(6) . '.xlsx';
        return $this->generarArchivo($this->spreadsheet, $name);
    }

}