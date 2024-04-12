<?php

namespace App\Http\Controllers;

use App\Models\ItemPrestacion;
use App\Traits\ObserverItemsPrestaciones;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Cache;

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
                Cache::put($cacheKey, $result->get(), 60);

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

                Cache::put($cacheKey, $result->get(), 60);

                
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

                Cache::put($cacheKey, $result->get(), 60);
            
            }else {
                $result = collect($data);
            }
            return Datatables::of($result)->make(true);
        }

        return view('layouts.ordenesExamen.index');
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
        $query/*->where('itemsprestaciones.Anulado', 0)
        ->where('proveedores.Inactivo', 0)
        ->where('prestaciones.Estado', 1)
        ->where('clientes.Bloqueado', 0)
        ->where('pacientes.Estado', 1)
        ->where('examenes.Inactivo', 0)*/
        ->orderBy('proveedores.Id', 'DESC');

        return $query;
    }

}