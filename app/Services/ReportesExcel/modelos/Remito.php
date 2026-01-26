<?php

namespace App\Services\ReportesExcel\modelos;

use App\Services\ReportesExcel\ReporteInterface;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Border;

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
        $columnas = ['A', 'B', 'C', 'D', 'E','F','G','H','I'];

        foreach($columnas as $columna){
            $sheet->getColumnDimension($columna)->setAutoSize(true);
        }
    }

    public function datos($sheet, $datos)
    {
        $mapa = Mapa::with(['prestacion', 'artMapa', 'empresaMapa'])->find($datos['IdMapa']);

        $sheet->setCellValue('A1', 'REMITO DE ENTREGA DE ESTUDIOS');
        $sheet->setCellValue('A3', 'ART: '.$mapa->artMapa->RazonSocial ?? '');
        $sheet->setCellValue('A4', 'REMITO: '.$datos['nroRemito'] ?? '');
        $sheet->setCellValue('A5', 'EMPRESA: '.$mapa->empresaMapa->RazonSocial ?? '');
        $sheet->setCellValue('A6', 'MAPA: '.$mapa->Id ?? 0);
        // $sheet->setCellValue('A6', 'Fecha Mapa : '.Carbon::parse($mapa->FechaAsignacion)->format('d/m/Y') ?? '');

        $sheet->getStyle('A1')->getFont()->setBold(true);
      
        $examenes = Prestacion::where('NroCEE', $datos['nroRemito'])->pluck('Id');
        $items = ItemPrestacion::with(['prestaciones', 'examenes', 'prestaciones.paciente', 'prestaciones.empresa', 'prestaciones.art'])->whereIn('IdPrestacion', $examenes)->get();


        $sheet->setCellValue('A8', 'CUIT');
        $sheet->setCellValue('B8', 'CUIL');
        $sheet->setCellValue('C8', 'CÓDIGO DE EXÁMEN');
        $sheet->setCellValue('D8', 'FECHA DE EXAMEN');
        $sheet->setCellValue('E8', 'FECHA DE ASIGNACION');
        $sheet->setCellValue('F8', 'PACIENTE');
        $sheet->setCellValue('G8', 'DNI');
        $sheet->setCellValue('H8', 'PRESTACION');
        $sheet->setCellValue('I8', 'EXAMENES');

        $sheet->getStyle('A8:I8')->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        $sheet->getStyle('A8:I8')->getFont()->setBold(true);

        $fila = 9;
        foreach($items as $item){

            $nombreCompleto = ($item->prestaciones->paciente->Apellido ?? '').' '.($item->prestaciones->paciente->Nombre ?? '');

            $sheet->getStyle('A'.$fila.':I'.$fila)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

            $sheet->setCellValue('A'.$fila, str_replace('-','', $item->prestaciones->art->Identificacion) ?? '');
            $sheet->setCellValue('B'.$fila, str_replace('-','',$item->prestaciones->paciente->Identificacion) ?? '');
            $sheet->setCellValue('C'.$fila, $item->examenes->Cod ?? '');
            $sheet->setCellValue('D'.$fila, Carbon::parse($item->prestaciones->Fecha)->format('d/m/Y') ?? '');
            $sheet->setCellValue('E'.$fila, $mapa->FechaAsignacion <> '0000-00-00' || $mapa->FechaAsignacion <> null ? Carbon::parse($mapa->FechaAsignacion)->format('d/m/Y') : '');
            $sheet->setCellValue('F'.$fila, $nombreCompleto);
            $sheet->setCellValue('G'.$fila, $item->prestaciones->paciente->Documento ?? '');
            $sheet->setCellValue('H'.$fila, $item->prestaciones->Id ?? '');
            $sheet->setCellValue('I'.$fila, $item->examenes->Nombre ?? '');
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