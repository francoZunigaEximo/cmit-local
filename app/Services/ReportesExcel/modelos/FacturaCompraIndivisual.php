<?php

namespace App\Services\ReportesExcel\modelos;

use App\Services\ReportesExcel\ReporteInterface;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

use Illuminate\Support\Str;
use App\Helpers\ToolsReportes;
use App\Services\ReportesExcel\ReporteInterface2;

class FacturaCompraIndivisual implements ReporteInterface2
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
            'A1' => 'DETALLE DE FACTURA COMPRA',
            'A6' => 'Prestacion',
            'B6' => 'Examen',
            'C6' => 'Empresa',
            'D6' => 'Paciente',
            'E6' => 'Tipo'
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

    public function datos($sheet, $examenesEfector, ...$args)
    {
        $profesional =  $args[0];
        $factura = $args[1];

        $nroFactura = $factura->Tipo."-".str_pad($factura->Sucursal, 5, "0", STR_PAD_LEFT)."-".str_pad($factura->NroFactura, 8, "0", STR_PAD_LEFT);
        $sheet->setCellValue('A2', "Fecha:".$factura->Fecha ?? '-');
        $sheet->setCellValue('A3', "Nro:".$nroFactura ?? '-');
        $sheet->setCellValue('A4', "Profesional:".$profesional->Nombre.', '.$profesional->Apellido ?? '-');

        $examenesInformador = $args[2];
        $totalEfector = $args[3];
        $totalInformador = $args[4];

        $styleArray = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_HAIR,
                    'color' => ['argb' => '000'],
                ],
            ],
        ];

        $fila = 7;
        $filaInicio = 6;
        foreach($examenesEfector as $examen){
          
            $sheet->setCellValue('A'.$fila, $examen->idPrestacion ?? '-');
            $sheet->setCellValue('B'.$fila, $examen->Examen ?? '-');
            $sheet->setCellValue('C'.$fila, $examen->Exmpresa ?? '-');
            $sheet->setCellValue('D'.$fila, $examen->Paciente ?? '-');
            $sheet->setCellValue('E'.$fila, "Efector" ?? '-');
            $fila++;
        }

        foreach($examenesInformador as $examen){
          
            $sheet->setCellValue('A'.$fila, $examen->idPrestacion ?? '-');
            $sheet->setCellValue('B'.$fila, $examen->Examen ?? '-');
            $sheet->setCellValue('C'.$fila, $examen->Empresa ?? '-');
            $sheet->setCellValue('D'.$fila, $examen->Paciente ?? '-');
            $sheet->setCellValue('E'.$fila, "Informador" ?? '-');
            $fila++;
        }

        $sheet->getStyle('A'.$filaInicio.':E'.$fila-1)->applyFromArray($styleArray);

        //colocamos los totales
        $fila++;
        $sheet->setCellValue('A'.$fila, "Total Examenes Efector" ?? '-');
        $fila++;
        $filaInicio = $fila;
        $sheet->setCellValue('A'.$fila, "Cantidad" ?? '-');
        $sheet->setCellValue('B'.$fila, "Examen" ?? '-');
        $fila++;
        foreach($totalEfector as $cantidad){
          
            $sheet->setCellValue('A'.$fila, $cantidad->Cantidad ?? '-');
            $sheet->setCellValue('B'.$fila, $cantidad->Examen ?? '-');
            $fila++;
        }
        $sheet->getStyle('A'.$filaInicio.':B'.$fila-1)->applyFromArray($styleArray);
        $sheet->setCellValue('A'.$fila, "Examenes: ".count($examenesEfector) ?? '-');
        
        $fila += 2;
        $sheet->setCellValue('A'.$fila, "Total Examenes Informador" ?? '-');
        $fila++;
        $filaInicio = $fila;
        $sheet->setCellValue('A'.$fila, "Cantidad" ?? '-');
        $sheet->setCellValue('B'.$fila, "Examen" ?? '-');
        $fila++;
        foreach($totalInformador as $cantidad){
          
            $sheet->setCellValue('A'.$fila, $cantidad->Cantidad ?? '-');
            $sheet->setCellValue('B'.$fila, $cantidad->Examen ?? '-');
            $fila++;
        }
        $sheet->getStyle('A'.$filaInicio.':B'.$fila-1)->applyFromArray($styleArray);
        $sheet->setCellValue('A'.$fila, "Examenes: ".count($examenesInformador) ?? '-');
    }

    public function generar($examenesEfector, ...$args)
    {
        $this->columnasYEncabezados($this->sheet);
        $this->datos($this->sheet, $examenesEfector, ...$args);
        
        $name = 'facturas_compra_individual_' . Str::random(6) . '.xlsx';
        return $this->generarArchivo($this->spreadsheet, $name);
    }

}