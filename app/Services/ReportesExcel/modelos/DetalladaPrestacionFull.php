<?php

namespace App\Services\ReportesExcel\modelos;

use App\Services\ReportesExcel\ReporteInterface;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

use Illuminate\Support\Str;
use App\Helpers\ToolsReportes;
use Carbon\Carbon;

class DetalladaPrestacionFull implements ReporteInterface
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
            'B1' => 'Prestacion',
            'C1' => 'Tipo',
            'D1' => 'Paciente',
            'E1' => 'DNI',
            'F1' => 'Cliente',
            'G1' => 'Empresa',
            'H1' => 'ART',
            'I1' => 'C.Costos',
            'J1' => 'Nro de CE',
            'K1' => 'Pres Anulada',
            'L1' => 'Obs Anulada',
            'M1' => 'Examen',
            'N1' => 'Examen Anulado',
            'O1' => 'INC',
            'P1' => 'AUS',
            'Q1' => 'FOR',
            'R1' => 'DEV',
            'S1' => 'Obs Estados',
        ];

        $columnas = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S'];

        foreach($columnas as $columna){
            $sheet->getColumnDimension($columna)->setAutoSize(true);
        }

        foreach ($encabezados as $celda => $valor) {
            $sheet->setCellValue($celda, $valor);
        }
    }

    public function datos($sheet, $prestaciones)
    {
        $prestaciones = $this->getDBDetalleYCompleto($prestaciones[0], $prestaciones['filters']);

        $fila = 2;
        foreach($prestaciones as $prestacion){
            $sheet->setCellValue('A'.$fila, $this->formatearFecha($prestacion->FechaAlta));
            $sheet->setCellValue('B'.$fila, $prestacion->Id ?? '');
            $sheet->setCellValue('C'.$fila, $prestacion->TipoPrestacion ?? '');
            $sheet->setCellValue('D'.$fila, $prestacion->Paciente);
            $sheet->setCellValue('E'.$fila, $prestacion->DNI ?? '');
            $sheet->setCellValue('F'.$fila, $prestacion->EmpresaRazonSocial ?? '');
            $sheet->setCellValue('G'.$fila, $prestacion->EmpresaParaEmp ?? '');
            $sheet->setCellValue('H'.$fila, $prestacion->ArtRazonSocial ?? '');
            $sheet->setCellValue('I'.$fila, $prestacion->CCosto ?? '');
            $sheet->setCellValue('J'.$fila, $prestacion->NroCEE ?? '');
            $sheet->setCellValue('K'.$fila, $prestacion->Anulado === 1 ? 'SI' : 'NO');
            $sheet->setCellValue('L'.$fila, $prestacion->ObsAnulado ?? '');
            $sheet->setCellValue('M'.$fila, $prestacion->Examen ?? '');
            $sheet->setCellValue('N'.$fila, strip_tags($prestacion->ObsExamen) ?? '');
            $sheet->setCellValue('O'.$fila, $prestacion->Incompleto ?? '');
            $sheet->setCellValue('P'.$fila, $prestacion->Ausente ?? '');
            $sheet->setCellValue('Q'.$fila, $prestacion->Forma ?? '');
            $sheet->setCellValue('R'.$fila, $prestacion->Devol ?? '');
            $sheet->setCellValue('S'.$fila, strip_tags($prestacion->ObsEstado) ?? '');
            $fila++;
        }
    }

    public function generar($detallada)
    {
        $this->columnasYEncabezados($this->sheet);
        $this->datos($this->sheet, $detallada);
        
        $name = 'detallado' . Str::random(6) . '.xlsx';
        return $this->generarArchivo($this->spreadsheet, $name);
    }

    private function formatearFecha($fecha)
    {
        return $fecha === '0000-00-00' ? '' : Carbon::parse($fecha)->format('d/m/Y');
    }

}