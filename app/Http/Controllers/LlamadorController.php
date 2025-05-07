<?php

namespace App\Http\Controllers;

use App\Events\GrillaEfectoresEvent;
use App\Events\LstProfEfectoresEvent;
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


class LlamadorController extends Controller
{
    protected $listadoProfesionales;
    protected $reporteExcel;
    protected $getExamenes;
    protected $utilidades;

    const ADMIN = ['Administrador', 'Admin SR', 'Recepcion SR'];
    const TIPOS = ['Efector', 'Informador'];

    public function __construct(
        Profesionales $listadoProfesionales, 
        ReporteExcel $reporteExcel,
        Examenes $getExamenes,
        Utilidades $utilidades
        )
    {
        $this->listadoProfesionales = $listadoProfesionales;
        $this->reporteExcel = $reporteExcel;
        $this->getExamenes = $getExamenes;
        $this->utilidades = $utilidades;
    }

    public function efector(Request $request)
    { 
        $user = Auth::user()->load('personal');

        $efectores = null;

        if ($this->utilidades->checkTipoRol(Auth::user()->name, SELF::ADMIN)) {

            $efectores = $this->listadoProfesionales->listado('Efector');
            event(new LstProfEfectoresEvent($efectores));   

        }else if($this->utilidades->checkTipoRol(Auth::user()->name, [SELF::TIPOS[0]])) {

            $efectores = collect([
                (object)[
                    'Id' => Auth::user()->profesional_id,
                    'NombreCompleto' => $user->personal->nombre_completo,
                ]
            ]);
        }

        return view('layouts.llamador.efector', compact(['efectores']));
    }

    public function informador()
    {
        return view('layouts.llamador.informador');
    }

    public function evaluador()
    {
        return view('layouts.llamador.evaluador');
    }

    public function buscarEfector(Request $request)
    {
        if ($request->ajax()) {

            $query = $this->queryBasico($request->profesional);
            $especialidades = ProfesionalProv::where('IdRol',SELF::TIPOS[0])->where('IdProf', $request->profesional)->pluck('IdProv')->toArray();

            if (!empty($request->prestacion)){
                
                $query->where('prestaciones.Id', $request->prestacion);
            
            } else {

                $query->when(!empty($request->profesional), function ($query) use ($request, $especialidades){
                    $query->whereIn('itemsprestaciones.IdProfesional', [$request->profesional, 0])
                        ->whereIn('examenes.IdProveedor', $especialidades)
                        ->addSelect(DB::raw('"' . implode(',', $especialidades) . '" as especialidades'));
                });
    
                $query->when(!empty($request->fechaDesde) || !empty($request->fechaHasta), function ($query) use ($request){
                    $query->whereBetween('prestaciones.Fecha', [$request->fechaDesde, $request->fechaHasta]);
                });
    
                $query->when(!empty($request->estado) && ($request->estado === 'abierto'), function($query) {
                    $query->whereIn('itemsprestaciones.CAdj', [0, 1, 2]);
                });
    
                $query->when(!empty($request->estado) && ($request->estado === 'cerrado'), function($query) {
                    $query->whereIn('itemsprestaciones.CAdj', [3, 4, 5]);
                });
    
                $query->when(!empty($request->estado) && ($request->estado === 'todos'), function($query){
                    $query->whereIn('itemsprestaciones.CAdj', [0, 1, 2, 3, 4, 5]);
                });
            }

            $query->groupBy('prestaciones.Id')
                  ->orderBy('prestaciones.Id', 'DESC')
                  ->orderBy('pacientes.Apellido', 'DESC');

            return Datatables::of($query)->make(true);
        }
        
        return view('layouts.llamador.efector');
    }

    public function imprimirExcel(Request $request)
    {
        if($request->tipo === 'efector' && $request->modo === 'basico') {
            $prestaciones = $this->queryBasico()->whereIn('prestaciones.Id', $request->Ids)->groupBy('prestaciones.Id')->get();

            if($prestaciones) {
                $reporte = $this->reporteExcel->crear('efectorExportar');
                return $reporte->generar($prestaciones);
            }else{
                return response()->json(['msg' => 'No existen prestaciones para exportar'], 409);
            }

        }elseif ($request->tipo === 'efector' && $request->modo === 'full') {
            $prestaciones = $this->queryFull()->whereIn('prestaciones.Id', $request->Ids)->groupBy('itemsprestaciones.Id')->get();

            if($prestaciones) {
                $reporte = $this->reporteExcel->crear('efectorDetalle');
                return $reporte->generar($prestaciones);
            }else{
                return response()->json(['msg' => 'No existen prestaciones para exportar'], 409);
            }
        }
    }

    public function verPaciente(Request $request)
    {
        $especialidades = explode(',', $request->Especialidades);

        $prestacion = Prestacion::with(['paciente','empresa','art'])->where('Id', $request->Id)->first();
        $itemsprestaciones = $this->getExamenes->getAllItemsprestaciones($request->Id, $especialidades);

        if (is_numeric($request->IdProfesional) && $request->IdProfesional == 'undefined') {
            $datos = User::with('personal')->where('profesional_id', $request->IdProfesional)->first();
        } 

        if($prestacion) {
            return response()->json([
                'prestacion' => $prestacion, 
                'profesional' => $datos->personal->nombre_completo,
                'itemsprestaciones' => $itemsprestaciones,
            ]);
        }
    }

    public function controlLlamado(Request $request)
    {   
        $query = Llamador::with(['prestacion', 'prestacion.paciente'])->where('prestacion_id', $request->prestacion)->first();

        $data = [];

        if ($query) {
            $query->delete();
            
            $data = [
                'status' => 'liberado', 
                'msg' => "Se ha liberado la prestación {$query->Id } del paciente {$query->prestacion->paciente->nombre_completo} "
            ];
        
        }else{

            Llamador::create([
                'Id' => Llamador::max('Id') + 1,
                'profesional_id' => $request->profesional,
	            'prestacion_id' =>  $request->prestacion,
                'itemprestacion_id' => 0
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
        $query = Llamador::where('prestacion_id', $request->id)->exists();
        return response()->json($query);
    }

    private function queryBasico(?int $idProfesional = null)
    {
        return Prestacion::join('pacientes', 'prestaciones.IdPaciente', '=', 'pacientes.Id')
        ->join('clientes as empresa', 'prestaciones.IdEmpresa', '=', 'empresa.Id')
        ->join('clientes as art', 'prestaciones.IdART', '=', 'art.Id')
        ->leftJoin('telefonos', 'pacientes.Id', '=', 'telefonos.IdEntidad')
        ->join('itemsprestaciones', 'prestaciones.Id', '=', 'itemsprestaciones.IdPrestacion')
        ->join('examenes', 'itemsprestaciones.IdExamen', '=', 'examenes.Id')
        ->join('proveedores', 'examenes.IdProveedor', '=', 'proveedores.Id')
        ->select(
            DB::raw('DATE_FORMAT(prestaciones.Fecha, "%d/%m/%Y") as fecha'),
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
        
        if ($idProfesional !== null) {
            $query->addSelect(DB::raw($idProfesional . ' as idProfesional'));
        }

        $query->addSelect(DB::raw("
            CASE 
                WHEN EXISTS (
                    SELECT 1 
                    FROM llamador
                    WHERE llamador.prestacion_id = prestaciones.Id
                ) THEN 'true'
                ELSE 'false'
            END as estado_llamado
        "));
            
       $query->whereNotNull('prestaciones.Fecha')
        ->where('prestaciones.Fecha', '<>', '0000-00-00')
        ->where('prestaciones.Anulado', 0);
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