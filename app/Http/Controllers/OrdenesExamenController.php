<?php

namespace App\Http\Controllers;

use App\Models\ItemPrestacion;
use App\Traits\ObserverItemsPrestaciones;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;
use Illuminate\Support\Str;
use DateTime;
use DateInterval;

class OrdenesExamenController extends Controller
{
    use ObserverItemsPrestaciones;

    public function index()
    {
        return view('layouts.ordenesExamen.index');
    }

    public function search(Request $request): mixed
    {   

        if($request->ajax())
        {
            $cacheKey = 'search:' . $request->fechaDesde . ':' . $request->fechaHasta . ':' . $request->especialidad;

            $data = Cache::get($cacheKey);

            if (!$data) {

                $query = ItemPrestacion::join('prestaciones', 'itemsprestaciones.IdPrestacion', '=', 'prestaciones.Id')
                ->join('examenes', 'itemsprestaciones.IdExamen', '=', 'examenes.Id')
                ->join('proveedores', 'examenes.IdProveedor', '=', 'proveedores.Id')
                ->join('clientes', 'prestaciones.IdEmpresa', '=', 'clientes.Id')
                ->join('clientes as art', 'prestaciones.IdART', '=', 'art.Id')
                ->join('pacientes', 'prestaciones.IdPaciente', '=', 'pacientes.Id')
                ->select(
                    'itemsprestaciones.Id as IdItem',
                    'itemsprestaciones.Fecha as Fecha',
                    'itemsprestaciones.IdProfesional as IdProfesional',
                    'proveedores.Nombre as Especialidad',
                    'proveedores.Id as IdEspecialidad',
                    'prestaciones.Id as IdPrestacion',
                    'clientes.RazonSocial as Empresa',
                    'pacientes.Apellido as pacApellido',
                    'pacientes.Nombre as pacNombre',
                    'pacientes.Documento as Documento',
                    'examenes.Nombre as Examen',
                )->whereNot('itemsprestaciones.Id', 0);

                $query = $this->filtrosBasicos($query, $request);

                $query->when(!empty($request->prestacion), function ($query) use ($request) {
                    $query->where('prestaciones.Id', $request->prestacion);
                });

                $query->when(!empty($request->paciente), function ($query) use ($request) {
                    $query->where('pacientes.Id', $request->paciente);
                });

                $query->when(!empty($request->examen), function ($query) use ($request) {
                    $query->where('examenes.Id', $request->examen);
                });

                $filtrado = $query->where('itemsprestaciones.IdProfesional', 0);

                $result = $this->condicionesComunes($filtrado);

                Cache::put($cacheKey, $result->get(), 60);

            }else{
                $result = collect($data);
            }

            return Datatables::of($result)->make(true);
        }

        return view('layouts.ordenesExamen.index');
    }

    public function searchA(Request $request): mixed
    {
        if($request->ajax())
        {
            $cacheKey = 'searchA:' . $request->fechaDesde . ':' . $request->fechaHasta . ':' . $request->especialidad;

            $data = Cache::get($cacheKey);

            if (!$data) {

                $query = ItemPrestacion::join('prestaciones', 'itemsprestaciones.IdPrestacion', '=', 'prestaciones.Id')
                ->join('examenes', 'itemsprestaciones.IdExamen', '=', 'examenes.Id')
                ->join('proveedores', 'examenes.IdProveedor', '=', 'proveedores.Id')
                ->join('clientes', 'prestaciones.IdEmpresa', '=', 'clientes.Id')
                ->join('clientes as art', 'prestaciones.IdART', '=', 'art.Id')
                ->join('pacientes', 'prestaciones.IdPaciente', '=', 'pacientes.Id')
                ->join('profesionales', 'itemsprestaciones.IdProfesional', '=', 'profesionales.Id')
                ->select(
                    'itemsprestaciones.Id as IdItem',
                    'itemsprestaciones.Fecha as Fecha',
                    'itemsprestaciones.CAdj as Estado',
                    'itemsprestaciones.CInfo as Informado',
                    'itemsprestaciones.IdProfesional as IdProfesional',
                    'proveedores.Nombre as Especialidad',
                    'proveedores.Id as IdEspecialidad',
                    'prestaciones.Id as IdPrestacion',
                    'clientes.RazonSocial as Empresa',
                    'pacientes.Apellido as pacApellido',
                    'pacientes.Nombre as pacNombre',
                    'pacientes.Documento as Documento',
                    'pacientes.Id as IdPaciente',
                    'examenes.Nombre as Examen',
                )->whereNot('itemsprestaciones.Id', 0);

                $query = $this->filtrosBasicos($query, $request);

                $query->when(!empty($request->prestacion), function ($query) use ($request) {
                    $query->where('prestaciones.Id', $request->prestacion);
                });

                $query->when(!empty($request->examen), function ($query) use ($request) {
                    $query->where('examenes.Id', $request->examen);
                });

                $query->when(!empty($request->paciente), function ($query) use ($request) {
                    $query->where('pacientes.Id', $request->paciente);
                });

                $query->when(!empty($request->estados) && ($request->estados === 'abiertos'), function ($query) {
                    $query->whereIn('itemsprestaciones.CAdj', ['0', '1', '2'])
                            ->where('itemsprestaciones.CInfo', 0);
                });

                $query->when(!empty($request->estados) && ($request->estados === 'cerrados'), function ($query) {
                    $query->whereIn('itemsprestaciones.CAdj', ['3', '4', '5']);
                });

                $query->when(!empty($request->estados) && ($request->estados === 'asignados'), function ($query) {
                    $query->where('itemsprestaciones.IdProfesional', '<>', 0)
                        ->havingRaw('(SELECT COUNT(*) FROM archivosefector WHERE IdEntidad = itemsprestaciones.Id) = 0')
                        ->whereIn('itemsprestaciones.CAdj', ['0', '1', '2']);
                });

                $query->when(!empty($request->efectores), function ($query) use ($request){
                    $query->where('itemsprestaciones.IdProfesional', $request->efectores);
                });
            
                $filtrado = $query->whereNot('itemsprestaciones.IdProfesional', 0);

                $result = $this->condicionesComunes($filtrado);

                Cache::put($cacheKey, $result->get(), 60);

            }else{
                $result = collect($data);
            }

            return Datatables::of($result)->make(true);
        }
        
        return view('layouts.ordenesExamen.index');
    }

