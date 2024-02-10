<?php

namespace App\Http\Controllers;

use App\Models\Profesional;
use App\Models\ProfesionalProv;
use App\Models\Proveedor;
use App\Models\Provincia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use App\Traits\ObserverProfesionales;
use App\Traits\ObserverPacientes;

use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Storage;

class ProfesionalesController extends Controller
{
    use ObserverProfesionales, ObserverPacientes;

    public function index()
    {
        return view('layouts.profesionales.index');
    }

    public function search(Request $request): mixed
    {

        $tipo = $request->tipo;
        $opciones = $request->opciones;
        $especialidad = intval($request->especialidad);
        $buscar = $request->buscar;

        $arrOpciones = [
            "pago0" => ["Pago", 0],
            "pago1" => ["Pago", 1],
            "inactivo0" => ["Inactivo", 0],
            "inactivo1" => ["Inactivo", 1],
            "inactivo2" => ["Inactivo", 2]     
        ];
        
        if($request->ajax())
        {
            $query = Profesional::select(
                'profesionales.Id as IdProfesional',
                'profesionales.Apellido as Apellido',
                'profesionales.Nombre as Nombre',
                'profesionales.Documento as Documento',
                DB::raw('(SELECT Nombre FROM proveedores WHERE Id = profesionales.IdProveedor) AS Proveedor'),
                'profesionales.TMP as TMP',
                'profesionales.T1 as Efector',
                'profesionales.T2 as Informador',
                'profesionales.T3 as Evaluador',
                'profesionales.T4 as Combinado',
                'profesionales.TLP as Login',
                'profesionales.Pago as Pago',
                'profesionales.Inactivo as Estado'
            )
            ->join('proveedores', 'profesionales.IdProveedor', '=', 'proveedores.Id')
            ->where('profesionales.Id', '<>', 0)
            ->orderBy('profesionales.Apellido', 'Asc');

            $query->when(is_array($tipo), function ($query) use ($tipo) {
                foreach ($tipo as $valor) {
                    $campo = strtoupper($valor);
                    $query->when(in_array($valor, ['t1', 't2', 't3', 't4']), function ($query) use ($campo) {
                        $query->where($campo, '=', '1');
                        $query->where('profesionales.Inactivo', 0);
                    });
                }
            });

            $query->when($buscar, function ($query) use ($buscar) {
                $query->where('profesionales.Id', '<>', 0)
                    ->where('profesionales.Inactivo', 0);
                    $query->where('profesionales.Apellido', 'LIKE', '%'.$buscar.'%')
                        ->orWhere('profesionales.Nombre', 'LIKE', '%'.$buscar.'%')
                        ->orWhereRaw("CONCAT(profesionales.Apellido, ' ', profesionales.Nombre) LIKE ?", ['%'.$buscar.'%'])
                        ->orWhere(function ($query) use ($buscar) {
                            $query->where('profesionales.Documento', '=', $buscar)
                                ->orWhere('profesionales.Documento', 'LIKE', '%'.$buscar.'%');
                        });
            });

            $query->when($especialidad, function ($query) use ($especialidad) {
                $query->where('profesionales.IdProveedor', $especialidad)
                    ->where('profesionales.Inactivo', 0);
            });

            $query->when(is_array($opciones), function ($query) use ($opciones, $arrOpciones) { 
                foreach ($opciones as $valor){
                    $data = $arrOpciones[$valor];
                    if ($arrOpciones[$valor] !== 'inactivo1' && $arrOpciones[$valor] !== 'inactivo2') {

                        $query->where('profesionales.'.$data[0], $data[1]);
                    
                    }elseif($arrOpciones[$valor] === 'inactivo1'){
                        $query->where('profesionales.Inactivo', 1);
                    
                    }elseif($arrOpciones[$valor] === 'inactivo2'){
                        $query->where('profesionales.Inactivo', 2);
                    
                    }
                    
                }
            });

            return Datatables::of($query)->make(true);
        }
        return view('layouts.profesionales.index');
    }

    public function create()
    {
        return view('layouts.profesionales.create', with([
            'provincias' => Provincia::all()
        ]));
    }

