<?php

namespace App\Services\ReportesExcel\modelos;

use App\Services\ReportesExcel\ReporteInterface;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

use Illuminate\Support\Str;
use App\Helpers\ToolsReportes;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class SimplePrestacionFull implements ReporteInterface
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
            'S1' => 'Obs. Evaluación',
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
        $prestaciones = $this->getPrestaciones($prestaciones[0], $prestaciones['filters']);

        $fila = 2;
        foreach($prestaciones as $prestacion){
            $sheet->setCellValue('A'.$fila, $this->formatearFecha($prestacion->FechaAlta));
            $sheet->setCellValue('B'.$fila, $prestacion->Id ?? '');
            $sheet->setCellValue('C'.$fila, $prestacion->TipoPrestacion ?? '');
            $sheet->setCellValue('D'.$fila, $prestacion->Paciente ?? '');
            $sheet->setCellValue('E'.$fila, $prestacion->DNI ?? '');
            $sheet->setCellValue('F'.$fila, $prestacion->EmpresaRazonSocial ?? '');
            $sheet->setCellValue('G'.$fila, $prestacion->EmpresaParaEmp ?? '');
            $sheet->setCellValue('H'.$fila, $prestacion->ArtRazonSocial ?? '');
            $sheet->setCellValue('I'.$fila, $prestacion->Cerrado === 1 ? 'SI' : 'NO');
            $sheet->setCellValue('J'.$fila, $prestacion->Finalizado === 1 ? 'SI' : 'NO');
            $sheet->setCellValue('K'.$fila, $prestacion->Entregado === 1 ? 'SI' : 'NO');
            $sheet->setCellValue('L'.$fila, $prestacion->eEnviado === 1 ? 'SI' : 'NO');
            $sheet->setCellValue('M'.$fila, $this->formatearFecha($prestacion->Facturado));
            $sheet->setCellValue('N'.$fila, $prestacion->NroFactura ?? '0000');
            $sheet->setCellValue('O'.$fila, $this->formaPagoPrestacion($prestacion->Pago));
            $sheet->setCellValue('P'.$fila, $this->formatearFecha($prestacion->FechaVto));
            $sheet->setCellValue('Q'.$fila, substr($prestacion->Evaluacion, 2));
            $sheet->setCellValue('R'.$fila, substr($prestacion->Calificacion, 2));
            $sheet->setCellValue('S'.$fila, strip_tags($prestacion->Observaciones) ?? '');
            $sheet->setCellValue('T'.$fila, $prestacion->Anulado === 1 ? 'SI' : 'NO');
            $sheet->setCellValue('U'.$fila, $prestacion->ObsAnulado ?? '');
            $sheet->setCellValue('V'.$fila, $prestacion->NroCEE ?? '');
            $sheet->setCellValue('W'.$fila, $prestacion->CCosto ?? '');
            $sheet->setCellValue('X'.$fila, $prestacion->Incompleto === 1 ? 'SI' : 'NO');
            $sheet->setCellValue('Y'.$fila, $prestacion->Ausente === 1 ? 'SI' : 'NO');
            $sheet->setCellValue('Z'.$fila, $prestacion->Forma === 1 ? 'SI' : 'NO');
            $sheet->setCellValue('AA'.$fila, $prestacion->Devol === 1 ? 'SI' : 'NO');
            $sheet->setCellValue('AB'.$fila, strip_tags($prestacion->ObsEstado) ?? '');
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

    private function getPrestaciones(array $ids, array $filtros){

        $query =  DB::table('prestaciones')
        ->join('pacientes', 'prestaciones.IdPaciente', '=', 'pacientes.Id')
        ->join('clientes as emp', 'prestaciones.IdEmpresa', '=', 'emp.Id')
        ->join('clientes as art', 'prestaciones.IdART', '=', 'art.Id')
        ->leftJoin('prestaciones_comentarios', 'prestaciones.Id', '=', 'prestaciones_comentarios.IdP')
        ->leftJoin('itemsfacturaventa', 'prestaciones.Id', '=', 'itemsfacturaventa.IdPrestacion')
        ->leftJoin('facturasventa', 'itemsfacturaventa.IdFactura', '=', 'facturasventa.Id') 
        ->leftJoin('fichaslaborales', 'pacientes.Id', '=', 'fichaslaborales.IdPaciente')
        ->select(
            'prestaciones.Fecha as FechaAlta',
            'prestaciones.Id as Id',
            'prestaciones.TipoPrestacion as TipoPrestacion',
            'pacientes.Documento as DNI',
            DB::raw('CONCAT(pacientes.Apellido, " ", pacientes.Nombre) as Paciente'),
            'prestaciones.FechaCierre as Cerrado',
            'prestaciones.FechaFinalizado as Finalizado',
            'prestaciones.FechaEntrega as Entregado',
            'prestaciones.FechaEnviado as eEnviado',  
            'prestaciones.FechaFact as Facturado',
            'prestaciones.FechaVto as FechaVto',
            'prestaciones.Evaluacion as Evaluacion', 
            'prestaciones.Calificacion as Calificacion',
            'prestaciones.Observaciones as Observaciones', 
            'prestaciones.Anulado as Anulado',
            'prestaciones.ObsAnulado as ObsAnulado', 
            'prestaciones.NroCEE as NroCEE',
            'prestaciones.Incompleto as Incompleto', 
            'prestaciones.Ausente as Ausente',
            'prestaciones.Forma as Forma',
            'prestaciones.Devol as Devol',
            'prestaciones_comentarios.Obs as ObsEstado',
            'prestaciones.Pago as Pago',
            'art.RazonSocial as ArtRazonSocial',
            'emp.RazonSocial as EmpresaRazonSocial',
            'emp.Identificacion as EmpresaIdentificacion',
            'emp.ParaEmpresa as EmpresaParaEmp',
            'facturasventa.Tipo as Tipo',
            'facturasventa.Sucursal as Sucursal',
            'facturasventa.NroFactura as NroFactura',
            'fichaslaborales.CCosto as CCosto'
        )
        ->where('prestaciones.Estado', 1)
        ->whereIn('prestaciones.Id', $ids)
        ->groupBy('prestaciones.Id');

         if (empty($filtros)) {
            return $query->orderBy('prestaciones.Id', 'DESC')->get();
         }
         else {

            return $this->applyFilters($query, $filtros);
         }
    }

    public function applyFilters($query, $filters) 
    {

        if(!empty($filters->pacienteSelect2)) {
            $query->where(function($query) use ($filters) {
                $query->where('paciente.Id', $filters->pacienteSelect2);
            });
        }

        if(!empty($filters->empresaSelect2)) {
            $query->where(function($query) use ($filters) {
                $query->where('emp.Id', $filters->empresaSelect2);
            });
        }

        if(!empty($filters->artSelect2)) {
            $query->where(function($query) use ($filters) {
                $query->where('art.Id', $filters->artSelect2);
            });
        }

        if (!empty($filters->tipoPrestacion)) {
            $query->where('prestaciones.TipoPrestacion', $filters->tipoPrestacion);
        }
    
        if (!empty($filters->pago)) {
            $query->where('prestaciones.Pago', $filters->pago);
        }
    
        if (!empty($filters->formaPago)) {
            $query->where('prestaciones.SPago', $filters->formaPago);
        }

        if(!empty($filters->eEnviado)){
            $query->where('prestaciones.eEnviado', $filters->eEnviado);
        }

        if (!empty($filters->fechaDesde) && (!empty($filters->fechaHasta))) {
            $query->whereBetween('prestaciones.Fecha', [$filters->fechaDesde, $filters->fechaHasta]);
        }

        if (!empty($filters->estado) && is_array($filters->estado) && in_array('Incompleto', $filters->estado)) {
            $query->where('prestaciones.Incompleto', 1);
        }

        if (!empty($filters->estado) && is_array($filters->estado) && in_array('Anulado', $filters->estado)) {
            $query->where('prestaciones.Anulado', 1);
        }

        if (!empty($filters->estado) && is_array($filters->estado) && in_array('Ausente', $filters->estado)) {
            $query->where('prestaciones.Ausente', 1);
        }

        if (!empty($filters->estado) && is_array($filters->estado) && in_array('Forma', $filters->estado)) {
            $query->where('prestaciones.Forma', 1);
        }

        if (!empty($filters->estado) && is_array($filters->estado) && in_array('SinEsc', $filters->estado)) {
            $query->where('prestaciones.SinEsc', 1);
        }

        if (!empty($filters->estado) && is_array($filters->estado) && in_array('Devol', $filters->estado)) {
            $query->where('prestaciones.Devol', 1);
        }
    
        if (!empty($filters->estado) && is_array($filters->estado) && in_array('RxPreliminar', $filters->estado)) {
            $query->where('prestaciones.RxPreliminar', 1);
        }

        if (!empty($filters->estado) && is_array($filters->estado) && in_array('Cerrado', $filters->estado)) {
            $query->where('prestaciones.Cerrado', 1);
        }

        if (!empty($filters->estado) && is_array($filters->estado) && in_array('Abierto', $filters->estado)) {
            $query->where('prestaciones.Cerrado', 0)
                ->where('prestaciones.Finalizado', 0);
        }

        if (!empty($filters->estado) && is_array($filters->estado) && in_array('Cerrado', $filters->estado)) {
            $query->where('prestaciones.Cerrado', 1);
        }

        if (!empty($filters->finalizado)) {
            $query->where('prestaciones.Finalizado', $filters->finalizado);
            return $query;
        }
    
        if (!empty($filters->facturado)) {
            $query->where('prestaciones.Facturado', $filters->facturado);
        }
    
        if (!empty($filters->entregado)) {
            $query->where('prestaciones.Entregado', $filters->entregado);
        }

        return $query->get();
    }

}