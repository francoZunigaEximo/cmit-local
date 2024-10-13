<?php

namespace App\Http\Controllers;

use App\Helpers\FileHelper;
use App\Models\Profesional;
use App\Models\ProfesionalProv;
use App\Models\Proveedor;
use App\Models\Provincia;
use App\Models\User;
use App\Models\Rol;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use App\Traits\ObserverProfesionales;
use App\Traits\ObserverPacientes;

use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Storage;

use App\Traits\CheckPermission;
use Illuminate\Support\Facades\Auth;

class ProfesionalesController extends Controller
{
    use ObserverProfesionales, ObserverPacientes, CheckPermission;

    protected $folder = "Prof";

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

            $query = Profesional::join('proveedores', 'profesionales.IdProveedor', '=', 'proveedores.Id')
            ->join('users', 'profesionales.Id', '=' , 'users.profesional_id')
            ->join('datos', 'users.datos_id', '=', 'datos.Id')
            ->select(
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
                'profesionales.T5 as Evaluador ART',
                'profesionales.TLP as Login',
                'profesionales.Pago as Pago',
                'profesionales.Inactivo as Estado',
                'users.name as NombreUsuario',
                'profesionales.RegHis as RegHis',
            );

            $query->when(is_array($tipo), function ($query) use ($tipo) {
                foreach ($tipo as $valor) {
                    $campo = strtoupper($valor);
                    $query->when(in_array($valor, ['t1', 't2', 't3', 't4', 't5']), function ($query) use ($campo) {
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

            $result = $query->where('profesionales.Id', '<>', 0)
                ->orderBy('profesionales.Apellido', 'Asc');

            return Datatables::of($result)->make(true);
        }
        return view('layouts.profesionales.index');
    }

    public function create()
    {

        return view('layouts.profesionales.create', with([
            'provincias' => Provincia::all(),
            'usuarios' => User::where('profesional_id', 0)->where('datos_id', 0)->where('Anulado', 0)->get(['id', 'name'])
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
            'lstProveedor' => Proveedor::where('Inactivo', 0)->whereNot('Id', 0)->OrderBy('Nombre', 'ASC')->get(['Id', 'Nombre']),
            'listEspecialistas' => Proveedor::where('Inactivo', 0)->whereNot('Id', 0)->OrderBy('Nombre', 'ASC')->get(['Id', 'Nombre']),
            'detailsLocalidad' => $this->getLocalidad($profesionale->IdLocalidad),
            'usuarios' => User::where('profesional_id', 0)->where('Anulado', 0)->get(['id', 'name'])
        ]));
    }

    public function setPerfil(Request $request)
    {

        $query = Profesional::find($request->Id);
        if(in_array($query->IdProveedor,[null,0])){

            $query->IdProveedor = $request->especialidad;
            $query->save();
        }
        $rol = Rol::find($request->perfil);
        $consulta = ProfesionalProv::where('IdProf', $request->Id)
            ->where('IdProv', $request->especialidad)
            ->where('IdRol', $rol->nombre)
            ->first();

        if (!$consulta) {

            ProfesionalProv::create([
                'Id' => ProfesionalProv::max('Id') + 1,
                'IdProf' => $request->Id,
                'IdProv' => $request->especialidad,
                'IdRol' => $rol->nombre,
            ]);

            return response()->json(['msg' => 'Se ha añadido el perfil de manera correcta'], 201);
        }else{

            return response()->json(['msg' => 'Ya existe un perfil con esa especialidad'], 500);
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
                'profesionales_prov.IdRol as Tipos'
            )
            ->where('IdProf', $request->Id)
            ->get();

        if($query){
            return response()->json(['data' => $query], 200);
        }else{
            return response()->json(['msg' => "Sin perfiles. Ingrese uno"], 409);
        }

        

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
            //$request->Foto->storeAs('public/profesionales', $fileName);
            FileHelper::uploadFile(FileHelper::getFileUrl('escritura').'/'.$this->folder.'/', $request->Foto, $fileName);

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

    public function opciones(Request $request):mixed
    {
        $prof = Profesional::find($request->Id);

        if($prof)
        {
            $prof->Pago = $request->Pago == 'true' ? 1 : '';
            $prof->InfAdj = $request->InfAdj == 'true' ? 1 : 0;
            $prof->TMP = $request->TMP;
            $prof->TLP = $request->TLP;

            $prof->save();

            return response()->json(['msg' => 'Se han guardado los cambios de manera correcta'], 200);
        }else{
            return response()->json(['msg' => 'No se ha encontrado el identificador para guardar'], 409);
        }
    }

    public function seguro(Request $request): mixed
    {
        $prof = Profesional::find($request->Id);

        if($prof)
        {
            $prof->MN = $request->MN;
            $prof->MP = $request->MP;
            $prof->SeguroMP = $request->SeguroMP;
            $prof->save();
        
            return response()->json(['msg' => 'Se han guardado los datos'], 200);
        }else{
            return response()->json(['msg' => 'No se encontro el identificador'], 409);
        }
    }

    public function choisePerfil(Request $request)
    {
        
        //$profesionales = Profesional::find($request->Id);
        //return response()->json($profesionales);

        return Rol::join('user_rol', 'roles.Id', '=', 'user_rol.rol_id')
            ->leftJoin('users', 'user_rol.user_id', '=', 'users.Id')
            ->leftJoin('rol_permisos', 'roles.Id', '=', 'rol_permisos.rol_id')
            ->leftJoin('permisos', 'rol_permisos.permiso_id', '=', 'permisos.Id')
            ->select(
                'roles.nombre as Nombre',
                'roles.Id as Id'
            )
            ->where('users.profesional_id', $request->Id)
            ->whereIn('roles.nombre', ["Efector", "Informador", "Evaluador", "Combinado", "Evaluador ART"])
            ->groupBy('roles.nombre')
            ->get(); 
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