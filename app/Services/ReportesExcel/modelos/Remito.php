<?php

namespace App\Services\ReportesExcel\modelos;

use App\Services\ReportesExcel\ReporteInterface;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

use Illuminate\Support\Str;
use App\Helpers\ToolsReportes;

use App\Models\Mapa;
use App\Models\Prestacion;
use App\Models\ItemPrestacion;
use Carbon\Carbon;

class Remito implements ReporteInterface
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
        $columnas = ['A', 'B', 'C', 'D', 'E','F','G'];

        foreach($columnas as $columna){
            $sheet->getColumnDimension($columna)->setAutoSize(true);
        }
    }

    public function datos($sheet, $datos)
    {
        $mapa = Mapa::with(['prestacion', 'artMapa', 'empresaMapa'])->find($datos['IdMapa']);

        $sheet->setCellValue('A1', 'REMITO DE ENTREGA DE ESTUDIOS');
        $sheet->setCellValue('A3', 'REMITO: '.$datos['nroRemito'] ?? 0);
        $sheet->setCellValue('A4', 'EMPRESA: '.$mapa->empresaMapa->RazonSocial ?? '');
        $sheet->setCellValue('A5', 'CUIT: '.$mapa->empresaMapa->Identificacion ?? '');
        $sheet->setCellValue('A6', 'MAPA: '.$mapa->Id ?? 0);
        $sheet->setCellValue('A6', 'Fecha Mapa : '.Carbon::parse($mapa->FechaAsignacion)->format('d/m/Y') ?? '');
      
        $examenes = Prestacion::where('NroCEE', $datos['nroRemito'])->pluck('Id');
        $items = ItemPrestacion::with(['prestaciones', 'examenes', 'prestaciones.paciente'])->whereIn('IdPrestacion', $examenes)->get();

        $sheet->setCellValue('A8', 'DNI');
        $sheet->setCellValue('B8', 'Paciente');
        $sheet->setCellValue('C8', 'Prestación');
        $sheet->setCellValue('D8', 'Examenes');
        $sheet->setCellValue('E8', 'CUIL');
        $sheet->setCellValue('F8', 'Código Exámen');
        $sheet->setCellValue('G8', 'Fecha Prestación');
        
        $fila = 9;
        foreach($items as $item){

            $nombreCompleto = ($item->prestaciones->paciente->Apellido ?? '').' '.($item->prestaciones->paciente->Nombre ?? '');

            $sheet->setCellValue('A'.$fila, $item->prestaciones->paciente->Documento ?? '');
            $sheet->setCellValue('B'.$fila, $nombreCompleto);
            $sheet->setCellValue('C'.$fila, $item->prestaciones->Id ?? ''); 
            $sheet->setCellValue('D'.$fila, $item->examenes->Nombre ?? '');
            $sheet->setCellValue('E'.$fila, $item->paciente->Identificacion ?? '');
            $sheet->setCellValue('F'.$fila, $item->examenes->Cod ?? '');
            $sheet->setCellValue('G'.$fila, Carbon::parse($item->prestaciones->Fecha)->format('d/m/Y') ?? '');
            $fila++;
        }
    }

    public function generar($datos)
    {
        $this->columnasYEncabezados($this->sheet);
        $this->datos($this->sheet, $datos);

        $name = 'remitos_' . Str::random(6) . '.xlsx';
        return $this->generarArchivo($this->spreadsheet, $name);
    }
      
}