    public function searchAdj(Request $request): mixed
    {
        if($request->ajax())
        {
            $cacheKey = 'searchAdj:' . $request->fechaDesde . ':' . $request->fechaHasta . ':' . $request->especialidad;

            $data = Cache::get($cacheKey);

            if (!$data) {

                $query = ItemPrestacion::join('prestaciones', 'itemsprestaciones.IdPrestacion', '=', 'prestaciones.Id')
                ->join('examenes', 'itemsprestaciones.IdExamen', '=', 'examenes.Id')
                ->join('proveedores', 'examenes.IdProveedor', '=', 'proveedores.Id')
                ->join('clientes', 'prestaciones.IdEmpresa', '=', 'clientes.Id')
                ->join('clientes as art', 'prestaciones.IdART', '=', 'art.Id')
                ->join('pacientes', 'prestaciones.IdPaciente', '=', 'pacientes.Id')
                ->leftJoin('archivosefector', 'itemsprestaciones.Id', '=', 'archivosefector.IdEntidad')
                ->join('profesionales', 'itemsprestaciones.IdProfesional', '=', 'profesionales.Id')
                ->select(
                    DB::raw('CASE 
                                WHEN proveedores.Multi = 1 THEN "Multi Examen"
                                ELSE examenes.Nombre
                            END AS examen_nombre'),
                    'itemsprestaciones.Id as IdItem',
                    'itemsprestaciones.Fecha as Fecha',
                    'itemsprestaciones.CAdj as Estado',
                    'proveedores.Nombre as Especialidad',
                    'proveedores.Multi as MultiEfector',
                    'prestaciones.Id as IdPrestacion',
                    'clientes.RazonSocial as Empresa',
                    'pacientes.Apellido as pacApellido',
                    'pacientes.Nombre as pacNombre',
                    'profesionales.Apellido as proApellido',
                    'profesionales.Nombre as proNombre',
                    'pacientes.Documento as Documento',
                    'pacientes.Id as IdPaciente',
                    'examenes.Nombre as Examen',
                    'examenes.Id as IdExamen',
                )->whereNot('itemsprestaciones.Id', 0);
        
                $query = $this->filtrosBasicos($query, $request);

                $query->when(!empty($request->efectores), function ($query) use ($request){
                    $query->where('itemsprestaciones.IdProfesional', $request->efectores);
                });

                $query->when(!empty($request->art), function ($query) use ($request) {
                    $query->where('art.Id', $request->art);
                });
                
                $filtrado = $query->where('examenes.Adjunto', 1)
                    ->whereNotExists(function ($query) {
                        $query->select(DB::raw(1))
                            ->from('archivosefector')
                            ->whereRaw('archivosefector.IdEntidad = itemsprestaciones.Id');
                    })
                    ->whereIn('itemsprestaciones.CAdj', [1,4])
                    ->whereNot('itemsprestaciones.IdProfesional', 0)
                    ->groupBy(DB::raw('
                        CASE 
                            WHEN proveedores.Multi = 1 THEN prestaciones.Id
                            ELSE itemsprestaciones.Id
                        END')
                    );

                $result = $this->condicionesComunes($filtrado);
                Cache::put($cacheKey, $result->get(), 60);
            }else{
                $result = collect($data);
            }
            return Datatables::of($result)->make(true);
        }
        return view('layouts.ordenesExamen.index');
    }

    public function searchInf(Request $request): mixed
    {
        if($request->ajax())
        {
            $cacheKey = 'searchInf:' . $request->fechaDesde . ':' . $request->fechaHasta . ':' . $request->especialidad;

            $data = Cache::get($cacheKey);

            if (!$data) {

                $query = ItemPrestacion::join('prestaciones', 'itemsprestaciones.IdPrestacion', '=', 'prestaciones.Id')
                ->join('examenes', 'itemsprestaciones.IdExamen', '=', 'examenes.Id')
                ->join('proveedores', 'examenes.IdProveedor2', '=', 'proveedores.Id')
                ->join('clientes', 'prestaciones.IdEmpresa', '=', 'clientes.Id')
                ->join('clientes as art', 'prestaciones.IdART', '=', 'art.Id')
                ->join('pacientes', 'prestaciones.IdPaciente', '=', 'pacientes.Id')
                ->leftJoin('archivosefector', 'itemsprestaciones.Id', '=', 'archivosefector.IdEntidad')
                ->join('profesionales', 'itemsprestaciones.IdProfesional', '=', 'profesionales.Id')
                ->select(
                    'itemsprestaciones.Id as IdItem',
                    'itemsprestaciones.Fecha as Fecha',
                    'itemsprestaciones.CAdj as Estado',
                    'itemsprestaciones.CInfo as Informado',
                    'itemsprestaciones.IdProfesional as IdProfesional',
                    'proveedores.Nombre as Especialidad',
                    'proveedores.Id as IdEspecialidad',
                    'proveedores.Multi as MultiEfector',
                    'proveedores.MultiE as MultiInformador',
                    'prestaciones.Id as IdPrestacion',
                    'clientes.RazonSocial as Empresa',
                    DB::raw("CONCAT(pacientes.Apellido, ' ', pacientes.Nombre) as NombreCompleto"),
                    DB::raw("CONCAT(profesionales.Apellido, ' ', profesionales.Nombre) as NombreProfesional"),
                    'pacientes.Documento as Documento',
                    'pacientes.Id as IdPaciente',
                    'examenes.Nombre as Examen',
                    'examenes.Id as IdExamen',
                )->whereNot('itemsprestaciones.Id', 0);

                $query = $this->filtrosBasicos($query, $request);

                $query->when(!empty($request->prestacion), function ($query) use ($request) {
                    $query->where('prestaciones.Id', $request->prestacion);
                });

                $query->when(!empty($request->paciente), function ($query) use ($request) {
                    $query->where('pacientes.Id', $request->paciente);
                });

                $query->when(!empty($request->examen), function ($query) use ($request) {
                    $query->where('examenes.Id', $request->examen);
                });

                $filtrado = $query->whereNot('itemsprestaciones.IdProfesional', 0)
                                ->where('itemsprestaciones.IdProfesional2', 0)
                                ->where('itemsprestaciones.CAdj', 5);

                $result = $this->condicionesComunes($filtrado);
                Cache::put($cacheKey, $result->get(), 10);

            }else{
                $result = collect($data);
            }
            return Datatables::of($result)->make(true);
        }
        return view('layouts.ordenesExamen.index');
    }

    public function searchInfA(Request $request)
    {
        if($request->ajax())
        {
            $cacheKey = 'searchInfA:' . $request->fechaDesde . ':' . $request->fechaHasta . ':' . $request->especialidad;

            $data = Cache::get($cacheKey);

            if (!$data) {
            
                $query = ItemPrestacion::join('prestaciones', 'itemsprestaciones.IdPrestacion', '=', 'prestaciones.Id')
                ->join('examenes', 'itemsprestaciones.IdExamen', '=', 'examenes.Id')
                ->join('proveedores', 'examenes.IdProveedor2', '=', 'proveedores.Id')
                ->join('clientes', 'prestaciones.IdEmpresa', '=', 'clientes.Id')
                ->join('clientes as art', 'prestaciones.IdART', '=', 'art.Id')
                ->join('pacientes', 'prestaciones.IdPaciente', '=', 'pacientes.Id')
                ->leftJoin('archivosefector', 'itemsprestaciones.Id', '=', 'archivosefector.IdEntidad')
                ->join('profesionales', 'itemsprestaciones.IdProfesional', '=', 'profesionales.Id')
                ->select(
                    'itemsprestaciones.Id as IdItem',
                    'itemsprestaciones.Fecha as Fecha',
                    'itemsprestaciones.CAdj as Estado',
                    'itemsprestaciones.CInfo as Informado',
                    'itemsprestaciones.IdProfesional as IdProfesional',
                    'proveedores.Nombre as Especialidad',
                    'proveedores.Id as IdEspecialidad',
                    'proveedores.Multi as MultiEfector',
                    'proveedores.MultiE as MultiInformador',
                    'prestaciones.Id as IdPrestacion',
                    'clientes.RazonSocial as Empresa',
                    DB::raw("CONCAT(pacientes.Apellido, ' ', pacientes.Nombre) as NombreCompleto"),
                    DB::raw("CONCAT(profesionales.Apellido, ' ', profesionales.Nombre) as NombreProfesional"),
                    'pacientes.Documento as Documento',
                    'pacientes.Id as IdPaciente',
                    'examenes.Nombre as Examen',
                    'examenes.Id as IdExamen',
                )->whereNot('itemsprestaciones.Id', 0);

                $query = $this->filtrosBasicos($query, $request);

                $query->when(!empty($request->informadores), function ($query) use ($request){
                    $query->where('itemsprestaciones.IdProfesional', $request->informadores);
                });

                $query->when(!empty($request->examen), function ($query) use ($request) {
                    $query->where('examenes.Id', $request->examen);
                });

                $query->when(!empty($request->prestacion), function ($query) use ($request) {
                    $query->where('prestaciones.Id', $request->prestacion);
                });

                $query->when(!empty($request->paciente), function ($query) use ($request) {
                    $query->where('pacientes.Id', $request->paciente);
                });

                $filtrado = $query->whereNot('itemsprestaciones.IdProfesional', 0)
                                ->whereNot('itemsprestaciones.IdProfesional2', 0)
                                ->where('itemsprestaciones.CAdj', 5)
                                ->whereNot('itemsprestaciones.CInfo', 3)
                                ->where('itemsprestaciones.FechaPagado', '0000-00-00')
                                ->whereNotExists(function ($query) {
                                    $query->select(DB::raw(1))
                                        ->from('itemsprestaciones_info')
                                        ->whereRaw('itemsprestaciones_info.IdIP = itemsprestaciones.Id');
                                });

                $result = $this->condicionesComunes($filtrado);

                Cache::put($cacheKey, $result->get(), 15);

                
            }else{
                $result = collect($data);

            }

            return Datatables::of($result)->make(true);
        }

        return view('layouts.ordenesExamen.index');
    }

    public function searchInfAdj(Request $request)
    {
        if($request->ajax())
        {
            $cacheKey = 'searchInfAdj:' . $request->fechaDesde . ':' . $request->fechaHasta . ':' . $request->especialidad;

            $data = Cache::get($cacheKey);

            if (!$data) {

                $query = ItemPrestacion::join('prestaciones', 'itemsprestaciones.IdPrestacion', '=', 'prestaciones.Id')
                ->join('examenes', 'itemsprestaciones.IdExamen', '=', 'examenes.Id')
                ->join('proveedores', 'examenes.IdProveedor2', '=', 'proveedores.Id')
                ->join('clientes', 'prestaciones.IdEmpresa', '=', 'clientes.Id')
                ->join('clientes as art', 'prestaciones.IdART', '=', 'art.Id')
                ->join('pacientes', 'prestaciones.IdPaciente', '=', 'pacientes.Id')
                ->leftJoin('archivosefector', 'itemsprestaciones.Id', '=', 'archivosefector.IdEntidad')
                ->join('profesionales', 'itemsprestaciones.IdProfesional', '=', 'profesionales.Id')
                ->select(
                    DB::raw('CASE 
                                WHEN proveedores.MultiE = 1 THEN "Multi Examen"
                                ELSE examenes.Nombre
                            END AS examen_nombre'),
                    'itemsprestaciones.Id as IdItem',
                    'itemsprestaciones.Fecha as Fecha',
                    'itemsprestaciones.CAdj as Estado',
                    'proveedores.Nombre as Especialidad',
                    'proveedores.Multi as MultiEfector',
                    'prestaciones.Id as IdPrestacion',
                    'clientes.RazonSocial as Empresa',
                    'pacientes.Apellido as pacApellido',
                    'pacientes.Nombre as pacNombre',
                    'profesionales.Apellido as proApellido',
                    'profesionales.Nombre as proNombre',
                    'pacientes.Documento as Documento',
                    'pacientes.Id as IdPaciente',
                    'examenes.Nombre as Examen',
                    'examenes.Id as IdExamen',
                    'prestaciones.Cerrado as prestacionCerrado'
                )->whereNot('itemsprestaciones.Id', 0);
        
                $query = $this->filtrosBasicos($query, $request);

                $query->when(!empty($request->informadores), function ($query) use ($request){
                    $query->where('itemsprestaciones.IdProfesional2', $request->informadores);
                });

                $query->when(!empty($request->art), function ($query) use ($request) {
                    $query->where('art.Id', $request->art);
                });

                $filtrado = $query->whereIn('itemsprestaciones.CInfo', [0,1])
                                ->whereNot('itemsprestaciones.IdProfesional', 0)
                                ->whereNot('itemsprestaciones.IdProfesional2', 0)
                                ->whereIn('itemsprestaciones.CAdj', [3,5])
                                ->where('proveedores.InfAdj', 1)
                                ->whereNotExists(function ($query) {
                                    $query->select(DB::raw(1))
                                        ->from('archivosinformador')
                                        ->whereRaw('archivosinformador.IdEntidad = itemsprestaciones.Id');
                                })
                                ->groupBy(DB::raw('
                                    CASE 
                                        WHEN proveedores.MultiE = 1 THEN prestaciones.Id
                                        ELSE itemsprestaciones.Id
                                    END')
                                );

                $result = $this->condicionesComunes($filtrado);

                Cache::put($cacheKey, $result->get(), 15);
            
            }else {
                $result = collect($data);
            }
            return Datatables::of($result)->make(true);
        }

        return view('layouts.ordenesExamen.index');
    }

    public function searchPrestacion(Request $request)
    {
        if($request->ajax())
        {
            $cacheKey = 'searchInfA:' . $request->fechaDesde . ':' . $request->fechaHasta . ':' . $request->especialidad;

            $data = Cache::get($cacheKey);

            if (!$data) {

                $query = ItemPrestacion::join('prestaciones', 'itemsprestaciones.IdPrestacion', '=', 'prestaciones.Id')
                ->join('examenes', 'itemsprestaciones.IdExamen', '=', 'examenes.Id')
                ->join('proveedores', 'examenes.IdProveedor2', '=', 'proveedores.Id')
                ->join('clientes', 'prestaciones.IdEmpresa', '=', 'clientes.Id')
                ->join('clientes as art', 'prestaciones.IdART', '=', 'art.Id')
                ->join('pacientes', 'prestaciones.IdPaciente', '=', 'pacientes.Id')
                ->join('profesionales as prof1', 'itemsprestaciones.IdProfesional', '=', 'prof1.Id')
                ->join('profesionales as prof2', 'itemsprestaciones.IdProfesional2', '=', 'prof2.Id')
                ->join('archivosefector', 'itemsprestaciones.Id', '=', 'archivosefector.IdEntidad')
                ->select(
                    'itemsprestaciones.Id as IdItem',
                    'itemsprestaciones.Fecha as Fecha',
                    'itemsprestaciones.CAdj as Efector',
                    'itemsprestaciones.CInfo as Informador',
                    'itemsprestaciones.IdProfesional as IdProfesional',
                    'proveedores.Nombre as Especialidad',
                    'proveedores.Id as IdEspecialidad',
                    'prestaciones.Id as IdPrestacion',
                    'prestaciones.Cerrado as PresCerrado',
                    'prestaciones.Finalizado as PresFinalizado',
                    'prestaciones.Entregado as PresEntregado',
                    'prestaciones.eEnviado as PresEnviado',
                    'clientes.RazonSocial as Empresa',
                    'pacientes.Nombre as NombrePaciente',
                    'pacientes.Apellido as ApellidoPaciente',
                    'prof1.Nombre as NombreProfesional',
                    'prof1.Apellido as ApellidoProfesional',
                    'prof2.Nombre as NombreProfesional2',
                    'prof2.Apellido as ApellidoProfesional2',
                    'examenes.Nombre as Examen',
                    'examenes.Id as IdExamen',
                    'examenes.DiasVencimiento as DiasVencimiento',
                    'examenes.NoImprime as NoImprime'
                )->whereNot('itemsprestaciones.Id', 0);

                $query->when(!empty($request->fechaDesde) && !empty($request->fechaHasta), function ($query) use ($request) {
                    $query->whereBetween('itemsprestaciones.Fecha', [$request->fechaDesde, $request->fechaHasta]);
                });
        
                $query->when(!empty($request->especialidad), function ($query) use ($request) {
                    $query->where('proveedores.Id', $request->especialidad);
                });

                $query->when(!empty($request->especialidad), function ($query) use ($request) {
                    $query->where('proveedores.Id', $request->especialidad);
                });

                //Abierto
                $query->when(!empty($request->estado) && ($request->estado === 'abierto'), function ($query) {
                    $query->addSelect(DB::raw("'Abierto' as estado"))
                        ->where('prestaciones.Finalizado', 0)
                        ->where('prestaciones.Cerrado', 0)
                        ->where('prestaciones.Entregado', 0);
                });

                //Cerrado
                $query->when(!empty($request->estado) && ($request->estado === 'cerrado'), function ($query) {
                    $query->addSelect(DB::raw("'Cerrado' as estado"))
                    ->where('prestaciones.Cerrado', 1)
                    ->where('prestaciones.Finalizado', 0);
                });

                //Finalizado
                $query->when(!empty($request->estado) && ($request->estado === 'finalizado'), function ($query) {
                    $query->addSelect(DB::raw("'Finalizado' as estado"))
                    ->where('prestaciones.Cerrado', 1)
                    ->where('prestaciones.Finalizado', 1);
                });

                //Entregado
                $query->when(!empty($request->estado) && ($request->estado === 'entregado'), function ($query) {
                    $query->addSelect(DB::raw("'Entregado' as estado"))
                    ->where('prestaciones.Cerrado', 1)
                    ->where('prestaciones.Finalizado', 1)
                    ->where('prestaciones.Entregado', 1);
                });

                //eEnviado
                $query->when(!empty($request->estado) && ($request->estado === 'eenviado'), function ($query) {
                    $query->addSelect(DB::raw("'eEnviado' as estado"))
                        ->where('prestaciones.eEnviado', 1);
                });

                $query->when(!empty($request->efector) && ($request->efector === 'pendientes'), function ($query) {
                    $query->addSelect(DB::raw("'Pendiente' as EstadoEfector"))
                    ->whereIn('itemsprestaciones.CAdj', [1,4]);
                });

                $query->when(!empty($request->efector) && ($request->efector === 'cerrados'), function ($query) {
                    $query->addSelect(DB::raw("'Cerrado' as EstadoEfector"))
                        ->whereIn('itemsprestaciones.CAdj', [3,4,5]);
                });

                $query->when(!empty($request->informador) && ($request->informador === 'pendientes'), function ($query) {
                    $query->addSelect(DB::raw("'Pendiente' as EstadoInformador"))
                        ->where('itemsprestaciones.CInfo', 0);
                });

                $query->when(!empty($request->informador) && ($request->informador === 'borrador'), function ($query) {
                    $query->addSelect(DB::raw("'Borrador' as EstadoInformador"))
                        ->where('itemsprestaciones.CInfo', 1);
                });

                $query->when(!empty($request->informador) && ($request->informador === 'pendienteYborrador'), function ($query) {
                    $query->addSelect(DB::raw("
                        CASE itemsprestaciones.CInfo
                            WHEN 0 THEN 'Pendiente'
                            WHEN 1 THEN 'Borrador'
                        END as EstadoInformador
                    "));
                });
                $query->when(!empty($request->informador) && ($request->informador === 'todos'), function ($query) {
                    $query->addSelect(DB::raw("
                    CASE 
                        WHEN itemsprestaciones.CInfo = 0 THEN 'Pendiente'
                        WHEN itemsprestaciones.CInfo = 1 THEN 'Pendiente'
                        WHEN itemsprestaciones.CInfo = 2 THEN 'Borrador'
                        WHEN itemsprestaciones.CInfo = 3 THEN 'Cerrado'
                    END as EstadoInformador
                    "));
                });

                $query->when(!empty($request->profEfector), function ($query) use ($request) {
                    $query->where('itemsprestaciones.IdProfesional', $request->profEfector);
                });

                $query->when(!empty($request->profInformador), function ($query) use ($request) {
                    $query->where('itemsprestaciones.IdProfesional2', $request->profInformador);
                });

                $query->when(!empty($request->tipo) && ($request->tipo === 'interno'), function ($query) {
                    $query->where('proveedores.Externo', 0);
                });

                $query->when(!empty($request->tipo) && ($request->tipo === 'externo'), function ($query) {
                    $query->where('proveedores.Externo', 1);
                });

                $query->when(!empty($request->tipo) && ($request->tipo === 'todos'), function ($query) {
                    $query->whereIn('proveedores.Externo', [0,1]);
                });

                $query->when(!empty($request->ausente) && ($request->ausente === 'ausente'), function ($query) {
                    $query->where('itemsprestaciones.Ausente', 1);
                });

                $query->when(!empty($request->ausente) && ($request->ausente === 'noAusente'), function ($query) {
                    $query->where('itemsprestaciones.Ausente', 0);
                });

                $query->when(!empty($request->ausente) && ($request->ausente === 'todos'), function ($query) {
                    $query->whereIn('itemsprestaciones.Ausente', [0,1]);
                });

                $query->when(!empty($request->adjunto) && ($request->adjunto === 'fisico'), function ($query) {
                    $query->where('examenes.NoImprime', 0);
                });

                $query->when(!empty($request->adjunto) && ($request->adjunto === 'digital'), function ($query) {
                    $query->where('examenes.NoImprime', 1);
                });

                $query->when(!empty($request->examen), function ($query) use ($request) {
                    $query->where('examenes.Id', $request->examen);
                });

                $query->when(!empty($request->pendiente) && ($request->pendiente === 1), function ($query){
                    $query->addSelect(DB::raw("'eEnviado' as estado"))
                        ->whereIn('itemsprestaciones.CAdj', [1,4])
                        ->where('itemsprestaciones.CInfo', 0);
                }); 

                //Sumamos los dias de vencimiento y comparamos
                $query->when(!empty($request->vencido) && ($request->vencido === 1), function ($query){
                    return $query->whereRaw('DATE_ADD(itemsprestaciones.Fecha, INTERVAL examenes.DiasVencimiento DAY) <= CURDATE()');
                });

                $query->when(!empty($request->adjuntoEfector) && ($request->adjuntoEfector === 1), function ($query) use ($request) {
                    $query->whereNot('archivosefector.IdEntidad', $request->IdItem)
                        ->where('examenes.adjunto', 1);
                });
                
                $limit = $query->orderBy('itemsprestaciones.Fecha', 'Desc');
                $result = $this->condicionesComunes($limit);

                Cache::put($cacheKey, $result->get(), 15);

            }else {
                $result = collect($data);
            }
            return Datatables::of($result)->make(true);   
        }

        return view('layouts.ordenesExamen.index');
    }

    public function exportar(Request $request)
    {
        $examenes = $request->Id;

        if (!is_array($examenes)) {
            $examenes = [$examenes];
        }

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);

        $columnas = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M'];

        foreach ($columnas as $columna) {
            $sheet->getColumnDimension($columna)->setAutoSize(true);
        }

        $sheet->getStyle('A1:M1')->getFill()
        ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
        ->getStartColor()->setARGB('CCCCCCCC'); 

        // Agregar bordes gruesos a la celda
        $styleArray = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THICK,
                    'color' => ['argb' => '00000000'],
                ],
            ],
        ];

        $sheet->getStyle('A1:M1')->applyFromArray($styleArray);
        $sheet->getStyle('A1:M1')->getFont()->setBold(true)->setSize(11);

        $sheet->setCellValue('A1', 'Especialidad');
        $sheet->setCellValue('B1', 'Fecha');
        $sheet->setCellValue('C1', 'Prestacion');
        $sheet->setCellValue('D1', 'Empresa');
        $sheet->setCellValue('E1', 'Paciente');
        $sheet->setCellValue('F1', 'Estado');
        $sheet->setCellValue('G1', 'Examen');
        $sheet->setCellValue('H1', 'Efector');
        $sheet->setCellValue('I1', 'Estado Efector');
        $sheet->setCellValue('J1', 'Tipo Adjunto');
        $sheet->setCellValue('K1', 'Informador');
        $sheet->setCellValue('L1', 'Estado Informador');
        $sheet->setCellValue('M1', 'Fecha Vencimiento');

        $fila = 2;
        foreach($examenes as $examen){

            $item = $this->queryPrestacion($examen);

            $estado = $item->PresCerrado === 0 && $item->PresFinalizado === 0
                        ? 'Abierto'
                        : ($item->PresCerrado === 1 && $item->PresFinalizado === 0
                            ? 'Cerrado'
                            : ($item->PresCerrado === 1 && $item->PresFinalizado === 1
                                ? 'Finalizado'
                                : ($item->PresEntregado === 1
                                    ? 'Entregado'
                                    : ($item->PresCerrado === 1 && $item->PresEnviado === 1
                                        ? 'eEnviado'
                                        : '-'))));

            $estadoEfector = in_array($item->Efector, [1,4]) 
                                ? "Pendiente"
                                : (in_array($item->Efector, [3,4,5])
                                    ? 'Cerrado'
                                    : '-');

            $adjunto = $item->NoImprime === 1 ? 'ADJ_D' : 'ADJ_F';
            
            $estadoInformador = in_array($item->Informador, [0,1]) 
                                    ? "Pendiente"
                                    : ($item->Informador === 2
                                        ? 'Borrador'
                                        : ($item->Informador === 3
                                            ? 'Cerrado'
                                            : '-'));

            $itemFecha = new DateTime($item->Fecha);
            $vencimiento = $itemFecha->add(new DateInterval('P' . intval($item->DiasVencimiento) . 'D'));
                                            

            $sheet->setCellValue('A'.$fila, $item->Especialidad);
            $sheet->setCellValue('B'.$fila, Carbon::parse($item->Fecha)->format('d/m/Y'));
            $sheet->setCellValue('C'.$fila, $item->IdPrestacion);
            $sheet->setCellValue('D'.$fila, $item->Empresa);
            $sheet->setCellValue('E'.$fila, $item->NombrePaciente ." ". $item->ApellidoPaciente);
            $sheet->setCellValue('F'.$fila, $estado);
            $sheet->setCellValue('G'.$fila, $item->Examen);
            $sheet->setCellValue('H'.$fila, $item->NombreProfesional . " " . $item->ApellidoProfesional);
            $sheet->setCellValue('I'.$fila, $estadoEfector);
            $sheet->setCellValue('J'.$fila, $adjunto);
            $sheet->setCellValue('K'.$fila, $item->NombreProfesional2 . " " . $item->ApellidoProfesional2);
            $sheet->setCellValue('L'.$fila, $estadoInformador);
            $sheet->setCellValue('M'.$fila, Carbon::parse($vencimiento)->format('d/m/Y'));
            $fila++;
        }

        // Generar un nombre aleatorio para el archivo
        $name = Str::random(10).'.xlsx';

        // Guardar el archivo en la carpeta de almacenamiento
        $filePath = storage_path('app/public/'.$name);

        $writer = new Xlsx($spreadsheet);
        $writer->save($filePath);
        chmod($filePath, 0777);

        // Devolver la ruta del archivo generado
        return response()->json(['filePath' => $filePath]);  

    }

    private function filtrosBasicos($query, $request): mixed
    {
        $query->when(!empty($request->fechaDesde) && !empty($request->fechaHasta), function ($query) use ($request) {
            $query->whereBetween('itemsprestaciones.Fecha', [$request->fechaDesde, $request->fechaHasta]);
        });

        $query->when(!empty($request->especialidad), function ($query) use ($request) {
            $query->where('proveedores.Id', $request->especialidad);
        });

        $query->when(!empty($request->empresa), function ($query) use ($request) {
            $query->where('clientes.Id', $request->empresa);
        });

        return $query;
    }

    private function condicionesComunes($query): mixed
    {
        $query->where('itemsprestaciones.Anulado', 0)
        ->where('proveedores.Inactivo', 0)
        ->where('prestaciones.Estado', 1)
        ->where('clientes.Bloqueado', 0)
        ->where('pacientes.Estado', 1)
        ->where('examenes.Inactivo', 0)
        ->limit(5000)
        ->orderBy('itemsprestaciones.Id', 'DESC');

        return $query;
    }

    private function queryPrestacion(?int $id): mixed
    {
        return ItemPrestacion::join('prestaciones', 'itemsprestaciones.IdPrestacion', '=', 'prestaciones.Id')
        ->join('examenes', 'itemsprestaciones.IdExamen', '=', 'examenes.Id')
        ->join('proveedores', 'examenes.IdProveedor2', '=', 'proveedores.Id')
        ->join('clientes', 'prestaciones.IdEmpresa', '=', 'clientes.Id')
        ->join('clientes as art', 'prestaciones.IdART', '=', 'art.Id')
        ->join('pacientes', 'prestaciones.IdPaciente', '=', 'pacientes.Id')
        ->join('profesionales as prof1', 'itemsprestaciones.IdProfesional', '=', 'prof1.Id')
        ->join('profesionales as prof2', 'itemsprestaciones.IdProfesional2', '=', 'prof2.Id')
        ->select(
            'itemsprestaciones.Id as IdItem',
            'itemsprestaciones.Fecha as Fecha',
            'itemsprestaciones.CAdj as Efector',
            'itemsprestaciones.CInfo as Informador',
            'itemsprestaciones.IdProfesional as IdProfesional',
            'proveedores.Nombre as Especialidad',
            'proveedores.Id as IdEspecialidad',
            'prestaciones.Id as IdPrestacion',
            'prestaciones.Cerrado as PresCerrado',
            'prestaciones.Finalizado as PresFinalizado',
            'prestaciones.Entregado as PresEntregado',
            'prestaciones.eEnviado as PresEnviado',
            'clientes.RazonSocial as Empresa',
            'pacientes.Nombre as NombrePaciente',
            'pacientes.Apellido as ApellidoPaciente',
            'prof1.Nombre as NombreProfesional',
            'prof1.Apellido as ApellidoProfesional',
            'prof2.Nombre as NombreProfesional2',
            'prof2.Apellido as ApellidoProfesional2',
            'examenes.Nombre as Examen',
            'examenes.Id as IdExamen',
            'examenes.DiasVencimiento as DiasVencimiento',
            'examenes.NoImprime as NoImprime'
        )->whereNot('itemsprestaciones.Id', 0)
        ->where('itemsprestaciones.Id', $id)
        ->first();
    }

}