<?php

namespace App\Http\Controllers;

use App\Models\Paciente;
use App\Models\Prestacion;
use App\Models\PrestacionesTipo;
use App\Models\Provincia;
use App\Traits\ObserverPacientes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;
use App\Traits\CheckPermission;
use Carbon\Carbon;
use App\Services\ReportesExcel\ReporteExcel;

class PacientesController extends Controller
{
    use ObserverPacientes, CheckPermission;

    protected $reporteExcel;

    public function __construct(ReporteExcel $reporteExcel)
    {
        $this->reporteExcel = $reporteExcel;
    }

    public function index(Request $request): mixed
    {
        if(!$this->hasPermission("pacientes_show")){abort(403);}

        $buscar = $request->buscar;

        if ($request->ajax()) {

            $query = Paciente::join('telefonos', 'pacientes.Id', '=', 'telefonos.IdEntidad')
            ->select(
                'pacientes.Id as Id',
                'pacientes.Nombre as Nombre',
                'pacientes.Apellido as Apellido',
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
        if(!$this->hasPermission("pacientes_show")) {
            return response()->json(['msg' => 'No tiene permisos'], 403);
        }

        $buscar = trim($request->buscar);
        $paciente = $request->paciente;

        $query = $this->queryBasico();

        if (!empty($buscar)) 
        {
            $query->when($buscar, function ($query) use ($buscar, $paciente) {
                $query->where(function ($query) use ($buscar, $paciente) {
                    $query->where('emp.RazonSocial', 'LIKE', '%'.$buscar.'%')
                        ->orWhere('emp.ParaEmpresa', 'LIKE', '%'.$buscar.'%');
                    $query->orWhere(function ($query) use ($buscar, $paciente) {
                        $query->whereExists(function ($subquery) use ($buscar, $paciente) {
                            $subquery->select(DB::raw(1))
                                ->from('clientes')
                                ->whereColumn('art.Id', 'prestaciones.IdART')
                                ->where('art.RazonSocial', 'LIKE', '%'.$buscar.'%');
                        });
                    });
                    $query->orWhere(function ($query) use ($buscar, $paciente) {
                        $query->where('prestaciones.Id', '=', $buscar)
                            ->where('prestaciones.IdPaciente', '=', $paciente);
                    });
                });
            });
        }

        $prestacion = $this->condicionesBasicas($query, $paciente);

        return response()->json(['pacientes' => $prestacion], 200);
    }

    public function create():mixed
    {
        if(!$this->hasPermission("pacientes_add")){abort(403);}
        return view('layouts.pacientes.create', with(['provincias' =>  Provincia::all()]));
    }

    public function store(Request $request): mixed
    {
        if(!$this->hasPermission("pacientes_add")) {
            return response()->json(['msg' => 'No tiene permisos'], 403);
        }

        $nuevoIdPaciente = Paciente::max('Id') + 1;
        $foto = 'foto-default.png';

        if(!empty($request->hasFile('Foto'))) {
            $foto = $this->addFoto($request->hasFile('Foto'), $nuevoIdPaciente, 'create');
        }
        
        Paciente::create([
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
            'Sexo' => $request->Sexo,
        ]);


        if($request->NumeroTelefono)
        {
            $this->addTelefono($request->NumeroTelefono, $nuevoIdPaciente);
        }

        return redirect()->route('pacientes.edit', ['paciente' => $nuevoIdPaciente]);

    }

    public function show(Paciente $paciente): mixed
    {
        if(!$this->hasPermission("pacientes_show")){ abort(403);}
        return view('layouts.pacientes.show', compact(['paciente']));
    }

    public function edit(Paciente $paciente): mixed
    {
        if(!$this->hasPermission("pacientes_edit")){abort(403);}

        $tiposPrestacionPrincipales = ['ART', 'INGRESO', 'PERIODICO', 'OCUPACIONAL', 'EGRESO'];

        return view('layouts.pacientes.edit', with([
                'paciente' => $paciente,
                'provincias' => Provincia::all(),
                'telefono' => $this->getTelefono($paciente->Id) ,
                'suEdad' => Carbon::parse($paciente->FechaNacimiento)->age,
                'tipoPrestacion' => PrestacionesTipo::all(),
                'tipoPrestacionN' => PrestacionesTipo::all(),
                'fichaLaboral' => $this->getFichaLaboral($paciente->Id, null) ?? null,
                'pacientePrestacion' => $this->getPrestacion($paciente->Id),
                'tiposPrestacionOtros' => PrestacionesTipo::whereNotIn('Nombre', $tiposPrestacionPrincipales)->get(),
            ])
        );
    }

    public function update(Request $request, Paciente $paciente)
    {
        if(!$this->hasPermission("pacientes_edit"))
        {
            return response()->json(['msg' => 'No tiene permisos'], 403);
        }

        $paciente = Paciente::find($paciente->Id);

        $data = $request->only([
            'Documento',
            'TipoDocumento',
            'Nombre',
            'Apellido',
            'TipoIdentificacion',
            'Identificacion',
            'FechaNacimiento',
            'EMail',
            'Direccion',
            'Provincia',
            'IdLocalidad',
            'CP',
            'Antecedentes',
            'Observaciones',
            'Sexo'
        ]);

        $paciente->update($data);
        
        if($request->Foto)
        {
            $paciente->Foto = $this->addFoto($request->Foto, $paciente->Id, 'update');
        } 

        $paciente->save();

        if($request->NumeroTelefono)
        {
            $this->addTelefono($request->NumeroTelefono, $paciente->Id);
        }

        return back();
    }

    public function verifyDocument(Request $request)
    {
        if(!$this->hasPermission("pacientes_add")) {
            return response()->json(['msg' => 'No tiene permisos'], 403);
        }

        $paciente = Paciente::where('Documento', $request->documento)->first();
        $existe = $paciente !== null;

        return response()->json(['existe' => $existe, 'paciente' => $paciente]);
    }

    public function down(Request $request): mixed
    {
        if(!$this->hasPermission("pacientes_delete")) {
            return response()->json(['msg' => 'No tiene permisos'], 403);
        }

        $ids = $request->ids;
        if (!is_array($ids)) {
            $ids = [$ids];
        }
        
        foreach($ids as $id)
        {
            $paciente = Paciente::with('prestaciones')->where('Id', $id)->first();

            $contadorAnulados = $paciente->prestaciones->every(function($prestacion) {
                return $prestacion->Anulado === 1;
            });

            if($paciente && (count($paciente->prestaciones) === 0 || $contadorAnulados === true)) {
                $paciente->Estado = 0;
                $paciente->save();
 
                $resultado = ['msg' => 'Paciente dado de baja correctamente: '. $paciente->Nombre.' '.$paciente->Apellido, 'status' => 200];
            }else{

                $resultado = ['msg' => 'Error al dar de baja el paciente '.$paciente->Nombre.' '.$paciente->Apellido.'. Hay prestaciones activas.', 'status' => 409]; 
            }

            $resultados[] = $resultado;
        }
        return response()->json($resultados);
    }

    public function exportExcel(Request $request)
    {
        $ids = $request->input('Id');
        if (! is_array($ids)) {
            $ids = [$ids];
        }

        $pacientes = Paciente::with('localidad')->where('Estado', 1)->whereIn('Id', $ids)->get();

        if($pacientes) {
            $reporte = $this->reporteExcel->crear('pacientes');
            return $reporte->generar($pacientes);

        }else{
            return response()->json(['msg' => 'No se ha podido generar el archivo'], 409);
        }
    }

    //Obtenemos listado pacientes
    public function getPacientes(Request $request)
    {
        $buscar = $request->buscar;

        $resultados = Cache::remember('pacientes_'.$buscar, 5, function () use ($buscar) {

            $pacientes = Paciente::whereRaw("CONCAT(Apellido, ' ', Nombre) LIKE ?", ['%'.$buscar.'%'])
                ->orWhere('Nombre', 'LIKE', '%'.$buscar.'%')
                ->orWhere('Apellido', 'LIKE', '%'.$buscar.'%')
                ->orWhere('Documento', 'LIKE', '%'.$buscar.'%')
                ->get();

            $resultados = [];

            foreach ($pacientes as $paciente) {
                $resultados[] = [
                    'id' => $paciente->Id,
                    'text' => $paciente->Apellido.' '.$paciente->Nombre.' | '.$paciente->Documento,
                ];
            }
            return $resultados;
        });
        return response()->json(['pacientes' => $resultados]);
    }

    public function deletePicture(Request $request): mixed
    {
        $paciente = Paciente::find($request->Id);

        if($paciente) {

            $paciente->Foto = 'foto-default.png';
            $paciente->save();

            return response()->json(['msg' => 'Se ha eliminado la imagen correctamente'], 200);
        }else{
            return response()->json(['msg' => 'No se ha podido eliminar. Intentelo nuevamente mas tarde.'], 500);
        }

        
    }

    private function queryBasico()
    {
        return Prestacion::join('pacientes', 'prestaciones.IdPaciente', '=', 'pacientes.Id')
        ->join('clientes as emp', 'prestaciones.IdEmpresa', '=', 'emp.Id')
        ->join('clientes as art', 'prestaciones.IdART', '=', 'art.Id')
        ->leftJoin('itemsprestaciones', 'prestaciones.Id', '=', 'itemsprestaciones.IdPrestacion')
        ->select(
            DB::raw('(SELECT RazonSocial FROM clientes WHERE Id = prestaciones.IdART) AS Art'),
            DB::raw('(SELECT RazonSocial FROM clientes WHERE Id = prestaciones.IdEmpresa) AS Empresa'),
            DB::raw('COALESCE(COUNT(itemsprestaciones.IdPrestacion), 0) as Total'),
            DB::raw('COALESCE(COUNT(CASE WHEN (itemsprestaciones.CAdj = 5 OR itemsprestaciones.CAdj = 3) AND (itemsprestaciones.CInfo = 3 OR itemsprestaciones.CInfo = 0) THEN itemsprestaciones.IdPrestacion END), 0) as CerradoAdjunto'),
            'emp.ParaEmpresa as ParaEmpresa',
            'emp.Identificacion as Identificacion',
            'prestaciones.Fecha as FechaAlta',
            'prestaciones.Id as Id',
            'pacientes.Nombre as Nombre',
            'pacientes.Apellido as Apellido',
            'prestaciones.TipoPrestacion as Tipo',
            'prestaciones.Anulado as Anulado',
            'prestaciones.Pago as Pago',
            'prestaciones.FechaVto as FechaVencimiento',
            'prestaciones.Ausente as Ausente',
            'prestaciones.IdPaciente as Paciente',
            'prestaciones.Estado as Estado',
            'prestaciones.Facturado as Facturado',
            'prestaciones.Cerrado as Cerrado',
            'prestaciones.Finalizado as Finalizado',
            'prestaciones.Entregado as Entregado',
            'prestaciones.eEnviado as eEnviado',
        );
    }

    private function condicionesBasicas($query, $paciente)
    {
        $resultado = $query->groupBy('prestaciones.Id')
                        ->where('prestaciones.Estado', 1)
                        ->where('prestaciones.IdPaciente', '=', $paciente)
                        ->orderBy('prestaciones.Id', 'DESC')
                        ->paginate(500);
        return $resultado;
    }
}