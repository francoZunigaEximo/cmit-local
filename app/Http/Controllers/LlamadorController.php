<?php

namespace App\Http\Controllers;

use App\Events\AsignarProfesionalEvent;
use App\Events\GrillaEfectoresEvent;
use App\Events\LstProfesionalesEvent;
use App\Events\LstProfInformadorEvent;
use App\Events\LstProfCombinadoEvent;
use App\Models\ArchivoEfector;
use App\Models\ItemPrestacion;
use Illuminate\Http\Request;
use App\Models\Llamador;
use App\Models\Prestacion;
use App\Models\ProfesionalProv;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;
use App\Services\Llamador\Examenes;
use App\Services\Llamador\Profesionales;
use App\Services\ReportesExcel\ReporteExcel;
use App\Services\Roles\Utilidades;

use App\Traits\ObserverItemsPrestaciones;


class LlamadorController extends Controller
{
    protected $profesionales;
    protected $reporteExcel;
    protected $getExamenes;
    protected $utilidades;

    const ADMIN = ['Administrador', 'Admin SR', 'Recepcion SR'];
    const TIPOS = ['Efector', 'Informador', 'Combinado'];

    use ObserverItemsPrestaciones;

    public function __construct(
        Profesionales $profesionales, 
        ReporteExcel $reporteExcel,
        Examenes $getExamenes,
        Utilidades $utilidades
        )
    {
        $this->profesionales = $profesionales;
        $this->reporteExcel = $reporteExcel;
        $this->getExamenes = $getExamenes;
        $this->utilidades = $utilidades;
    }

    public function efector(Request $request) 
    { 
        $user = Auth::user()->load('personal');

        $efectores = null;

        if($this->utilidades->checkTipoRol($user->name, SELF::ADMIN)) {

            $efectores = $this->profesionales->listado('Efector');
            event(new LstProfesionalesEvent($efectores));

        }else if($this->utilidades->checkTipoRol($user->name, [SELF::TIPOS[0]])) {

            $efectores = collect([
                (object)[
                    'Id' => $user->profesional_id,
                    'NombreCompleto' => $user->personal->nombre_completo,
                ]
            ]);
        }

        return view('layouts.llamador.efector', compact(['efectores']));
    }

    public function informador()
    {
        $user = Auth::user()->load('personal');

        $informadores = null;

        if($this->utilidades->checkTipoRol($user->name, SELF::ADMIN)) {
            
            $informadores = $this->profesionales->listado('Informador');
            event(new LstProfInformadorEvent($informadores));

        } else if($this->utilidades->checkTipoRol($user->name, [SELF::TIPOS[1]])) {
  
            $informadores = collect([
                (object)[
                    'Id' => $user->profesional_id,
                    'NombreCompleto' => $user->personal->nombre_completo,
                ]
            ]);
        }

        return view('layouts.llamador.informador', compact(['informadores']));
    }

    public function combinado()
    {
        $user = Auth::user()->load('personal');

        $combinados = null;

        if($this->utilidades->checkTipoRol($user->name, SELF::ADMIN)) {
            
            $combinados = $this->profesionales->listado('Combinado');
            event(new LstProfCombinadoEvent($combinados));

        } else if($this->utilidades->checkTipoRol($user->name, [SELF::TIPOS[2]])) {
  
            $combinados = collect([
                (object)[
                    'Id' => $user->profesional_id,
                    'NombreCompleto' => $user->personal->nombre_completo,
                ]
            ]);
        }

        return view('layouts.llamador.combinado', compact(['combinados']));
    }