    public function getEvaluador(Request $request): mixed
    {
        $buscar = $request->buscar;

        $resultados = Cache::remember('Profesionales_'.$buscar, 5, function () use ($buscar) {

            $profesionales = Profesional::where(function ($query) use ($buscar) {
                $query->where('Apellido', 'LIKE', '%'.$buscar.'%')
                    ->orWhere('Nombre', 'LIKE', '%'.$buscar.'%');
            })
                ->where('T3', 1)
                ->get();

            $resultados = [];

            foreach ($profesionales as $evaluador) {
                $resultados[] = [
                    'id' => $evaluador->Id,
                    'text' => $evaluador->Apellido.' - '.$evaluador->Nombre,
                ];
            }

            return $resultados;

        });

        return response()->json(['evaluadores' => $resultados]);
    }

    public function estado(Request $request)
    {

        $Id = $request->Id;

        if(is_array($Id)){

            switch ($request->tipo) {
                
                case 'multipleBProf':
                    
                    Profesional::whereIn('Id', $Id)->update(['Inactivo' => 1]);
                    break;
                
                case 'multipleDProf':

                    Profesional::whereIn('Id', $Id)->update(['Inactivo' => 2]);
                    break;
            }
        }else{

                $profesional = Profesional::find($Id);

                if($request->tipo === 'bloquear'){

                    $profesional->Inactivo = 1;
                
                }elseif($request->tipo === 'eliminar'){

                    $profesional->Inactivo = 2;
                
                }

                $profesional->save();
        }
    }
        
    public function edit(Profesional $profesionale): mixed
    {

        return view('layouts.profesionales.edit', with([
            'profesionale' => $profesionale, 
            'telefono' => $this->getTelefono($profesionale->Id), 
            'provincias' => Provincia::OrderBy('Nombre', 'ASC')->get(),
            'listEspecialistas' => Proveedor::where('Inactivo', 0)->whereNot('Id', 0)->OrderBy('Nombre', 'ASC')->get(['Id', 'Nombre']),
            'detailsLocalidad' => $this->getLocalidad($profesionale->IdLocalidad)
        ]));
    }

    public function setPerfil(Request $request)
    {

        $query = Profesional::where('Id', $request->Id)->first();
        if($query->IdProveedor === null || $query->IdProveedor === 0){

            $query->IdProveedor = $request->especialidad;
            $query->save();
        }

        $consulta = ProfesionalProv::where('IdProf', $request->Id)
            ->where('IdProv', $request->especialidad)
            ->where('Tipo', $request->perfil)
            ->first();

        if(!$consulta){

            ProfesionalProv::create([
                'Id' => ProfesionalProv::max('Id') + 1,
                'IdProf' => $request->Id,
                'IdProv' => $request->especialidad,
                'Tipo' => $request->perfil
            ]);
        }else{

            return response()->json(['error' => 'Ya existe un perfil con esa especialidad']);
        }
    }

    public function getPerfil(Request $request)
    {

        $query = ProfesionalProv::join('profesionales', 'profesionales_prov.IdProf', '=', 'profesionales.Id')
            ->join('proveedores', 'profesionales_prov.IdProv', '=', 'proveedores.Id')
            ->select(
                'profesionales_prov.Id as Id',
                'profesionales_prov.IdProf as IdProf',
                'profesionales_prov.IdProv as IdProv',
                'proveedores.Nombre as especialidad',
                DB::raw('GROUP_CONCAT(profesionales_prov.Tipo) as Tipos')
            )
            ->where('IdProf', $request->Id)
            ->groupBy('IdProf', 'IdProv')
            ->get();

        return response()->json(['data' => $query]);

    }

    public function delPerfil(Request $request)
    {
        $perfiles = ProfesionalProv::where('IdProv', $request->IdProv)->where('IdProf', $request->IdProf)->get();

        foreach ($perfiles as $perfil) {
            $perfil->delete();
        }
    }

    public function store(Request $request): string
    {
        $nuevoId = Profesional::max('Id') + 1;

        if($request->hasFile('Foto')) {
            $fileName = 'PROF'.$nuevoId. '.' . $request->Foto->extension();
            $request->Foto->storeAs('public/profesionales', $fileName);
        }

        Profesional::create([
            'Id' => $nuevoId,
            'Documento' => $request->Documento,
            'Apellido' => $request->Apellido,
            'Nombre' => $request->Nombre,
            'EMail' => $request->EMail,
            'Direccion' => $request->Direccion ?? '',
            'Provincia' => $request->Provincia,
            'IdLocalidad' => $request->IdLocalidad ?? '',
            'Firma' => $request->Firma ?? '',
            'CP' => $request->CP ?? '0',
            'Inactivo' => $request->estado,
            'wImagen' => $request->wImage ?? '0',
            'hImagen' => $request->hImage ?? '0',
            'Foto' => $fileName ?? ''
        ]);
        if($request->Telefono) {
            $this->setTelefono($nuevoId, $request->Telefono);
        }
        
        return redirect()->route('profesionales.edit', ['profesionale' => $nuevoId]);

    }

