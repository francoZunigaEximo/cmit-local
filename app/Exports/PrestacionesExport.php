<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use App\Models\Prestacion;
use stdClass;
use Carbon\Carbon;

class PrestacionesExport implements FromCollection,WithHeadings
{

    protected $ids;
    protected $filters;

    function __construct($ids, $filters) {
        $this->ids      = $ids;
        $this->filters  = $filters;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function headings(): array
    {
        return [
            'N°',
            'Alta',
            'Empresa',
            'Para Empresa',
            'Cuit',
            'Paciente',
            'ART',
            'Situación',
            'F.Pago',
            'Observacion Estado',
            'Aus',
            'Inc',
            'Dev',
            'For',
            'SinEsc'
        ];
    }
    public function collection()
    {
        $prestacionesExcel = collect();
        $prestaciones = empty($this->filters) ? $this->getPrestacionesPorIds() : $this->getPrestacionesPorFiltros();

        foreach ($prestaciones as $prestacion) {

            $prestacionExcel = new stdClass();

            $prestacionExcel->numero            = $prestacion->Id ?? '-';
            $prestacionExcel->alta              = Carbon::parse($prestacion->FechaAlta)->format('d-m-Y') ?? '-'; // Formatear
            $prestacionExcel->empresa           = $prestacion->RazonSocial ?? '-';
            $prestacionExcel->paraEmpresa       = $prestacion->ParaEmpresa ?? '-';
            $prestacionExcel->Identificacion    = $prestacion->Identificacion ?? '-';
            $prestacionExcel->paciente          = $prestacion->Apellido . " " . $prestacion->Nombre;
            $prestacionExcel->art               = $prestacion->Art ?? '-';
            $prestacionExcel->situacion         = $prestacion->Anulado == 0 ? "Habilitado" : "Anulado";
            $pago = "-";

            switch ($prestacion->Pago) {
                case "B":
                    $prestacionExcel->pago = 'Ctdo.';
                    break;
                case "C":
                    $prestacionExcel->pago = 'CCorriente';
                    break;
                case "P":
                    $prestacionExcel->pago = 'ExCuenta';
                    break;
            }
            
            $prestacionExcel->observacionEstado = $prestacion->ObsEstado ?? '-';
            $prestacionExcel->ausente           = $prestacion->Ausente === 0 ? '-' : 'Sí';
            $prestacionExcel->incompleto        = $prestacion->Incompleto === 0 ? '-' : 'Sí';
            $prestacionExcel->devolucion        = $prestacion->Devol === 0 ? '-' : 'Sí';
            $prestacionExcel->forma             = $prestacion->Forma === 0 ? '-' : 'Sí';
            $prestacionExcel->sinesc            = $prestacion->SinEsc === 0 ? '-' : 'Sí';

            $prestacionesExcel->push($prestacionExcel);
        }

        return $prestacionesExcel;
    }


    public function getPrestacionesPorIds(){

    return DB::table('prestaciones')
        ->join('pacientes', 'prestaciones.IdPaciente', '=', 'pacientes.Id')
        ->join('clientes', 'prestaciones.IdEmpresa', '=', 'clientes.Id')
        ->leftJoin('prestaciones_comentarios', 'prestaciones.Id', '=', 'prestaciones_comentarios.IdP')
        ->select(
            DB::raw('(SELECT RazonSocial FROM clientes WHERE Id = prestaciones.IdART) AS Art'),
            DB::raw('(SELECT RazonSocial FROM clientes WHERE Id = prestaciones.IdEmpresa) AS RazonSocial'),
            'clientes.ParaEmpresa as ParaEmpresa',
            'clientes.Identificacion as Identificacion',
            'prestaciones.Fecha as FechaAlta',
            'prestaciones.Id as Id',
            'pacientes.Nombre as Nombre',
            'pacientes.Apellido as Apellido',
            'prestaciones.Anulado as Anulado',
            'prestaciones.Pago as Pago',
            'prestaciones.Ausente as Ausente',
            'prestaciones.Incompleto as Incompleto',
            'prestaciones.Devol as Devol',
            'prestaciones.Forma as Forma',
            'prestaciones.SinEsc as SinEsc',
            'prestaciones_comentarios.Obs as ObsEstado'
        )
        ->where('prestaciones.Estado', '=', '1')
        ->whereIn('prestaciones.Id', $this->ids)
        ->orderBy('prestaciones.Id', 'DESC')
        ->get();
    }

    public function getPrestacionesPorFiltros() {

        $filters = $this->filters;
        
        $query = Prestacion::join('pacientes', 'prestaciones.IdPaciente', '=', 'pacientes.Id')
            ->join('clientes as emp', 'prestaciones.IdEmpresa', '=', 'emp.Id')
            ->join('clientes as art', 'prestaciones.IdART', '=', 'art.Id')
            ->leftJoin('prestaciones_comentarios', 'prestaciones.Id', '=', 'prestaciones_comentarios.IdP')
            ->select(
                DB::raw('(SELECT RazonSocial FROM clientes WHERE Id = prestaciones.IdART) AS Art'),
                DB::raw('(SELECT RazonSocial FROM clientes WHERE Id = prestaciones.IdEmpresa) AS empresa'),
                DB::raw("CONCAT(pacientes.Apellido,pacientes.Nombre) AS nombreCompleto"),            
                'emp.ParaEmpresa as ParaEmpresa',
                'emp.Identificacion as Identificacion',
                'prestaciones.Fecha as FechaAlta',
                'prestaciones.Id as Id',
                'pacientes.Nombre as Nombre',
                'pacientes.Apellido as Apellido',
                'prestaciones.Anulado as Anulado',
                'prestaciones.Pago as Pago',
                'prestaciones.FechaVto as FechaVencimiento',
                'prestaciones.Ausente as Ausente',
                'prestaciones.Incompleto as Incompleto',
                'prestaciones.Devol as Devol',
                'prestaciones.Forma as Forma',
                'prestaciones.SinEsc as SinEsc',
                'prestaciones.TipoPrestacion as TipoPrestacion',
                'prestaciones.eEnviado as eEnviado',
                'prestaciones.Estado as Estado',
                'prestaciones_comentarios.Obs as ObsEstado'
                
            )
            ->where('prestaciones.Estado', 1);

         if(!empty($filters->pacempart)) {
            $query->where(function ($query) use ($filters) {
                $query->orwhere('emp.RazonSocial', 'LIKE', '%'. $filters->pacempart .'%')
                    ->orwhere('art.RazonSocial', 'LIKE', '%'. $filters->pacempart .'%')
                    ->orWhere('emp.Identificacion', 'LIKE', '%'. $filters->pacempart .'%')
                    ->orWhere('art.Identificacion', 'LIKE', '%'. $filters->pacempart .'%')
                    ->orWhere('emp.ParaEmpresa', 'LIKE', '%'. $filters->pacempart .'%')
                    ->orWhere('art.ParaEmpresa', 'LIKE', '%'. $filters->pacempart .'%')
                    ->orWhere('emp.NombreFantasia', 'LIKE', '%'. $filters->pacempart .'%')
                    ->orWhere('art.NombreFantasia', 'LIKE', '%'. $filters->pacempart .'%')
                    ->orWhere('pacientes.Nombre', 'LIKE', '%'. $filters->pacempart .'%')
                    ->orWhere('pacientes.Apellido', 'LIKE', '%'. $filters->pacempart .'%')
                    ->orWhere('pacientes.Documento', 'LIKE', '%'. $filters->pacempart .'%')
                    ->orWhere('pacientes.Identificacion', 'LIKE', '%'. $filters->pacempart .'%');
            });
        }

        if (!empty($filters->tipoPrestacion)) {
            $query->where('prestaciones.TipoPrestacion', $filters->tipoPrestacion);
        }
    
        if (!empty($filters->pago)) {
            $query->where('prestaciones.Pago', '=', $filters->pago);
        }
    
        if (!empty($filters->formaPago)) {
            $query->where('prestaciones.SPago', '=', $filters->formaPago);
        }

        if(!empty($filters->eEnviado)){
            $query->where('prestaciones.eEnviado', '=', $filters->eEnviado);
        }

        if (!empty($filters->fechaDesde) && (!empty($filters->fechaHasta))) {
            $query->whereBetween('prestaciones.Fecha', [$filters->fechaDesde, $filters->fechaHasta]);
        }

        if (is_array($filters->estado) && in_array('Incompleto', $filters->estado)) {
            $query->where('prestaciones.Incompleto', '1');
        }

        if (is_array($filters->estado) && in_array('Anulado', $filters->estado)) {
            $query->where('prestaciones.Anulado', '1');
        }

        if (is_array($filters->estado) && in_array('Ausente', $filters->estado)) {
            $query->where('prestaciones.Ausente', '1');
        }

        if (is_array($filters->estado) && in_array('Forma', $filters->estado)) {
            $query->where('prestaciones.Forma', '1');
        }

        if (is_array($filters->estado) && in_array('SinEsc', $filters->estado)) {
            $query->where('prestaciones.SinEsc', '1');
        }

        if (is_array($filters->estado) && in_array('Devol', $filters->estado)) {
            $query->where('prestaciones.Devol', '1');
        }
    
        if (is_array($filters->estado) && in_array('RxPreliminar', $filters->estado)) {
            $query->where('prestaciones.RxPreliminar', '1');
        }

        if (is_array($filters->estado) && in_array('Cerrado', $filters->estado)) {
            $query->where('prestaciones.Cerrado', '1');
        }

        if (is_array($filters->estado) && in_array('Abierto', $filters->estado)) {
            $query->where('prestaciones.Cerrado', '0')
                ->where('prestaciones.Finalizado', '0');
        }

        if (is_array($filters->estado) && in_array('Cerrado', $filters->estado)) {
            $query->where('prestaciones.Cerrado', '1');
        }

        if (!empty($filters->finalizado)) {
            $query->where('prestaciones.Finalizado', '=', $filters->finalizado);
            return $query;
        }
    
        if (!empty($filters->facturado)) {
            $query->where('prestaciones.Facturado', '=', $filters->facturado);
        }
    
        if (!empty($filters->entregado)) {
            $query->where('prestaciones.Entregado', '=', $filters->entregado);
        }
    
        return $query->get();
    }

}