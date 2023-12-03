<?php

namespace App\Http\Controllers;

use App\Models\Paciente;
use App\Models\Prestacion;
use App\Models\PrestacionesTipo;
use App\Models\Provincia;
use App\Models\Telefono;
use App\Traits\Components;
use App\Traits\ObserverPacientes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Yajra\DataTables\DataTables;

class PacientesController extends Controller
{
    use Components, ObserverPacientes;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        return view('layouts.pacientes.index');
    }

    /**
     * Search
     */
    public function search(Request $request)
    {

        $buscar = trim($request->buscar);

        if ($request->ajax()) {

            $query = Paciente::join('telefonos', 'pacientes.Id', '=', 'telefonos.IdEntidad')
            ->select(
                'pacientes.Id as Id',
                DB::raw("CONCAT(pacientes.Apellido, ' ', pacientes.Nombre) as NombreCompleto"),
                DB::raw("CONCAT(pacientes.TipoDocumento, ' ', pacientes.Documento) as Documento"),
                'telefonos.NumeroTelefono as Telefono',
                'telefonos.CodigoArea as Cp'
            )
            ->where(function ($query) use ($buscar) {
                $query->where(function ($query) use ($buscar) {
                        $query->where('pacientes.Apellido', 'LIKE', '%'.$buscar.'%')
                            ->orWhere('pacientes.Nombre', 'LIKE', '%'.$buscar.'%');
                    })
                    ->orWhere(function ($query) use ($buscar) {
                        $query->whereRaw("CONCAT(pacientes.Apellido, ' ', pacientes.Nombre) LIKE ?", ['%'.$buscar.'%']);
                    })
                    ->orWhere(function ($query) use ($buscar) {
                        $query->where('pacientes.Documento', '=', $buscar)
                            ->orWhere('pacientes.Documento', 'LIKE', '%'.$buscar.'%');
                    });
            })
            ->where('pacientes.Estado', 1)
            ->orderBy('pacientes.Id', 'DESC');
                

            return Datatables::of($query)->make(true);

        }

        return view('layouts.pacientes.index');
    }

    public function searchPrestPacientes(Request $request): mixed
    {

        $prestacion = Cache::remember('prestacion_pac', 1, function () use ($request) {

            $buscar = trim($request->buscar);
            $paciente = $request->paciente;

            $query = Prestacion::join('pacientes', 'prestaciones.IdPaciente', '=', 'pacientes.Id')
                ->join('clientes', 'prestaciones.IdEmpresa', '=', 'clientes.Id')
                ->select(
                    DB::raw('(SELECT RazonSocial FROM clientes WHERE Id = prestaciones.IdART) AS Art'),
                    DB::raw('(SELECT RazonSocial FROM clientes WHERE Id = prestaciones.IdEmpresa) AS RazonSocial'),
                    'clientes.ParaEmpresa as ParaEmpresa',
                    'clientes.Identificacion as Identificacion',
                    'prestaciones.Fecha as FechaAlta',
                    'prestaciones.Id as Id',
                    'pacientes.Nombre as Nombre',
                    'pacientes.Apellido as Apellido',
                    'prestaciones.Anulado as Anulado',
                    'prestaciones.Pago as Pago',
                    'prestaciones.FechaVto as FechaVencimiento',
                    'prestaciones.Ausente as Ausente',
                    'prestaciones.IdPaciente as Paciente',
                    'prestaciones.Estado as Estado')
                ->where('prestaciones.Estado', 1)
                ->where('prestaciones.IdPaciente', '=', $paciente)
                ->orderBy('prestaciones.Id', 'DESC');

            $query->when($buscar, function ($query) use ($buscar, $paciente) {
                $query->where(function ($query) use ($buscar, $paciente) {
                    $query->where('clientes.RazonSocial', 'LIKE', '%'.$buscar.'%')
                        ->orWhere('clientes.ParaEmpresa', 'LIKE', '%'.$buscar.'%');
                    $query->orWhere(function ($query) use ($buscar, $paciente) {
                        $query->whereExists(function ($subquery) use ($buscar, $paciente) {
                            $subquery->select(DB::raw(1))
                                ->from('clientes')
                                ->whereColumn('clientes.Id', 'prestaciones.IdART')
                                ->where('clientes.RazonSocial', 'LIKE', '%'.$buscar.'%')
                                ->where('prestaciones.IdPaciente', $paciente)
                                ->where('prestaciones.Estado', 1);
                        });
                    });
                    $query->orWhere(function ($query) use ($buscar, $paciente) {
                        $query->where('prestaciones.Id', '=', $buscar)
                            ->where('prestaciones.IdPaciente', '=', $paciente);
                    });
                });
            });

            return $query->cursorPaginate(500);
        });

        return response()->json(['pacientes' => $prestacion]);
    }

    public function create():mixed
    {

        return view('layouts.pacientes.create', with(['provincias' =>  Provincia::all()]));
    }

    public function store(Request $request): mixed
    {

        $nuevoIdPaciente = Paciente::max('Id') + 1;

        $foto = $this->addFoto($request->Foto, $nuevoIdPaciente, 'create');

        $paciente = Paciente::create([
            'Id' => $nuevoIdPaciente,
            'Nombre' => $request->Nombre,
            'Apellido' => $request->Apellido,
            'TipoDocumento' => $request->TipoDocumento,
            'Documento' => $request->Documento,
            'TipoIdentificacion' => $request->TipoIdentificacion,
            'EstadoCivil' => $request->EstadoCivil,
            'Identificacion' => $request->Identificacion,
            'FechaNacimiento' => $request->FechaNacimiento,
            'EMail' => $request->EMail,
            'Direccion' => $request->Direccion,
            'Provincia' => $request->Provincia,
            'IdLocalidad' => $request->IdLocalidad,
            'CP' => $request->CP,
            'Estado' => 1,
            'Foto' => $foto,
            'Antecedentes' => $request->Antecedentes,
            'Observaciones' => $request->Observaciones,
        ]);

        $paciente->save();

        if($request->NumeroTelefono)
        {
            $this->addTelefono($request->NumeroTelefono, $nuevoIdPaciente, 'create');
        }

        return redirect()->route('pacientes.edit', ['paciente' => $nuevoIdPaciente]);

    }

    public function show(Paciente $paciente): mixed
    {
        return view('layouts.pacientes.show', compact(['paciente']));
    }

    public function edit(Paciente $paciente): mixed
    {
        
        $tiposPrestacionPrincipales = ['ART', 'INGRESO', 'PERIODICO', 'OCUPACIONAL', 'EGRESO', 'OTRO'];

        return view('layouts.pacientes.edit', with([
                'paciente' => $paciente,
                'provincias' => Provincia::all(),
                'telefono' => $this->getTelefono($paciente->Id) ,
                'suEdad' => $this->getAge($paciente->FechaNacimiento) ?? '',
                'tipoPrestacion' => PrestacionesTipo::all(),
                'dataArt' => $this->getFichaLaboral($paciente->Id, 'art')?? null,
                'dataCliente' => $this->getFichaLaboral($paciente->Id, 'empresa')?? null,
                'fichaLaboral' => $this->getFichaLaboral($paciente->Id, null) ?? null,
                'pacientePrestacion' => $this->getPrestacion($paciente->Id),
                'tiposPrestacionOtros' => PrestacionesTipo::whereNotIn('Nombre', $tiposPrestacionPrincipales)->get(),
            ])
        );
    }

    public function updateFinanciador(Request $request)
    {
        $art = $this->getFichaLaboral($request->Id, 'art');
        $cliente = $this->getFichaLaboral($request->Id, 'empresa');

        return response()->json(['art' => $art, 'cliente' => $cliente]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Paciente $paciente)
    {

        $paciente = Paciente::find($paciente->Id);
        $paciente->Documento = $request->Documento;
        $paciente->TipoDocumento = $request->TipoDocumento;
        $paciente->Nombre = $request->Nombre;
        $paciente->Apellido = $request->Apellido;
        $paciente->TipoIdentificacion = $request->TipoIdentificacion;
        $paciente->Identificacion = $request->Identificacion;
        $paciente->FechaNacimiento = $request->FechaNacimiento;
        $paciente->EMail = $request->EMail;
        $paciente->Direccion = $request->Direccion;
        $paciente->Provincia = $request->Provincia;
        $paciente->IdLocalidad = $request->IdLocalidad;
        $paciente->CP = $request->CP;
        if($request->Foto)
        {
            $paciente->Foto = $this->addFoto($request->Foto, $paciente->Id, 'update');
        } 
        $paciente->Antecedentes = $request->Antecedentes;
        $paciente->Observaciones = $request->Observaciones;
        $paciente->save();

        if($request->NumeroTelefono)
        {
            $this->addTelefono($request->NumeroTelefono, $paciente->Id, 'update');
        }

        return back();

    }

    public function down(Request $request): void
    {
        $paciente = Paciente::find($request->Id);

        if($paciente){

            $paciente->Estado = '0';
            $paciente->save();

        } 
    }

    public function verifyDocument(Request $request)
    {
        $paciente = Paciente::where('Documento', $request->documento)->first();
        $existe = $paciente !== null;

        return response()->json(['existe' => $existe, 'paciente' => $paciente]);
    }

    public function multipleDown(Request $request)
    {
        $ids = $request->input('ids');
        if (! is_array($ids)) {
            $ids = [$ids];
        }

        Paciente::whereIn('id', $ids)->update(['Estado' => 0]);

        return redirect()->back();
    }

    public function exportExcel(Request $request)
    {
        $ids = $request->input('Id');
        if (! is_array($ids)) {
            $ids = [$ids];
        }

        $pacientes = DB::table('pacientes')
            ->join('localidades', 'pacientes.IdLocalidad', '=', 'localidades.Id')
            ->where('pacientes.Estado', 1)
            ->whereIn('pacientes.Id', $ids)
            ->select(
                'pacientes.Id', 
                'pacientes.Apellido', 
                'pacientes.Nombre', 
                'pacientes.Identificacion', 
                'pacientes.Documento', 
                'pacientes.Nacionalidad', 
                'pacientes.FechaNacimiento', 
                'pacientes.LugarNacimiento', 
                'pacientes.EstadoCivil', 
                'pacientes.ObsEstadoCivil', 
                'pacientes.Hijos', 
                'pacientes.Direccion', 
                'localidades.Nombre as Localidad', 
                'pacientes.Provincia', 
                'pacientes.EMail', 
                'pacientes.ObsEMail', 
                'pacientes.Antecedentes', 
                'pacientes.Observaciones')
            ->get();

        $excel = "Numero,Apellido,Nombre,CUIL/CUIT,Documento,Nacionalidad,Fecha de Nacimiento, Lugar de Nacimiento, Estado Civil, Observacion de Estado Civil, Hijos, Direccion, Localidad, Provincia, Email, Observacion Email, Antecedentes, Observaciones\n";
        foreach ($pacientes as $row) {
            $numero = $row->Id ?? '-';
            $apellido = $row->Apellido ?? '-';
            $nombre = $row->Nombre ?? '-';
            $identificacion = $row->Identificacion ?? '-';
            $documento = $row->Documento ?? '-';
            $nacionalidad = $row->Nacionalidad ?? '-';
            $fechaNacimiento = $row->FechaNacimiento ?? '-';
            $lugarNacimiento = $row->LugarNacimiento ?? '-';
            $estadoCivil = $row->EstadoCivil ?? '-';
            $obsEstadoCivil = $row->ObsEstadoCivil ?? '-';
            $hijos = $row->Hijos ?? '-';
            $direccion = $row->Direccion ?? '-';
            $localidad = $row->Localidad ?? '-';
            $provincia = $row->Provincia ?? '-';
            $email = $row->EMail ?? '-';
            $obsEmail = $row->ObsEMail ?? '-';
            $antecedentes = $row->Antecedentes ?? '-';
            $observaciones = $row->Observaciones ?? '-';

            $excel .= "$numero,$apellido,$nombre,$identificacion,$documento,$nacionalidad,$fechaNacimiento,$lugarNacimiento,$estadoCivil,$obsEstadoCivil,$hijos,$direccion,$localidad,$provincia,$email,$obsEmail,$antecedentes,$observaciones\n";
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

    //Obtenemos listado pacientes
    public function getPacientes(Request $request)
    {
        $buscar = $request->buscar;

        $resultados = Cache::remember('pacientes_'.$buscar, 5, function () use ($buscar) {

            $pacientes = Paciente::where('Nombre', 'LIKE', '%'.$buscar.'%')
                ->orWhere('Apellido', 'LIKE', '%'.$buscar.'%')
                ->orWhere('Documento', 'LIKE', '%'.$buscar.'%')
                ->get();

            $resultados = [];

            foreach ($pacientes as $paciente) {
                $resultados[] = [
                    'id' => $paciente->Id,
                    'text' => $paciente->Nombre.' '.$paciente->Apellido.' | '.$paciente->Documento,
                ];
            }

            return $resultados;

        });

        return response()->json(['pacientes' => $resultados]);
    }

    public function deletePicture(Request $request): void
    {
        $paciente = Paciente::find($request->Id);
        $paciente->Foto = 'foto-default.png';
        $paciente->save();
        
    }
}