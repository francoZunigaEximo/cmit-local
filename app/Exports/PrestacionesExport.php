<?php

namespace App\Exports;

use App\Models\Prestacion;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use stdClass;
use Carbon\Carbon;

class PrestacionesExport implements FromCollection,WithHeadings
{

    protected $ids;
    protected $filters;
    protected $tipo;

    function __construct($ids, $filters, $tipo) {
        $this->ids      = $ids;
        $this->filters  = $filters;
        $this->tipo     = $tipo;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function headings(): array
    {
        switch ($this->tipo) {
            case 'simple':
                return [
                    'Fecha',
                    'Prestacion',
                    'Tipo',
                    'Paciente',
                    'DNI',
                    'Cliente',
                    'Empresa',
                    'ART',
                    'Cerrado',
                    'Finalizado',
                    'Entregado',
                    'eEnviado',
                    'Facturado',
                    'Factura',
                    'Forma Pago',
                    'Vencimiento',
                    'Evaluacion',
                    'Califiacion',
                    'Obs.Resultado',
                    'Anulada',
                    'Obs.Anulada',
                    'Nro.CE',
                    'C.Costos',
                    'INC',
                    'AUS',
                    'FOR',
                    'DEV',
                    'OBS ESTADOS'
                ];
                break;
            case 'detallado':
                return [
                    'Fecha',
                    'Prestacion',
                    'Tipo',
                    'Paciente',
                    'DNI',
                    'Cliente',
                    'Empresa',
                    'ART',
                    'C.Costos',
                    'Nro.CE',
                    'Prst Anulada',
                    'Obs.Anulada',
                    'Examen',
                    'Exa Anulado',
                    'INC',
                    'AUS',
                    'FOR',
                    'DEV',
                    'OBS ESTADOS'
                ];
                break;
            case 'completo':
                return [
                    'Fecha',
                    'Prestacion',
                    'Tipo',
                    'Paciente',
                    'DNI',
                    'Cliente',
                    'Empresa',
                    'ART',
                    'C.Costos',
                    'Nro.CE',
                    'Anulada',
                    'Obs.Anulada',
                    'Cerrado',
                    'Finalizado',
                    'Entregado',
                    'eEnviado',
                    'Vencimiento',
                    'Evaluacion',
                    'Calificacion',
                    'Obs.Resultado',
                    'Examen',
                    'Anulado',
                    'INC',
                    'AUS',
                    'FOR',
                    'DEV',
                    'OBS ESTADOS',
                    'Facturado',
                    'Factura',
                    'Forma Pago',
                    'Especialidad Efector',
                    'Efector',
                    'Asignado',
                    'Pagado Ef.',
                    'Informador',
                    'Pagado Inf.',
                    'Obs. Examen',
                    'Obs. Efector',
                    'Obs. Informador',
                    'Obs. Privadas',
                ];
                break;
                case 'basico':
                    return [
                        'Fecha',
                        'Nro',
                        'Tipo',
                        'Empresa',
                        'ParaEmpresa',
                        'ART',
                        'Estado',
                        'eEnv',
                        'INC',
                        'AUS',
                        'FOR',
                        'DEV',
                        'FP',
                        'FC'
                    ];
                    break;
        }
    }
    
    public function collection()
    {
        $prestacionesExcel = collect();

        switch ($this->tipo) {
            case 'simple':
                $prestacionesExcel = $this->getInformeSimple();
                break;
            case 'detallado':
                $prestacionesExcel = $this->getInformeDetallado();
                break;
            case 'completo':
                $prestacionesExcel = $this->getInformeCompleto();
                break;
            case 'basico':
                $prestacionesExcel = $this->getInformeBasico();
                break;
        }

        return $prestacionesExcel;
    }

    public function getInformeBasico()
    {
        $prestacionesExcel = collect();
        $prestaciones = $this->getBasico();

        foreach ($prestaciones as $prestacion) {

            $prestacionExcel = new stdClass();

            $prestacionExcel->fecha             = Carbon::parse($prestacion->Fecha)->format('d-m-Y') ?? '-';
            $prestacionExcel->prestacion        = $prestacion->Id ?? '-';
            $prestacionExcel->tipoPrestacion    = $prestacion->TipoPrestacion ?? '-';
            $prestacionExcel->empresa           = $prestacion->empresa->RazonSocial ?? '-';
            $prestacionExcel->paraEmpresa       = $prestacion->empresa->ParaEmpresa ?? '-';
            $prestacionExcel->art               = $prestacion->art->RazonSocial ?? '-';
            $prestacionExcel->estado            = $prestacion->Estado === 1 ? 'Habilitado' : 'Inhabilitado';
            $prestacionExcel->eEnviado          = $prestacion->eEnviado === 1 ? "Si" : "No";
            $prestacionExcel->incompleto        = $prestacion->Incompleto === 1 ? "Si" : "No";
            $prestacionExcel->ausente           = $prestacion->Ausente === 1 ? "Si" : "No";
            $prestacionExcel->forma             = $prestacion->Forma === 1 ? "Si" : "No";
            $prestacionExcel->devolucion        = $prestacion->Devol === 1 ? "Si" : "No";
            
            switch ($prestacion->Pago) {
                case "B":
                    $prestacionExcel->formaPago = 'Ctdo.';
                    break;
                case "C":
                    $prestacionExcel->formaPago = 'CCorriente';
                    break;
                case "P":
                    $prestacionExcel->formaPago = 'ExCuenta';
                    break;
                default:
                    $prestacionExcel->formaPago = '-';
                    break;
            }

            $prestacionExcel->facturacion       = $prestacion->Facturado === 1 ? "Si" : "No";

            $prestacionesExcel->push($prestacionExcel);
        }

        return $prestacionesExcel;
    }

    public function getInformeSimple(){

        $prestacionesExcel = collect();
        $prestaciones = $this->getPrestaciones();

        foreach ($prestaciones as $prestacion) {

            $prestacionExcel = new stdClass();

            $prestacionExcel->fecha             = Carbon::parse($prestacion->FechaAlta)->format('d-m-Y') ?? '-';
            $prestacionExcel->prestacion        = $prestacion->Id ?? '-';
            $prestacionExcel->tipo              = $prestacion->TipoPrestacion ?? '-';
            $prestacionExcel->paciente          = $prestacion->Apellido . " " .  $prestacion->Nombre ?? '-';
            $prestacionExcel->dni               = $prestacion->DNI ?? '-';
            $prestacionExcel->cliente           = $prestacion->EmpresaRazonSocial . ' ' . $prestacion->EmpresaIdentificacion ?? '-'; 
            $prestacionExcel->empresa           = $prestacion->EmpresaParaEmp ?? '-';
            $prestacionExcel->art               = $prestacion->ArtRazonSocial ?? '-';
            $prestacionExcel->cerrado           = $prestacion->Cerrado ?? '-';
            $prestacionExcel->finalizado        = $prestacion->Finalizado ?? '-';
            $prestacionExcel->entregado         = $prestacion->Entregado ?? '-';
            $prestacionExcel->eEnviado          = $prestacion->eEnviado ?? '-';
            $prestacionExcel->facturado         = $prestacion->Facturado ?? '-';
            $prestacionExcel->factura           = $prestacion->Tipo.(sprintf('%05d', $prestacion->Sucursal))."-".$prestacion->NroFactura ?? '-';

            switch ($prestacion->Pago) {
                case "B":
                    $prestacionExcel->formaPago = 'Ctdo.';
                    break;
                case "C":
                    $prestacionExcel->formaPago = 'CCorriente';
                    break;
                case "P":
                    $prestacionExcel->formaPago = 'ExCuenta';
                    break;
                default:
                    $prestacionExcel->formaPago = '-';
                    break;
            }

            $prestacionExcel->vencimiento       = $prestacion->FechaVto ?? '-';
            $prestacionExcel->evaluacion        = $prestacion->Evaluacion ?? '-';
            $prestacionExcel->calificacion      = $prestacion->Calificacion ?? '-';
            $prestacionExcel->obsResultado      = $prestacion->Observaciones ?? '-';
            $prestacionExcel->anulada           = $prestacion->Anulado ?? '-';
            $prestacionExcel->obsAnulado        = $prestacion->ObsAnulado ?? '-';
            $prestacionExcel->nroCe             = $prestacion->NroCEE ?? '-';
            $prestacionExcel->ccostos           = $prestacion->CCosto ?? '-';
            $prestacionExcel->inc               = $prestacion->Incompleto === 0 ? '-' : 'Sí';
            $prestacionExcel->aus               = $prestacion->Ausente === 0 ? '-' : 'Sí';
            $prestacionExcel->for               = $prestacion->Forma === 0 ? '-' : 'Sí';
            $prestacionExcel->dev               = $prestacion->Devol === 0 ? '-' : 'Sí';
            $prestacionExcel->observacionEstado = $prestacion->ObsEstado ?? '-';

            $prestacionesExcel->push($prestacionExcel);
        }

        return $prestacionesExcel;
    }

    public function getInformeDetallado(){
        
        $prestacionesExcel = collect();
        $prestaciones = $this->getExamenes();

        foreach ($prestaciones as $prestacion) {

            $prestacionExcel = new stdClass();

            $prestacionExcel->fecha             = Carbon::parse($prestacion->FechaAlta)->format('d-m-Y') ?? '-'; // Formatear
            $prestacionExcel->prestacion        = $prestacion->Id ?? '-';
            $prestacionExcel->tipo              = $prestacion->TipoPrestacion ?? '-';
            $prestacionExcel->paciente          = $prestacion->Apellido . " " .  $prestacion->Nombre ?? '-';
            $prestacionExcel->dni               = $prestacion->DNI ?? '-';
            $prestacionExcel->cliente           = $prestacion->EmpresaRazonSocial . ' ' . $prestacion->EmpresaIdentificacion ?? '-'; 
            $prestacionExcel->empresa           = $prestacion->EmpresaParaEmp ?? '-';
            $prestacionExcel->art               = $prestacion->ArtRazonSocial ?? '-';
            $prestacionExcel->ccostos           = $prestacion->CCosto ?? '-';
            $prestacionExcel->nroCe             = $prestacion->NroCEE ?? '-';
            $prestacionExcel->anulada           = $prestacion->Anulado === 1 ? 'Sí' : '-';
            $prestacionExcel->obsAnulado        = $prestacion->ObsAnulado ?? '-';
            $prestacionExcel->examen            = $prestacion->Examen ?? '-';
            $prestacionExcel->exaAnulado        = $prestacion->ExaAnulado ?? '-'; // Consultar Exa Anulado 
            $prestacionExcel->inc               = ($prestacion->Incompleto === 1 ? 'Sí' : '-');
            $prestacionExcel->aus               = $prestacion->Ausente === 1 ? 'Sí' : '-';
            $prestacionExcel->for               = $prestacion->Forma === 1 ? 'Sí' : '-';
            $prestacionExcel->dev               = $prestacion->Devol === 1 ? 'Sí' : '-';
            $prestacionExcel->observacionEstado = $prestacion->ObsEstado ?? '-';

            $prestacionesExcel->push($prestacionExcel);
        }

        return $prestacionesExcel;
    }

    public function getInformeCompleto(){
        
        $prestacionesExcel = collect();
        $prestaciones = $this->getExamenes();
        
        foreach ($prestaciones as $prestacion) {

            $prestacionExcel = new stdClass();

            $prestacionExcel->fecha             = Carbon::parse($prestacion->FechaAlta)->format('d-m-Y') ?? '-'; // Formatear
            $prestacionExcel->prestacion        = $prestacion->Id ?? '-';
            $prestacionExcel->tipo              = $prestacion->TipoPrestacion ?? '-';
            $prestacionExcel->paciente          = $prestacion->Apellido . " " .  $prestacion->Nombre ?? '-';
            $prestacionExcel->dni               = $prestacion->DNI ?? '-';
            $prestacionExcel->cliente           = $prestacion->EmpresaRazonSocial . ' ' . $prestacion->EmpresaIdentificacion ?? '-';
            $prestacionExcel->empresa           = $prestacion->EmpresaParaEmp ?? '-';
            $prestacionExcel->art               = $prestacion->ArtRazonSocial ?? '-';
            $prestacionExcel->ccostos           = $prestacion->CCosto ?? '-';
            $prestacionExcel->nroCe             = $prestacion->NroCEE ?? '-';
            $prestacionExcel->anulada           = $prestacion->Anulado ?? '-';
            $prestacionExcel->obsAnulado        = $prestacion->ObsAnulado ?? '-';
            $prestacionExcel->cerrado           = $prestacion->Cerrado ?? '-';
            $prestacionExcel->finalizado        = $prestacion->Finalizado ?? '-';
            $prestacionExcel->entregado         = $prestacion->Entregado ?? '-';
            $prestacionExcel->eEnviado          = $prestacion->eEnviado ?? '-';
            $prestacionExcel->vencimiento       = $prestacion->FechaVto ?? '-';
            $prestacionExcel->evaluacion        = $prestacion->Evaluacion ?? '-';
            $prestacionExcel->calificacion      = $prestacion->Calificacion ?? '-';
            $prestacionExcel->obsResultado      = $prestacion->Observaciones ?? '-';
            $prestacionExcel->examen            = $prestacion->Examen ?? '-';
            $prestacionExcel->anulado           = $prestacion->ExaAnulado ?? '-';
            $prestacionExcel->inc               = $prestacion->Incompleto === 1 ? 'Sí' : '-';
            $prestacionExcel->aus               = $prestacion->Ausente === 1 ? 'Sí' : '-';
            $prestacionExcel->for               = $prestacion->Forma === 1 ? 'Sí' : '-';
            $prestacionExcel->dev               = $prestacion->Devol === 1 ? 'Sí' : '-';
            $prestacionExcel->observacionEstado = $prestacion->ObsEstado ?? '-';
            $prestacionExcel->facturado         = $prestacion->Facturado ?? '-';
            $prestacionExcel->factura           = $prestacion->Tipo."".(sprintf('%05d', $prestacion->Sucursal))."-".$prestacion->NroFactura ?? '-';

            switch ($prestacion->Pago) {
                case "B":
                    $prestacionExcel->formaPago = 'Ctdo.';
                    break;
                case "C":
                    $prestacionExcel->formaPago = 'CCorriente';
                    break;
                case "P":
                    $prestacionExcel->formaPago = 'ExCuenta';
                    break;
                default:
                    $prestacionExcel->formaPago = '-';
                    break;
            }

            $prestacionExcel->facturado             = $prestacion->Facturado ?? '-';
            $prestacionExcel->EspecialidadEfector   = $prestacion->EspecialidadEfector ?? '-';
            $prestacionExcel->Efector               = $prestacion->apellidoEfector . " " . $prestacion->nombreEfector;
            $prestacionExcel->asignado              = $prestacion->asignado ?? '-';
            $prestacionExcel->Informador            = $prestacion->apellidoInformador . " " . $prestacion->nombreInformador ??'-';
            $prestacionExcel->Pagadoef              = $prestacion->pagadoEfector ?? '-';
            $prestacionExcel->PagadoInf             = $prestacion->pagadoInformador ?? '-';
            $prestacionExcel->ObsExamen             = $prestacion->ObsExamen ?? '-';
            $prestacionExcel->ObsEfector            = '-'; // Consultar
            $prestacionExcel->ObsInformador         = $prestacion->ObsInformador ?? '-';
            $prestacionExcel->ObsPrivadas           = $prestacion->ObsEstado ?? '-';
            $prestacionesExcel->push($prestacionExcel);
            
        }

        return $prestacionesExcel;
    }

    public function getPrestaciones(){

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
            'pacientes.Nombre as Nombre',
            'pacientes.Apellido as Apellido',
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
        ->where('prestaciones.Estado', '1');

         if (empty($this->filters)) {
            return $query->whereIn('prestaciones.Id', $this->ids)
            ->orderBy('prestaciones.Id', 'DESC')
            ->get();
         }
         else {

            return $this->applyFilters($query);
         }
    }

    public function getExamenes(){

        $query = DB::table('prestaciones')
        ->join('pacientes', 'prestaciones.IdPaciente', '=', 'pacientes.Id')
        ->join('clientes as emp', 'prestaciones.IdEmpresa', '=', 'emp.Id')
        ->join('clientes as art', 'prestaciones.IdART', '=', 'art.Id')
        ->join('itemsprestaciones', 'prestaciones.Id', '=', 'itemsprestaciones.IdPrestacion')
        ->join('examenes', 'examenes.Id', '=', 'itemsprestaciones.IdExamen')
        ->join('profesionales as efector', 'efector.Id', '=', 'itemsprestaciones.IdProfesional')
        ->leftJoin('profesionales as informador', 'informador.Id', '=', 'itemsprestaciones.IdProfesional2')
        ->join('proveedores', 'proveedores.Id', '=', 'efector.IdProveedor')
        ->leftJoin('itemsfacturaventa', 'prestaciones.Id', '=', 'itemsfacturaventa.IdPrestacion')
        ->leftJoin('facturasventa', 'itemsfacturaventa.IdFactura', '=', 'facturasventa.Id') 
        ->leftJoin('prestaciones_comentarios', 'prestaciones.Id', '=', 'prestaciones_comentarios.IdP')
        ->leftJoin('itemsprestaciones_info', 'itemsprestaciones.Id', '=', 'itemsprestaciones_info.IdIP')
        ->select( 
            'pacientes.Documento as DNI',
            'pacientes.Nombre as Nombre',
            'pacientes.Apellido as Apellido',
            'prestaciones.NroCEE as NroCEE',
            'prestaciones.Anulado as Anulado',
            'prestaciones.ObsAnulado as ObsAnulado', 
            'prestaciones.FechaCierre as Cerrado',
            'prestaciones.FechaFinalizado as Finalizado',
            'prestaciones.FechaEntrega as Entregado',
            'prestaciones.FechaEnviado as eEnviado',  
            'prestaciones.FechaFact as Facturado',
            'prestaciones.FechaVto as FechaVto',
            'prestaciones.Evaluacion as Evaluacion', 
            'prestaciones.Calificacion as Calificacion',
            'prestaciones.Observaciones as Observaciones', 
            'prestaciones.Incompleto as Incompleto', 
            'prestaciones.Ausente as Ausente',
            'prestaciones.Forma as Forma',
            'prestaciones.Devol as Devol',
            'prestaciones.Pago as Pago',
            'prestaciones_comentarios.Obs as ObsEstado',
            'prestaciones.Fecha as FechaAlta',
            'prestaciones.Id as Id',
            'prestaciones.TipoPrestacion as TipoPrestacion',
            'itemsprestaciones.ObsExamen as ObsExamen',
            'itemsprestaciones.Anulado as ExaAnulado',
            'itemsprestaciones.FechaAsignado as asignado',
            'itemsprestaciones.FechaPagado as pagadoEfector',
            'itemsprestaciones.FechaPagado2 as pagadoInformador',
            'itemsprestaciones_info.Obs as ObsInformador',
            'efector.Nombre as nombreEfector',
            'efector.Apellido as apellidoEfector',
            'informador.Nombre as nombreInformador',
            'informador.Apellido as apellidoInformador',
            'examenes.Nombre as Examen',
            'proveedores.Nombre as EspecialidadEfector',
            'emp.RazonSocial as EmpresaRazonSocial',
            'emp.ParaEmpresa as EmpresaParaEmp',
            'emp.Identificacion as EmpresaIdentificacion',
            'facturasventa.Tipo as Tipo',
            'facturasventa.Sucursal as Sucursal',
            'facturasventa.NroFactura as NroFactura',
            'art.RazonSocial as ArtRazonSocial'
        )
        ->where('prestaciones.Estado', '=', '1');

        if (empty($this->filters)) {
            return $query->whereIn('prestaciones.Id', $this->ids)
            ->orderBy('prestaciones.Id', 'DESC')
            ->get();
        }
        else {
            return $this->applyFilters($query);
        }
    }

    public function applyFilters($query) {

        $filters = $this->filters;

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

    private function getBasico()
    {
        return Prestacion::with(['empresa','art'])->whereIn('Id', $this->ids)->orderBy('Id', 'DESC')->get();
    }
}