    public function buscar(Request $request)
    {
        if ($request->ajax()) {

            $query = $this->queryBasico($request->especialidad);
            // $especialidades = ProfesionalProv::where('IdRol',SELF::TIPOS[0])->where('IdProf', $request->profesional)->pluck('IdProv')->toArray();

            if (!empty($request->prestacion)){
                
                $query->where('prestaciones.Id', $request->prestacion);
            
            } else {

                $query->when(!empty($request->profesional), function ($query) use ($request){
                    // $query->whereIn('itemsprestaciones.IdProfesional', [$request->profesional, 0])
                        // ->where('itemsprestaciones.IdProfesional', '!=', $request->profesional)
                        $query->where('itemsprestaciones.IdProfesional2', 0)
                        ->addSelect(DB::raw('"' . $request->especialidad . '" as especialidades'));
                });
    
                $query->when(!empty($request->fechaDesde) || !empty($request->fechaHasta), function ($query) use ($request){
                    $query->whereBetween('prestaciones.Fecha', [$request->fechaDesde, $request->fechaHasta]);
                });
    
                $query->when(!empty($request->estado) && ($request->estado === 'abierto'), function($query) {
                    $query->whereIn('itemsprestaciones.CAdj', [0, 1, 2])
                        ->where('prestaciones.Cerrado', 0);
                });
    
                $query->when(!empty($request->estado) && ($request->estado === 'cerrado'), function($query) {
                    $query->whereIn('itemsprestaciones.CAdj', [3, 4, 5])
                        ->where('prestaciones.Cerrado', 1);
                });
    
                $query->when(!empty($request->estado) && ($request->estado === 'todos'), function($query){
                    $query->whereIn('itemsprestaciones.CAdj', [0, 1, 2, 3, 4, 5]);
                });
            }

            $query->where('itemsprestaciones.IdProveedor', $request->especialidad)
                  ->groupBy('prestaciones.Id')
                  ->orderBy('prestaciones.Id', 'DESC')
                  ->orderBy('pacientes.Apellido', 'DESC');

            return Datatables::of($query)->make(true);
        }
    }

    public function buscarInf(Request $request)
    {
        if ($request->ajax()) {
        
            $query = $this->queryBasico($request->profesional);

            if (!empty($request->prestacion)){
                    
                $query->where('prestaciones.Id', $request->prestacion);
            
            } else {

                $query->when(!empty($request->profesional), function ($query) use ($request){
                    $query->where('itemsprestaciones.IdProfesional', '!=', 0)
                        ->whereIn('itemsprestaciones.IdProfesional2', [$request->profesional, 0])
                        ->where('itemsprestaciones.IdProfesional2', '!=', $request->profesional)
                        ->where('itemsprestaciones.IdProveedor', $request->especialidad)
                        ->addSelect(DB::raw('"' . $request->especialidad . '" as especialidades'));
                });
    
                $query->when(!empty($request->fechaDesde) || !empty($request->fechaHasta), function ($query) use ($request){
                    $query->whereBetween('prestaciones.Fecha', [$request->fechaDesde, $request->fechaHasta]);
                });
    
                $query->when(!empty($request->estado) && ($request->estado === 'abierto'), function($query) {
                        $query->where('itemsprestaciones.CInfo', 1)
                            ->whereIn('itemsprestaciones.CAdj', [3,5]);
                });
    
                $query->when(!empty($request->estado) && ($request->estado === 'cerrado'), function($query) {
                    $query->whereIn('itemsprestaciones.CAdj', [3, 5])
                        ->where('prestaciones.CInfo', 2);
                });
    
                $query->when(!empty($request->estado) && ($request->estado === 'todos'), function($query){
                    $query->whereIn('itemsprestaciones.CAdj', [3, 5])
                        ->whereIn('itemsprestaciones.CInfo', [1,2]);
                });
            }

            $query->groupBy('prestaciones.Id')
                  ->orderBy('prestaciones.Id', 'DESC')
                  ->orderBy('pacientes.Apellido', 'DESC');

            return Datatables::of($query)->make(true);

            }
    }

    public function exportar(Request $request)
    {
        if($request->modo === 'basico') {
            $prestaciones = $this->queryBasico()->whereIn('prestaciones.Id', $request->Ids)->groupBy('prestaciones.Id')->get();

            if($prestaciones) {
                $reporte = $this->reporteExcel->crear('llamadorExportar');
                return $reporte->generar($prestaciones);
            }else{
                return response()->json(['msg' => 'No existen prestaciones para exportar'], 409);
            }

        }elseif ($request->modo === 'full') {
            $prestaciones = $this->queryFull()->whereIn('prestaciones.Id', $request->Ids)->groupBy('itemsprestaciones.Id')->get();

            if($prestaciones) {
                $reporte = $this->reporteExcel->crear('llamadorDetalle');
                return $reporte->generar($prestaciones);
            }else{
                return response()->json(['msg' => 'No existen prestaciones para exportar'], 409);
            }
        }
    }

