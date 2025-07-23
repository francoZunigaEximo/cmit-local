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
            'C1' => 'CUIT',
            'D1' => 'Condición IVA',
            'E1' => 'Para Empresa',
            'F1' => 'Dirección',
            'G1' => 'Provincia',
            'H1' => 'Localidad',
            'I1' => 'CódigoPostal',
            //nuevos encabezados
            'J1'=> 'Nombre',
            'K1' => 'Tipo Cliente',
            'L1' => 'Entrega',
            'M1' => 'Logo Certificado',
            'N1' => 'Oreste',
            'O1' => 'Generico',
            'P1' => 'Bloqueado',
            'Q1' => 'Forma Pago',
            'R1' => 'Descuento',
            'S1' => 'Sin Envio Mail',
            'T1' => 'Facturacion Sin Paquetes',
            'U1' => 'Sin Evaluacion',
            'V1' => 'Actividad',
            'W1' => 'Asignado',
            'X1' => 'Bloqueado',
            'Y1' => 'Motivo',
            'Z1' => 'Direccion',
            'AA1' => 'Provincia',
            'AB1' => 'EMail',
            'AC1' => 'ObsEMail',
            'AD1' => 'EMailResultados',
            'AE1' => 'EMail Facturacion',
            'AF1' => 'Ultimo Envio Factura',
            'AG1' => 'EMail Informes',
            'AH1' => 'Ultimo Envio Informe',
            'AI1' => 'Telefono',
            'AJ1' => 'Obs Eval',
            'AK1' => 'Obs CE',
            'AL1' => 'ObsCobranzas',
            'AM1' => 'Observaciones'
        ];

        $columnas = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I','J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', 'AA', 'AB', 'AC', 'AD', 'AE', 'AF', 'AG', 'AH', 'AI', 'AJ', 'AK', 'AL', 'AM'];

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
            $sheet->setCellValue('J'.$fila, $cliente->NombreFantasia);
            $sheet->setCellValue('K'.$fila, $cliente->TipoCliente);
            $sheet->setCellValue('L'.$fila, $cliente->Entrega);
            $sheet->setCellValue('M'.$fila, $cliente->LogoCertificado == 1 ? 'Si' : 'No');
            $sheet->setCellValue('N'.$fila, $cliente->Oreste == 1 ? 'Si' : 'No');
            $sheet->setCellValue('O'.$fila, $cliente->Generico);
            $sheet->setCellValue('P'.$fila, $cliente->Bloqueado);
            $sheet->setCellValue('Q'.$fila, $cliente->FPago);
            $sheet->setCellValue('R'.$fila, $cliente->Descuento);
            $sheet->setCellValue('S'.$fila, $cliente->SEMail == 1 ? 'Si' : 'No');
            $sheet->setCellValue('T'.$fila, $cliente->SinPF == 1 ? 'Si' : 'No');
            $sheet->setCellValue('U'.$fila, $cliente->SinEval == 1 ? 'Si' : 'No');
            $sheet->setCellValue('V'.$fila, $cliente->actividad->Nombre ?? 'No Asignada');
            $sheet->setCellValue('W'.$fila, $cliente->IdAsignado);
            $sheet->setCellValue('X'.$fila, $cliente->Bloqueado);
            $sheet->setCellValue('Y'.$fila, $cliente->Motivo);
            $sheet->setCellValue('Z'.$fila, $cliente->Direccion);
            $sheet->setCellValue('AA'.$fila, $cliente->Provincia);
            $sheet->setCellValue('AB'.$fila, $cliente->EMail);
            $sheet->setCellValue('AC'.$fila, $cliente->ObsEMail);
            $sheet->setCellValue('AD'.$fila, $cliente->EMailResultados);
            $sheet->setCellValue('AE'.$fila, $cliente->EMailFactura);
            $sheet->setCellValue('AF'.$fila, "");
            $sheet->setCellValue('AG'.$fila, $cliente->EMailInformes);
            $sheet->setCellValue('AH'.$fila, "");
            $sheet->setCellValue('AI'.$fila, $cliente->Telefono);
            $sheet->setCellValue('AJ'.$fila, $cliente->ObsEval);
            $sheet->setCellValue('AK'.$fila, $cliente->ObsCE);
            $sheet->setCellValue('AL'.$fila, "");
            $sheet->setCellValue('AM'.$fila, $cliente->Observaciones);
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