    public function update(Request $request, $profesionale)
    {   

        $update = Profesional::find($profesionale);
        $update->Documento = $request->Documento;
        $update->Apellido = $request->Apellido;
        $update->Nombre = $request->Nombre;
        $update->EMail = $request->EMail;
        $update->Direccion = $request->Direccion;
        $update->Provincia = $request->Provincia;
        $update->IdLocalidad = $request->IdLocalidad;
        $update->Firma = $request->Firma;
        $update->CP = $request->CP;
        $update->wImage = $request->wImage;
        $update->hImage = $request->hImage;
        $update->Inactivo = $request->estado;

        if ($request->hasFile('Foto')) {
   
            $fotoExistente = 'public/profesionales/' . $request->Foto;
            if (Storage::exists($fotoExistente)) {
                Storage::delete($fotoExistente);
            }

            $fileName = 'PROF' . $profesionale . '.' . $request->Foto->extension();
            $request->Foto->storeAs('public/profesionales', $fileName);

            $update->Foto = $fileName;
        }

        if($this->checkTelefono($profesionale) === 0) {

            $this->updateTelefono($profesionale, $request->Telefono);
        
        }else{

            $this->setTelefono($profesionale, $request->Telefono);
        }

        $update->push();

        return back();
        
    }

    public function checkDocumento(Request $request): mixed
    {
        $check = Profesional::where('Documento', $request->Documento)->first();

        return response()->json(['check' => $check]);
    }

    public function opciones(Request $request):void
    {

        $prof = Profesional::find($request->Id);

        if($prof)
        {

            $prof->T1 = $request->T1 === 'true' ? 1 : 0;
            $prof->T2 = $request->T2 === 'true' ? 1 : 0;
            $prof->T3 = $request->T3 === 'true' ? 1 : 0;
            $prof->T4 = $request->T4 === 'true' ? 1 : 0;
            $prof->Pago = $request->Pago === 'true' ? 1 : '';
            $prof->proveedor->InfAdj = $request->InfAdj === 'true' ? 1 : 0;
            $prof->TMP = $request->TMP;
            $prof->TLP = $request->TLP;

            $prof->push();
        }
    }

    public function seguro(Request $request): void
    {

        $prof = Profesional::find($request->Id);

        if($prof)
        {

            $prof->MN = $request->MN;
            $prof->MP = $request->MP;
            $prof->SeguroMP = $request->SeguroMP;
            $prof->save();
        }
    }

    public function choisePerfil(Request $request)
    {
        
        $profesionales = Profesional::find($request->Id);
        return response()->json($profesionales);
    }

    public function choiseEspecialidad(Request $request)
    {

        $especialidad = ProfesionalProv::join('proveedores', 'profesionales_prov.IdProv', '=', 'proveedores.Id')
            ->where('profesionales_prov.IdProf', $request->Id)
            ->where('profesionales_prov.Tipo', $request->Tipo)
            ->select('proveedores.Nombre')
        ->get();

        return response()->json($especialidad);
    }

    public function savePrestador(Request $request)
    {
        $arr = ['t1' => 'Efector', 't2' => 'Informador', 't3' => 'Evaluador', 't4' => 'Combinado'];
        $prestador = $arr[$request->perfil]. '|' . $request->especialidad;
        session()->put('mProf', '1');
        session()->put('choiseT', $prestador);
    }

    public function listGeneral(Request $request): mixed
    {

        $data = Profesional::join('proveedores', 'profesionales.IdProveedor', '=', 'proveedores.Id')
            ->select(
                'profesionales.Id as Id',
                DB::raw("CONCAT(profesionales.Apellido, ' ', profesionales.Nombre) AS NombreCompleto"),
            )
            ->where(function($query) use ($request) {
                if ($request->tipo === 'efector') {
                    $query->where('profesionales.T1', '1');
                } elseif ($request->tipo === 'informador') {
                    $query->where('profesionales.T2', '1');
                }
            })
            ->where('profesionales.IdProveedor', $request->proveedor)
            ->where('profesionales.Inactivo', '0')
            ->get();

            
        return response()->json(['resultados' => $data]);
    }

}