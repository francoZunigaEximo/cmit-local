<?php

namespace App\Helpers;

use App\Models\ExamenCuentaIt;
use App\Models\ItemPrestacion;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\Calculation\TextData\Replace;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

trait ToolsReportes
{
    private static $RUTATEMPORAL = "app/public/";
    private static $RUTAPUBLICA = "/";

    public function AnexosFormulariosPrint(int $id): mixed
    {
        //verifico si hay anexos con formularios a imprimir
	    // $query="Select e.Id From itemsprestaciones ip,examenes e Where e.Id=ip.IdExamen and e.IdReporte <> 0 and ip.Anulado=0 and e.Evaluador=1 and  ip.IdPrestacion=$idprest LIMIT 1";	$rs=mysql_query($query,$conn);

        return ItemPrestacion::join('examenes', 'itemsprestaciones.IdExamen', '=', 'examenes.Id')
                ->select('examenes.Id as Id')
                ->whereNot('examenes.IdReporte', 0)
                ->where('itemsprestaciones.Anulado', 0)
                ->where('examenes.Evaluador', 1)
                ->where('itemsprestaciones.IdPrestacion', $id)
                ->first();
    }

    public function checkExCtaImpago(int $idPrestacion): mixed
    {
        return ExamenCuentaIt::join('prestaciones', 'pagosacuenta_it.IdPrestacion', '=', 'prestaciones.Id')
            ->join('pagosacuenta', 'pagosacuenta_it.IdPago', '=', 'pagosacuenta.Id')
            ->where('pagosacuenta_it.IdPrestacion', $idPrestacion)->where('pagosacuenta.Pagado', 0)->count();
    }

    public function folderTempClean(): void
    {
        $deleteFiles = ['file-', 'AINF', 'merge_']; 
        
        $files = Storage::disk('public')->files('temp'); 
        
        foreach ($files as $file) {
            
            foreach ($deleteFiles as $deleteFile) {
                if (Str::startsWith(basename($file), $deleteFile)) {
            
                    Storage::disk('public')->delete($file);
                    break; 
                }
            }
        }
    }

    public function generarArchivo($spreadsheet, $nombre)
    {
        $filePath = storage_path(self::$RUTATEMPORAL.$nombre);
 
        $writer = new Xlsx($spreadsheet);
        $writer->save($filePath);
        chmod($filePath, 0777);

        //modificacion para que la ruta pase de /app/
        $filePath = str_replace('/app/public','',$filePath);

        return response()->json(['filePath' => $filePath, 'msg' => 'Se ha generado correctamente el reporte ', 'estado' => 'success']);
    }

    public function getDBDetalleYCompleto(array $ids, array $filtros): mixed
    {
        $query = DB::table('prestaciones')
        ->join('pacientes', 'prestaciones.IdPaciente', '=', 'pacientes.Id')
        ->leftJoin('clientes as emp', 'prestaciones.IdEmpresa', '=', 'emp.Id')
        ->leftJoin('clientes as art', 'prestaciones.IdART', '=', 'art.Id')
        ->leftJoin('itemsprestaciones', 'prestaciones.Id', '=', 'itemsprestaciones.IdPrestacion')
        ->leftJoin('examenes', 'examenes.Id', '=', 'itemsprestaciones.IdExamen')
        ->leftJoin('profesionales as efector', 'efector.Id', '=', 'itemsprestaciones.IdProfesional')
        ->leftJoin('profesionales as informador', 'informador.Id', '=', 'itemsprestaciones.IdProfesional2')
        ->join('proveedores', 'proveedores.Id', '=', 'efector.IdProveedor')
        ->leftJoin('itemsfacturaventa', 'prestaciones.Id', '=', 'itemsfacturaventa.IdPrestacion')
        ->leftJoin('facturasventa', 'itemsfacturaventa.IdFactura', '=', 'facturasventa.Id') 
        ->leftJoin('prestaciones_comentarios', 'prestaciones.Id', '=', 'prestaciones_comentarios.IdP')
        ->leftJoin('itemsprestaciones_info', 'itemsprestaciones.Id', '=', 'itemsprestaciones_info.IdIP')
        ->select( 
            'pacientes.Documento as DNI',
            DB::raw('CONCAT(pacientes.Apellido, " ", pacientes.Nombre) as Paciente'),
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
            'itemsprestaciones.FechaAsignadoI as asignadoI',
            'itemsprestaciones.HoraAsignado as horaAsignado',
            'itemsprestaciones.HoraAsignadoI as horaAsignadoI',
            'itemsprestaciones.FechaPagado as pagadoEfector',
            'itemsprestaciones.FechaPagado2 as pagadoInformador',
            'itemsprestaciones_info.Obs as ObsInformador',
            DB::raw('CONCAT(efector.Apellido, " ", efector.Nombre) as Efector'),
            DB::raw('CONCAT(informador.Apellido, " ", informador.Nombre) as Informador'),
            'examenes.Nombre as Examen',
            'proveedores.Nombre as EspecialidadEfector',
            'emp.RazonSocial as EmpresaRazonSocial',
            'emp.ParaEmpresa as EmpresaParaEmp',
            'emp.Identificacion as EmpresaIdentificacion',
            'facturasventa.Tipo as Tipo',
            'facturasventa.Sucursal as Sucursal',
            'facturasventa.NroFactura as NroFactura',
            'art.RazonSocial as ArtRazonSocial',
        )
        ->where('prestaciones.Estado', 1)
        ->whereIn('prestaciones.Id', $ids);

        if (empty($filtros)) {
            return $query->orderBy('prestaciones.Id', 'DESC')
            //->groupBy('prestaciones.Id')
            ->get();
        }
        else {
            return $this->applyFilters($query, $filtros);
        }
    }

    private function applyFilters($query, $filters) 
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