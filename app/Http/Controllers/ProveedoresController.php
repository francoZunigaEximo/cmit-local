<?php

namespace App\Http\Controllers;

use App\Models\Localidad;
use App\Models\Proveedor;
use App\Models\Provincia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;
use App\Services\ReportesExcel\ReporteExcel;
use App\Traits\CheckPermission;
use Illuminate\Http\JsonResponse;

class ProveedoresController extends Controller
{

    use CheckPermission;

    protected $reporteExcel;

    public function __construct(ReporteExcel $reporteExcel)
    {
        $this->reporteExcel = $reporteExcel;
    }

    public function index(Request $request)
    {
        if (!$this->hasPermission('especialidades_show')) {
            abort(403);
        }

        if ($request->ajax()) {

            $query = Proveedor::select(
                'Id as IdEspecialidad',
                'Nombre',
                'Telefono',
                'Direccion',
                DB::raw('(SELECT Nombre FROM localidades WHERE IdLocalidad = localidades.Id) AS NombreLocalidad'),
                'Multi as Adjunto',
                'MultiE as Examen',
                'InfAdj as Informe',
                'Externo as Ubicacion'
            );

            $query->when(!empty($request->especialidad), function($query) use ($request){
                $query->where('Nombre', 'LIKE', '%' . $request->especialidad . '%');
            });
            
            $query->when($request->opciones === 'Interno', function($query){
                $query->where('Externo', 0);
            });

            $query->when($request->opciones === 'Externo', function($query){
                $query->where('Externo', 1);
            });

            $query->when($request->opciones === 'Todo', function($query){
                $query->whereIn('Externo', [0,1]);
            });

            $query->when($request->opciones === 'Multi', function($query){
                $query->where('Multi', 1);
            });

            $query->when($request->opciones === 'MultiE', function($query){
                $query->where('MultiE', 1);
            });
        
            $result = $query->where('Inactivo', 0)->get();
            return Datatables::of($result)->make(true);
        }

        return view('layouts.especialidades.index');

    }

    public function edit(Proveedor $especialidade)
    {
        if(!$this->hasPermission('especialidades_edit')) {
            abort(403);
        }

        $detalleProv = Localidad::with('provincia')->find($especialidade->IdLocalidad);
        $provincias = Provincia::all();

        return view('layouts.especialidades.edit', compact(['especialidade', 'detalleProv', 'provincias']));
    }

    public function create()
    {
        if(!$this->hasPermission('especialidades_add')) {
            abort(403);
        }

        return view('layouts.especialidades.create', with([
            'provincias' => Provincia::all(),
            'localidades' => Localidad::where('IdPcia', 1)->get(['Id', 'Nombre'])
        ]));
    }

    public function getProveedores(Request $request)
    {

        $buscar = $request->buscar;

        $resultados = Cache::remember('proveedores' . $buscar, 5, function() use ($buscar){

            $proveedores = $this->buscar($buscar);

            $resultados = [];

            foreach($proveedores as $proveedor){
                $resultados[] = [
                    'id' => $proveedor->Id,
                    'text' => $proveedor->Nombre
                ];
            }
            return $resultados;
        });

        return response()->json(['proveedores' => $resultados]);
    }

    public function baja(Request $request): mixed
    {
        if(!$this->hasPermission('especialidades_delete')) {
            return response()->json(['msg' => 'No tiene permisos'], 403);
        }

        if(empty($request->input('ids'))) {
             return response()->json(['msg' => 'No se ha podido realizar la acción.'], 409);
        }

        $ids = (array) $request->input('ids');
        Proveedor::whereIn('Id', $ids)->update(['Inactivo' => 1]);
        
        return response()->json(['msg' => 'Se ha dado de baja correctamente'], 200);
        
    }

    public function check(Request $request)
    {
        if(!$this->hasPermission('especialidades_add')) {
            abort(403);
        }

        $especialidad = Proveedor::where('Nombre', $request->Nombre)->first();

        return response()->json($especialidad);
    }

    public function save(Request $request)
    {
        if(!$this->hasPermission('especialidades_add')) {
            abort(403);
        }

        $Id = Proveedor::max('Id') + 1;
        $data = $request->all();
        $data['Id'] = $Id;

        $query = Proveedor::create($data);

        if(empty($query)) {
            return response()->json(['msg' => 'No se ha podido registrar'], 500);
        }

        return response()->json(['msg' => 'Se ha registrado la nueva especialidad de manera correcta', 'especialidad' => $Id], 200);
    }

    public function updateProveedor(Request $request)
    {
        if(!$this->hasPermission('especialidades_add')) {
            abort(403);
        }

        $especialidad = Proveedor::find($request->Id);
        
        if(empty($especialidad))
        {
            return response()->json(['msg' => 'No se ha podido actualizar'], 404);
        }

        $especialidad->Nombre = $request->Nombre;
        $especialidad->Telefono = $request->Telefono ?? '';
        $especialidad->Direccion = $request->Direccion ?? '';
        $especialidad->IdLocalidad = $request->IdLocalidad ?? '';
        $especialidad->Inactivo = $request->Inactivo ?? '';
        $especialidad->Min = $request->Min ?? '';
        $especialidad->Multi = $request->Multi === 'true' ? 1 : 0;
        $especialidad->MultiE = $request->MultiE === 'true' ? 1 : 0;
        $especialidad->Externo = $request->Externo ?? '';
        $especialidad->InfAdj = $request->InfAdj ?? '';
        $especialidad->PR = $request->PR ?? '';
        $especialidad->Obs = $request->Obs ?? '';
        $especialidad->save();

        return response()->json(['msg' => 'Se han cargado los datos de manera correcta'], 200);
    }

    public function excel(Request $request)
    {
        if(!$this->hasPermission('especialidades_show')) {
            return response()->json(['msg' => 'No tiene permisos'], 403);
        }

        $ids = (array) $request->input('Id');

        $especialidades = $this->getListadoProveedores($ids);

        if(empty($especialidades)) {
             return response()->json(['msg' => 'No se ha podido generar el archivo'], 404);
        }

        $reporte = $this->reporteExcel->crear('especialidades');
        return $reporte->generar($especialidades);

    }

    public function lstProveedores(Request $request)
    {
        $listado = Proveedor::whereNot('Id', 0)
        ->orderBy('Nombre', 'ASC')    
        ->get(['Id', 'Nombre']);

        return response()->json($listado);
    }

    private function getListadoProveedores(array $ids)
    {
        return Proveedor::select(
                'Id as IdEspecialidad',
                'Nombre',
                'Telefono',
                'Multi as Adjunto',
                'MultiE as Examen',
                'InfAdj as Informe',
                'Externo as Ubicacion')
            ->where('Inactivo', 0)
            ->whereIn('Id', $ids)
            ->get();
    }

    
    private function buscar(string $buscar)
    {
        return Proveedor::where('Nombre', 'LIKE', '%'. $buscar . '%')
                ->where('Inactivo', 0)
                ->where('Id', '<>', 0)
                ->get();
    }
    
}