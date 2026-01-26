<?php

namespace App\Services\ReportesExcel\modelos;

use App\Services\ReportesExcel\ReporteInterface;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

use Illuminate\Support\Str;
use App\Helpers\ToolsReportes;

class Paciente implements ReporteInterface
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
            'B1' => 'Apellido',
            'C1' => 'Nombre',
            'D1' => 'CUIL/CUIT',
            'E1' => 'Documento',
            'F1' => 'Nacionalidad',
            'G1' => 'Fecha de Nacimiento',
            'H1' => 'Direccion',
            'I1' => 'Localidad',
            'J1' => 'Provincia',
            'K1' => 'Email',
            'L1' => 'Telefono',
            'M1' => 'Antecedentes',
            'N1' => 'Observaciones'
        ];

        $columnas = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M','N'];

        foreach ($columnas as $columna) {
            $sheet->getColumnDimension($columna)->setAutoSize(true);
        }

        // Establecer los encabezados de las celdas
        foreach ($encabezados as $celda => $valor) {
            $sheet->setCellValue($celda, $valor);
        }
    }

    public function datos($sheet, $pacientes)
    {
        $fila = 2;
        $pacientes->chunk(1000, function($pacientes) use ($sheet, &$fila) {
            foreach ($pacientes as $paciente) {
                $nroTelefono = ( $paciente->CodigoArea != "" ? "(".$paciente->CodigoArea.") " : "" ). $paciente->NumeroTelefono;
                
                $sheet->setCellValue('A' . $fila, $paciente->Id);
                $sheet->setCellValue('B' . $fila, $paciente->Apellido);
                $sheet->setCellValue('C' . $fila, $paciente->Nombre);
                $sheet->setCellValue('D' . $fila, $paciente->Identificacion);
                $sheet->setCellValue('E' . $fila, $paciente->Documento);
                $sheet->setCellValue('F' . $fila, $paciente->Nacionalidad);
                $sheet->setCellValue('G' . $fila, $paciente->FechaNacimiento);
                $sheet->setCellValue('H' . $fila, $paciente->Direccion);
                $sheet->setCellValue('I' . $fila, $paciente->localidad->Nombre);
                $sheet->setCellValue('J' . $fila, $paciente->Provincia);
                $sheet->setCellValue('K' . $fila, $paciente->EMail);
                $sheet->setCellValue('L' . $fila, $nroTelefono);   
                $sheet->setCellValue('M' . $fila, $paciente->Antecedentes);
                $sheet->setCellValue('N' . $fila, $paciente->Observaciones);
                $fila++;
            }
        });
    }

    public function generar($pacientes)
    {
        $this->columnasYEncabezados($this->sheet);
        $this->datos($this->sheet, $pacientes);
        
        $name = 'pacientes_' . Str::random(6) . '.xlsx';
        return $this->generarArchivo($this->spreadsheet, $name);
    }

    
}