    public function verPaciente(Request $request)
    {
        if(empty($request->Especialidades)) {
            return response()->json(['msg' => 'Verifique la especialidad. No se ha encontrado'], 404);
        }

        $especialidades = explode(',', $request->Especialidades);

        $prestacion = Prestacion::with(['paciente','empresa','art'])->where('Id', $request->Id)->first();
        $itemsprestaciones = $this->getExamenes->getAllItemsprestaciones($request->Id, $especialidades);

        if (is_numeric($request->IdProfesional) && $request->IdProfesional !== 'undefined') {
            $datos = User::with('personal')->where('profesional_id', $request->IdProfesional)->first();
        } 

        if($prestacion) {
            return response()->json([
                'prestacion' => $prestacion, 
                'profesional' => $datos->personal->nombre_completo ?? '',
                'itemsprestaciones' => $itemsprestaciones,
            ]);
        }
    }

    public function controlLlamado(Request $request)
    {   
        if(empty($request->prestacion) || empty($request->profesional) || empty($request->especialidad)) {
            return response()->json(['msg' => 'Faltan datos para poder activar la opción'], 404);
        }

        $query = Llamador::with(['prestacion', 'prestacion.paciente'])
            ->where('prestacion_id', $request->prestacion)
            ->where('profesional_id', $request->profesional)
            ->where('especialidad_id', $request->especialidad)
            ->first();
            
        $data = [];

        if ($query) {

            $listaExamenes = ItemPrestacion::where('IdPrestacion', $request->prestacion)
                ->where('IdProveedor', $request->especialidad)
                ->pluck('CAdj')
                ->toArray();

            if(in_array(2, $listaExamenes)) {
                return response()->json(['msg' => 'No se ha liberado la prestacion porque hay examenes con adjunto pero abiertos'], 409);
            }

            $query->delete();
            
            $data = [
                'status' => 'liberado', 
                'msg' => "Se ha liberado la prestación {$query->Id } del paciente {$query->prestacion->paciente->nombre_completo} ",
                'prestacion' => $request->prestacion
            ];
        
        }else{

            Llamador::create([
                'Id' => Llamador::max('Id') + 1,
                'profesional_id' => $request->profesional,
	            'prestacion_id' =>  $request->prestacion,
                'especialidad_id' => $request->especialidad,
                'tipo_profesional' => session('Profesional') ?? $request->Tipo
            ]);

            $data = [
                'status' => 'llamado', 
                'prestacion' => $request->prestacion
            ];
        }

        event(new GrillaEfectoresEvent($data));   
    }

    public function checkLlamado(Request $request)
    {
        $profesional = (empty(session('Profesional')) ? $request->tipo : session('Profesional'));

        $query = Llamador::where('prestacion_id', $request->id)
            ->where('tipo_profesional', $profesional)
            ->first();
        
        return response()->json($query);
    }

    public function asignarProfesional(Request $request)
    {
        if($request->Id === 'on') return;

        $query = ItemPrestacion::with(['examenes'])->where('Id', $request->Id)->first();

        if (empty($query)) {
            return response()->json(['msg' => 'No se pudo procesar la informacion.'], 404);
        }

        if($query->examenes?->Adjunto === 1 && $this->adjunto($request->Id, 'Efector') && $query->IdProfesional !== 0) {
            return response()->json(['msg' => 'No se puede desasignar al profesional porque hay un archivo adjunto en el examen', 'noCheck' => true], 409);
        }

        if(in_array($query->CAdj, [3,5])) {
            return response()->json(['msg' => 'No se puede desasignar al profesional porque el examen se encuentra cerrado (estado)'], 409);
        }

        if($query)  {
            $query->IdProfesional = $request->estado == 'true' ? $request->Profesional : 0;
            $query->save();

            $msg = $request->estado == 'true'
                ? 'Se ha asignado el profesional al exámen'
                : 'Se ha desasignado el profesional al exámen';

            $profesional = $request->estado == 'true' ? $this->profesionales->getProfesional($request->Profesional) : null;

            $data = [
                'itemprestacion' => $request->Id,
                'profesional' => $profesional?->NombreCompleto
            ];

                event(new AsignarProfesionalEvent($data));

            return response()->json(['msg' => $msg], 200);
        }
    }

