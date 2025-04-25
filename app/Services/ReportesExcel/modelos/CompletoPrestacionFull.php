<?php

namespace App\Services\ReportesExcel\modelos;

use App\Services\ReportesExcel\ReporteInterface;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

use Illuminate\Support\Str;
use App\Helpers\ToolsReportes;
use Carbon\Carbon;

class CompletoPrestacionFull implements ReporteInterface
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
            'J1' => 'Nro.CE',
            'K1' => 'Anulada',
            'L1' => 'Obs.Anulada',
            'M1' => 'Cerrado',
            'N1' => 'Finalizado',
            'O1' => 'Entregado',
            'P1' => 'eEnviado',
            'Q1' => 'Vencimiento',
            'R1' => 'Evaluacion',
            'S1' => 'Calificacion',
            'T1' => 'Obs. Evaluación',
            'U1' => 'Examen',
            'V1' => 'Anulado',
            'W1' => 'INC',
            'X1' => 'AUS',
            'Y1' => 'FOR',
            'Z1' => 'DEV',
            'AA1' => 'OBS ESTADOS',
            'AB1' => 'Facturado',
            'AC1' => 'Factura',
            'AD1' => 'Forma Pago',
            'AE1' => 'Especialidad Efector',
            'AF1' => 'Efector',
            'AG1' => 'Fecha Asig Efector',
            'AH1' => 'Fecha Asig Inf',
            'AI1' => 'Hora Asig Efector',
            'AJ1' => 'Hora Asig Inf',
            'AK1' => 'Pagado Ef.',
            'AL1' => 'Informador',
            'AM1' => 'Pagado Inf.',
            'AN1' => 'Obs. Examen',
            'AO1' => 'Obs. Efector',
            'AP1' => 'Obs. Informador',
            'AQ1' => 'Obs. Privadas',
        ];

        $columnas = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', 'AA', 'AB', 'AC', 'AD', 'AE', 'AF', 'AG', 'AH', 'AI', 'AJ', 'AK', 'AL', 'AM', 'AN', 'AO', 'AP', 'AQ'];

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
            $sheet->setCellValue('D'.$fila, $prestacion->Paciente ?? '');
            $sheet->setCellValue('E'.$fila, $prestacion->DNI ?? '');
            $sheet->setCellValue('F'.$fila, $prestacion->EmpresaRazonSocial ?? ''.' '. $prestacion->EmpresaIdentificacion ?? '');
            $sheet->setCellValue('G'.$fila, $prestacion->EmpresaParaEmp ?? '');
            $sheet->setCellValue('H'.$fila, $prestacion->ArtRazonSocial ?? '');
            $sheet->setCellValue('I'.$fila, $prestacion->CCosto ?? '');
            $sheet->setCellValue('J'.$fila, $prestacion->NroCEE ?? '');
            $sheet->setCellValue('K'.$fila, $prestacion->Anulado ?? '');
            $sheet->setCellValue('L'.$fila, $prestacion->ObsAnulado ?? '');
            $sheet->setCellValue('M'.$fila, $prestacion->Cerrado <> '0000-00-00' ? Carbon::parse($prestacion->Cerrado)->format('d/m/Y') : '');
            $sheet->setCellValue('N'.$fila, $prestacion->Finalizado <> '0000-00-00' ? Carbon::parse($prestacion->Finalizado)->format('d/m/Y') : '');
            $sheet->setCellValue('O'.$fila, $prestacion->Entregado <> '0000-00-00' ? Carbon::parse($prestacion->Entregado)->format('d/m/Y') : '');
            $sheet->setCellValue('P'.$fila, $prestacion->eEnviado <> '0000-00-00' ? Carbon::parse($prestacion->eEnviado)->format('d/m/Y') : '');
            $sheet->setCellValue('Q'.$fila, $prestacion->FechaVto  <> '0000-00-00' ? Carbon::parse($prestacion->FechaVto) : '');
            $sheet->setCellValue('R'.$fila, substr($prestacion->Evaluacion, 2));
            $sheet->setCellValue('S'.$fila, substr($prestacion->Calificacion, 2));
            $sheet->setCellValue('T'.$fila, strip_tags($prestacion->Observaciones) ?? '');
            $sheet->setCellValue('U'.$fila, $prestacion->Examen ?? '');
            $sheet->setCellValue('V'.$fila, $prestacion->ExaAnulado === 1 ? 'Sí' : '');
            $sheet->setCellValue('W'.$fila, $prestacion->Incompleto === 1 ? 'Sí' : '');
            $sheet->setCellValue('X'.$fila, $prestacion->Ausente === 1 ? 'Sí' : '');
            $sheet->setCellValue('Y'.$fila, $prestacion->Forma === 1 ? 'Sí' : '');
            $sheet->setCellValue('Z'.$fila, $prestacion->Devol === 1 ? 'Sí' : '');
            $sheet->setCellValue('AA'.$fila, $prestacion->ObsEstado ?? '');
            $sheet->setCellValue('AB'.$fila, $prestacion->Facturado <> '0000-00-00' ? Carbon::parse($prestacion->Facturado)->format('d/m/Y') : '');
            $sheet->setCellValue('AC'.$fila, $prestacion->Tipo."".(sprintf('%05d', $prestacion->Sucursal))."-".$prestacion->NroFactura ?? '-');
            $sheet->setCellValue('AD'.$fila, $this->formaPagoPrestacion($prestacion->Pago) ?? '');
            $sheet->setCellValue('AE'.$fila, $prestacion->EspecialidadEfector ?? '');
            $sheet->setCellValue('AF'.$fila, $prestacion->Efector ?? '');
            $sheet->setCellValue('AG'.$fila, $prestacion->asignado <> '0000-00-00' ? Carbon::parse($prestacion->asignado)->format('d/m/Y') : '');
            $sheet->setCellValue('AH'.$fila, $prestacion->asignadoI <> '0000-00-00' ? Carbon::parse($prestacion->asignadoI)->format('d/m/Y') : '');
            $sheet->setCellValue('AI'.$fila, $prestacion->horaAsignado ?? '');
            $sheet->setCellValue('AJ'.$fila, $prestacion->horaAsignadoI ?? '');
            $sheet->setCellValue('AK'.$fila, $prestacion->Informador ?? '');
            $sheet->setCellValue('AL'.$fila, ($prestacion->pagadoEfector <> '0000-00-00' ? Carbon::parse($prestacion->pagadoEfector)->format('d/m/Y') : ''));
            $sheet->setCellValue('AM'.$fila, $prestacion->pagadoInformador <> '0000-00-00' ? Carbon::parse($prestacion->pagadoInformador)->format('d/m/Y') : '');
            $sheet->setCellValue('AN'.$fila, strip_tags($prestacion->ObsExamen) ?? '');
            $sheet->setCellValue('AO'.$fila, ''); // Consultar
            $sheet->setCellValue('AP'.$fila, strip_tags($prestacion->ObsInformador) ?? '');
            $sheet->setCellValue('AQ'.$fila, strip_tags($prestacion->ObsEstado) ?? '');
            $fila++;
        }
    }

    public function generar($completo)
    {
        $this->columnasYEncabezados($this->sheet);
        $this->datos($this->sheet, $completo);
        
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