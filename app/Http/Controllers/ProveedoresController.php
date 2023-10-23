<?php

namespace App\Http\Controllers;

use App\Models\Localidad;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use App\Models\Proveedor;
use App\Models\Provincia;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProveedoresController extends Controller
{

    public function index()
    {
        return view('layouts.especialidades.index');
    }

    public function edit(Proveedor $especialidade)
    {
        $localidad = Localidad::where('IdPcia', $especialidade->IdLocalidad)->first();
        $detalleProv = Provincia::find($localidad->Id);
        $provincias = Provincia::all();

        return view('layouts.especialidades.edit', compact(['especialidade', 'detalleProv', 'provincias']));
    }

    public function create()
    {
        return view('layouts.especialidades.create', with([
            'provincias' => Provincia::all(),
            'localidades' => Localidad::where('IdPcia', 1)->get(['Id', 'Nombre'])
        ]));
    }

    public function getProveedores(Request $request)
    {
        $buscar = $request->buscar;

        $resultados = Cache::remember('proveedores' . $buscar, 5, function() use ($buscar){

            $proveedores = Proveedor::where('Nombre', 'LIKE', '%'. $buscar . '%')
            ->where('Inactivo', 0)
            ->where('Id', '<>', 0)
            ->get();

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

    public function down(Request $request): void
    {
        $especialidad = Proveedor::find($request->Id);

        if($especialidad){

            $especialidad->Inactivo = 1;
            $especialidad->save();

        } 
    }

    public function multiDown(Request $request): void
    {
        $ids = $request->input('ids');
        if (! is_array($ids)) {
            $ids = [$ids];
        }

        Proveedor::whereIn('id', $ids)->update(['Inactivo' => 1]);

    }

    public function check(Request $request)
    {
        $especialidad = Proveedor::where('Nombre', $request->Nombre)->first();
        $existe = $especialidad !== null;

        return response()->json(['existe' => $existe, 'especialidades' => $especialidad]);
    }

    public function save(Request $request)
    {
        $Id = Proveedor::max('Id') + 1;

        Proveedor::create([
            'Id' => $Id,
            'Nombre' => $request->Nombre,
            'Telefono' => $request->Telefono ?? '',
            'Direccion' => $request->Direccion ?? '',
            'IdLocalidad' => $request->IdLocalidad,
            'Inactivo' => $request->Inactivo,
            'Externo' => $request->Externo
        ]);

        return response()->json(['especialidad' => $Id]);
        
    }

    public function update(Request $request)
    {
        $especialidad = Proveedor::find($request->Id);

        if($especialidad)
        {
            $especialidad->Nombre = $request->Nombre;
            $especialidad->Telefono = $request->Telefono ?? '';
            $especialidad->Direccion = $request->Direccion ?? '';
            $especialidad->IdLocalidad = $request->IdLocalidad ?? '';
            $especialidad->Inactivo = $request->Inactivo ?? '';
            $especialidad->Min = $request->Min ?? '';
            $especialidad->Multi = $request->Multi ?? '';
            $especialidad->MultiE = $request->MultiE ?? '';
            $especialidad->Externo = $request->Externo ?? '';
            $especialidad->InfAdj = $request->InfAdj ?? '';
        }
    }

    public function excel(Request $request): string
    {
        $ids = $request->input('Id');
        if (! is_array($ids)) {
            $ids = [$ids];
        }

        $especialidades = DB::table('proveedores')
            ->where('Inactivo', 0)
            ->whereIn('Id', $ids)
            ->select(
                'Id as IdEspecialidad',
                'Nombre',
                'Telefono',
                'Multi as Adjunto',
                'MultiE as Examen',
                'InfAdj as Informe',
                'Externo as Ubicacion'
                )
            ->get();

        $excel = "Id,Proveedor,Ubicación,Teléfono,Adjunto,Examen, Informe\n";
        foreach ($especialidades as $row) {
            $IdEspecialidad = $row->IdEspecialidad ?? '-';
            $Nombre = $row->Nombre ?? '-';
            $Telefono = $row->Telefono ?? '-';
            $Adjunto = ($row->Adjunto === 0 ? 'Simple' : ($row->Adjunto === 1 ? 'Multiple' : '-'));
            $Examen = ($row->Examen === 0 ? 'Interno' : ($row->Examen === 1 ? 'Externo' : '-'));
            $Informe = ($row->Informe === 0 ? 'Simple' : ($row->Informe === 1 ? 'Multiple' : '-'));
            $Ubicacion = ($row->Ubicacion === 0 ? 'Interno':($row->Ubicacion === 1 ? 'Externo' : '-'));

            $excel .= "$IdEspecialidad,$Nombre,$Ubicacion,$Telefono,$Adjunto,$Examen,$Informe\n";
        }

        // Generar un nombre aleatorio para el archivo
        $name = Str::random(10).'.xlsx';

        // Guardar el archivo en la carpeta de almacenamiento
        $filePath = storage_path('app/public/'.$name);
        file_put_contents($filePath, $excel);
        chmod($filePath, 0777);

        // Devolver la ruta del archivo generado
        return response()->json(['filePath' => $filePath]);
    }

    public function search(Request $request)
    {

        $especialidad = $request->especialidad;
        $opciones = $request->opciones ?? '0';
        
        if($request->ajax())
        {
            $query = Proveedor::where('Nombre', 'LIKE', '%' . $especialidad . '%')
            ->where('Inactivo', 0)
            ->select(
            'Id as IdEspecialidad',
            'Nombre',
            'Telefono',
            'Multi as Adjunto',
            'MultiE as Examen',
            'InfAdj as Informe',
            'Externo as Ubicacion'
            );
            if ($opciones !== '0') {
                $query->where($opciones, 1);
            }
        
            $result = $query->get();
            return Datatables::of($result)->make(true);
        }

        return view('layouts.especialidades.index');

    }


    
}