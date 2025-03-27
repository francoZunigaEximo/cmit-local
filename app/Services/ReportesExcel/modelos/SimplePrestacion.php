<?php 

namespace App\Services\ReportesExcel\modelos;

use App\Services\ReportesExcel\ReporteInterface;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

use Illuminate\Support\Str;
use App\Helpers\ToolsReportes;
use Carbon\Carbon;

class SimplePrestacion implements ReporteInterface
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
            'I1' => 'Cerrado',
            'J1' => 'Finalizado',
            'K1' => 'Entregado',
            'L1' => 'eEnviado',
            'M1' => 'Facturado',
            'N1' => 'Factura',
            'O1' => 'Forma de Pago',
            'P1' => 'Vencimiento',
            'Q1' => 'Evaluación',
            'R1' => 'Calificación',
            'S1' => 'Obs Resultado',
            'T1' => 'Anulada',
            'U1' => 'Obs Anulada',
            'V1' => 'Nro CE',
            'W1' => 'C.Costos',
            'X1' => 'INC',
            'Y1' => 'AUS',
            'Z1' => 'FOR',
            'AA1' => 'DEV',
            'AB1' => 'Obs Estados',
        ];

        $columnas = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', 'AA', 'AB'];

        foreach ($columnas as $columna) {
            $sheet->getColumnDimension($columna)->setAutoSize(true);
        }

        foreach ($encabezados as $celda => $valor) {
            $sheet->setCellValue($celda, $valor);
        }
    }

    public function datos($sheet, $prestaciones)
    {
        $fila = 2;
        foreach($prestaciones as $prestacion){
            $sheet->setCellValue('A'.$fila, $this->formatearFecha($prestacion->FechaAlta));
            $sheet->setCellValue('B'.$fila, $prestacion->Id ?? '');
            $sheet->setCellValue('C'.$fila, $prestacion->TipoPrestacion ?? '');
            $sheet->setCellValue('D'.$fila, $prestacion->paciente->Apellido." ".$prestacion->paciente->Nombre);
            $sheet->setCellValue('E'.$fila, $prestacion->paciente->Documento ?? '');
            $sheet->setCellValue('F'.$fila, $prestacion->empresa->RazonSocial ?? '');
            $sheet->setCellValue('G'.$fila, $prestacion->empresa->ParaEmpresa ?? '');
            $sheet->setCellValue('H'.$fila, $prestacion->art->RazonSocial ?? '');
            $sheet->setCellValue('I'.$fila, $prestacion->Cerrado === 1 ? 'SI' : 'NO');
            $sheet->setCellValue('J'.$fila, $prestacion->Finalizado === 1 ? 'SI' : 'NO');
            $sheet->setCellValue('K'.$fila, $prestacion->Entregado === 1 ? 'SI' : 'NO');
            $sheet->setCellValue('L'.$fila, $prestacion->eEnviado === 1 ? 'SI' : 'NO');
            $sheet->setCellValue('M'.$fila, $this->formatearFecha($prestacion->Facturado));
            $sheet->setCellValue('N'.$fila, $prestacion->NumeroFacturaVta ?? '0000');
            $sheet->setCellValue('O'.$fila, $this->formaPagoPrestacion($prestacion->Pago));
            $sheet->setCellValue('P'.$fila, $this->formatearFecha($prestacion->FechaVto));
            $sheet->setCellValue('Q'.$fila, substr($prestacion->Evaluacion, 2));
            $sheet->setCellValue('R'.$fila, substr($prestacion->Calificacion, 2));
            $sheet->setCellValue('S'.$fila, strip_tags($prestacion->Observaciones) ?? '');
            $sheet->setCellValue('T'.$fila, $prestacion->Anulado === 1 ? 'SI' : 'NO');
            $sheet->setCellValue('U'.$fila, strip_tags($prestacion->ObsAnulado) ?? '');
            $sheet->setCellValue('V'.$fila, $prestacion->NroCEE ?? '');
            $sheet->setCellValue('W'.$fila, $prestacion->paciente->fichaLaboral->CCosto ?? '');
            $sheet->setCellValue('X'.$fila, $prestacion->Incompleto === 1 ? 'SI' : 'NO');
            $sheet->setCellValue('Y'.$fila, $prestacion->Ausente === 1 ? 'SI' : 'NO');
            $sheet->setCellValue('Z'.$fila, $prestacion->Forma === 1 ? 'SI' : 'NO');
            $sheet->setCellValue('AA'.$fila, $prestacion->Devol === 1 ? 'SI' : 'NO');
            $sheet->setCellValue('AB'.$fila, strip_tags($prestacion->prestacionComentario->Obs) ?? '');
            $fila++;
            
        }
    }

    public function generar($simple)
    {
        $this->columnasYEncabezados($this->sheet);
        $this->datos($this->sheet, $simple);
        
        $name = 'simple_' . Str::random(6) . '.xlsx';
        return $this->generarArchivo($this->spreadsheet, $name);
    }

    private function formatearFecha($fecha)
    {
        return $fecha === '0000-00-00' ? '' : Carbon::parse($fecha)->format('d/m/Y');
    }

    private function formaPagoPrestacion(string $pago): string
    {
        switch ($pago) {
            case "B":
                return 'Ctdo.';
            case "C":
                return  'CCorriente';
            case "P":
                return 'ExCuenta';
            default:
                return 'CCorriente';
        }
    }

}