    public function listadoEspecialidades(Request $request)
    {
        return ProfesionalProv::join('proveedores', 'profesionales_prov.IdProv', '=', 'proveedores.Id')
            ->select(
                'proveedores.Nombre as Nombre',
                'proveedores.Id as Id'
            )
            ->whereNot('profesionales_prov.IdRol', '0')
            ->where('profesionales_prov.IdProf', $request->IdProfesional)
            ->where('profesionales_prov.IdRol', $request->Tipo)
            ->get();
    }

    public function cambioEstado(Request $request)
    {

        if(empty($request->Id)) {
            return response()->json(['msg' => 'No hay ID para procesar. Error interno'], 500);
        }
        
        $item = ItemPrestacion::with('examenes')->find($request->Id);

        if(!$item) {
            return response()->json(['msg' => 'No se encontro el examen'], 404);
        }

        if(!$item->IdProfesional) {
            return response()->json(['msg' => 'No tiene profesional efector asignado. Debe asignarse para cerrar'], 409);
        }

        if ($request->tipo === SELF::TIPOS[0]) {

            //Adjunto "Cero" no lleva adjuntos
            if ($item->examenes->Adjunto === 0 && $request->accion === 'cerrar') {

                //Tres es cerrado sin adjunto
                $item->update(['CAdj' => 3]);
                return response()->json(['msg' => 'Se ha cerrado el examen de manera correcto', 'CAdj' => 3, 'IdItem' => $request->Id], 200);
            
            } elseif($item->examenes->Adjunto === 1 && $request->accion === 'cerrar') {

                $archivo = ArchivoEfector::where('IdEntidad', $request->Id)->first();

                if(!$archivo) {

                    return response()->json(['msg' => 'No se puede cerrar el examen porque no se ha adjuntado el archivo'], 409);
                }

                //Cerrado es cerrado con adjunto
                $item->update(['CAdj' => 5]);
                return response()->json(['msg' => 'Se ha cerrado el examen de manera correcto', 'CAdj' => 5, 'IdItem' => $request->Id], 200);

            }       
        } 
    }

    public function cerrarAtencion(Request $request)
    {
        if(empty($request->Id)) {
            return response()->json(['msg' => 'No se puede terminar la atencion sin la ID'], 404);
        }

        $llamador = Llamador::where('prestacion_id', $request->Id)->where('tipo_profesional', $request->tipo)->first();

        if($llamador) {
            $llamador->delete();

            return response()->json(['msg', 'Se ha liberado la prestación correctamente'], 200);
        }
    }

    public function getItemprestacion(Request $request)
    {
        $item = ItemPrestacion::find($request->Id);

        return response()->json([
            'itemprestacion' => $item,
            'multiEfector' => $this->multiEfector($item->IdPrestacion, $item->IdProfesional, $item->examenes->IdProveedor),
            'proveedores' => $item->examenes->proveedor1
        ]);
    }

    public function cierreForzado(Request $request)
    {
        // if(!$this->hasPermission("llamador_show")) {
        //     return response()->json(['msg' => 'No tiene permisos'], 403);
        // }

        if(empty($request->profesional) || (empty($request->prestacion))) {
            return response()->json(['msg' => 'No se ha podido realizar la operacion porque la id no existe'], 404);
        }

        $query = Llamador::where('profesional_id', $request->profesional)->where('prestacion_id', $request->prestacion)->first();

        if($query) {
            $query->delete();
        }

        $data = [
                'status' => 'liberado', 
                'msg' => "Se ha liberado la prestación {$query->Id } del paciente {$query->prestacion->paciente->nombre_completo} ",
                'prestacion' => $request->prestacion
            ];

        event(new GrillaEfectoresEvent($data));

    }

    private function queryBasico()
    {
        return ItemPrestacion::join('prestaciones', 'itemsprestaciones.IdPrestacion', '=', 'prestaciones.Id')
        ->join('pacientes', 'prestaciones.IdPaciente', '=', 'pacientes.Id')
        ->join('clientes as empresa', 'prestaciones.IdEmpresa', '=', 'empresa.Id')
        ->join('clientes as art', 'prestaciones.IdART', '=', 'art.Id')
        ->leftJoin('telefonos', 'pacientes.Id', '=', 'telefonos.IdEntidad')
        ->join('examenes', 'itemsprestaciones.IdExamen', '=', 'examenes.Id')
        ->join('proveedores', 'examenes.IdProveedor', '=', 'proveedores.Id')
        ->select(
            DB::raw('DATE_FORMAT(prestaciones.Fecha, "%d/%m/%Y") as fecha'),
            'itemsprestaciones.IdProfesional2 as IdProfesional2',
            'prestaciones.Id as prestacion',
            'prestaciones.TipoPrestacion as tipo',
            'prestaciones.Cerrado as Cerrado',
            'empresa.RazonSocial as empresa',
            'empresa.ParaEmpresa as paraEmpresa',
            'art.RazonSocial as art',
            DB::raw("CONCAT(pacientes.Apellido,' ',pacientes.Nombre) as paciente"),
            'pacientes.Documento as dni',
            'pacientes.FechaNacimiento as fechaNacimiento',
            DB::raw("CONCAT(telefonos.CodigoArea,telefonos.NumeroTelefono) as telefono")
        );
             
       $query->where('itemsprestaciones.Anulado', 0);
    }

    private function queryFull()
    {
        return Prestacion::join('pacientes', 'prestaciones.IdPaciente', '=', 'pacientes.Id')
        ->join('clientes as empresa', 'prestaciones.IdEmpresa', '=', 'empresa.Id')
        ->join('clientes as art', 'prestaciones.IdART', '=', 'art.Id')
        ->leftJoin('telefonos', 'pacientes.Id', '=', 'telefonos.IdEntidad')
        ->join('itemsprestaciones', 'prestaciones.Id', '=', 'itemsprestaciones.IdPrestacion')
        ->leftJoin('examenes', 'itemsprestaciones.IdExamen', '=', 'examenes.Id')
        ->leftJoin('archivosefector', 'itemsprestaciones.Id', '=', 'archivosefector.IdEntidad')
        ->select(
            DB::raw('DATE_FORMAT(prestaciones.Fecha, "%d/%m/%Y") as fecha'),
            'itemsprestaciones.IdProfesional2 as IdProfesional2',
            'prestaciones.Id as prestacion',
            'prestaciones.TipoPrestacion as tipo',
            'prestaciones.Cerrado as Cerrado',
            'empresa.RazonSocial as empresa',
            'empresa.ParaEmpresa as paraEmpresa',
            'art.RazonSocial as art',
            DB::raw("CONCAT(pacientes.Apellido,' ',pacientes.Nombre) as paciente"),
            'pacientes.Documento as dni',
            DB::raw("CONCAT(telefonos.CodigoArea,telefonos.NumeroTelefono) as telefono"),
            'examenes.Nombre as nombreExamen',
            DB::raw('(
                CASE 
                    WHEN itemsprestaciones.CAdj = 5 OR itemsprestaciones.CAdj = 3 AND itemsprestaciones.CInfo = 3 OR itemsprestaciones.CInfo = 0 THEN "Completo" ELSE "Incompleto"
                END) AS estadoExamen
            '),
            'itemsprestaciones.Anulado as Anulado',
            DB::raw('(
                    CASE 
                        WHEN itemsprestaciones.CAdj in (1,2,4) THEN "Pdte"
                        WHEN itemsprestaciones.CAdj in (3,5) THEN "Cerr"
                        ELSE ""
                    END
                ) AS estadoEfector
                        '),
            DB::raw('(
                    CASE 
                        WHEN itemsprestaciones.CInfo = 1 THEN "Pdte"
                        WHEN itemsprestaciones.CInfo = 2 THEN "Borrador"
                        WHEN itemsprestaciones.CInfo = 3 THEN "Cerr"
                        ELSE ""
                    END
                    ) AS estadoInformador
                '),
            DB::raw('(CASE 
                WHEN EXISTS(SELECT 1 FROM archivosefector WHERE itemsprestaciones.Id = archivosefector.IdEntidad) THEN "Adj" 
                ELSE ""
            END) AS estadoAdj'),
            'itemsprestaciones.ObsExamen as obsExamen',
            'itemsprestaciones.CAdj',
            'itemsprestaciones.CInfo'
        )->whereNot('prestaciones.Fecha', null)
        ->whereNot('prestaciones.Fecha', '0000-00-00')
        ->where('prestaciones.Anulado', 0);
    }

}