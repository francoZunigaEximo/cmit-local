<?php

namespace App\Http\Controllers;

use App\Models\Examen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Yajra\DataTables\DataTables;
use App\Traits\ObserverExamenes;
use App\Traits\CheckPermission;
use App\Traits\ReporteExcel;

class ExamenesController extends Controller
{
    use ObserverExamenes, CheckPermission, ReporteExcel;

    public function index()
    {
        if(!$this->hasPermission("examenes_show")) {
            abort(403);
        }

        return view("layouts.examenes.index");
    }

    public function create()
    {
        if(!$this->hasPermission("examenes_add")) {
            abort(403);
        }

        $estudios = $this->getEstudios();
        $reportes = $this->getReportes();
        $proveedores = $this->getProveedor();
        $aliasexamenes = $this->getAliasExamenes();

        return view("layouts.examenes.create", compact(['estudios', 'reportes', 'proveedores','aliasexamenes']));
    }

    public function store(Request $request)
    {
        if(!$this->hasPermission("examenes_add")) {
            return response()->json(["msg" => "No tiene permisos"], 403);
        }

        Examen::create([
            'Id' => Examen::max('Id') + 1,
            'Nombre' => $request->Examen ?? '',
            'IdEstudio' => $request->Estudio ?? 0,
            'Descripcion' => $request->Descripcion ?? '',
            'IdReporte' => $request->Reporte ?? 0,
            'IdProveedor' => $request->ProvEfector ?? 0,
            'IdProveedor2' => $request->ProvInformador ?? 0,
            'DiasVencimiento' => $request->DiasVencimiento ?? 0,
            'Inactivo' => ($request->Inactivo === 'on' ? '1' : '0'),
            'IdForm' => $request->Formulario ?? '',
            'Cod' => $request->CodigoEx ?? '',
            'Cod2' => $request->CodigoE ?? '',
            'Ausente' => $request->Ausente ?? 0,
            'Devol' => ($request->Devolucion === 'on' ? '1' : '0'),
            'Informe' => ($request->Informe === 'on' ? '1' : '0'),
            'Adjunto' => ($request->Adjunto === 'on' ? '1' : '0'),
            'NoImprime' => ($request->Fisico === 'on' ? '1' : '0'),
            'PI' => ($request->priImpresion === 'on' ? '1' : '0'),
            'Evaluador' => ($request->EvalExclusivo === 'on' ? '1' : '0'),
            'EvalCopia' => ($request->ExpAnexo === 'on' ? '1' : '0'),
            'aliasexamen' => $request->aliasexam ?? ''
        ]);

        return redirect()->route('examenes.index');
    }

    public function edit(Examen $examene)
    {
        if(!$this->hasPermission("examenes_edit")) {
            return response()->json(["msg" => "No tiene permisos"], 403);
        }
        
        $estudios = $this->getEstudios();
        $reportes = $this->getReportes();
        $proveedores = $this->getProveedor();
        $aliasexamenes = $this->getAliasExamenes();

        return view("layouts.examenes.edit", compact(['examene', 'estudios', 'reportes', 'proveedores', 'aliasexamenes']));
    }

    public function search(Request $request): mixed
    {

        $buscar = $request->buscar;

        $resultados = Cache::remember('Examen'.$buscar, 5, function () use ($buscar) {

            $examenes = Examen::where(function ($query) use ($buscar) {
                $query->where('Nombre', 'LIKE', '%'.$buscar.'%');
            })->where('Inactivo', '<>', 2)->get();

            $resultados = [];

            foreach ($examenes as $examen) {
                $resultados[] = [
                    'id' => $examen->Id,
                    'text' => $examen->Nombre,
                ];
            }

            return $resultados;

        });

        return response()->json(['examen' => $resultados]);
    }

    public function getId(Request $request): mixed
    {
        $getId = Examen::find($request->IdExamen);
        return $getId && response()->json(['examenes' => $getId]);
    }

    public function deleteEx(Request $request): mixed
    {   
        if(!$this->hasPermission("examenes_delete")) {
            return response()->json(["msg" => "No tiene permisos"], 403);
        }

        $examen = Examen::find($request->Id);

        if ($examen && count($this->auditarExamen($request->Id)) > 0) {
            
            return response()->json(['estatus' => true]);

        } else {
            
            $examen->update(['Inactivo' => '2']);
            return response()->json(['estatus' => false]);
        }
    }

    public function searchExamenes(Request $request)
    {
        $examen = $request->examen;
        $especialidad = $request->especialidad;
        $atributos = $request->atributos;
        $opciones = $request->opciones;
        $estado = $request->estado;
        $codigoex = $request->codigoex;
        $activo = $request->activo;

        $lstAtributos = [
            'informe' => 'examenes.Informe',
            'adjunto' => 'examenes.Adjunto',
            'cerrado' => 'examenes.Cerrado',
            'fisico'  => 'examenes.NoImprime',
        ];

        $lstOpciones = [
            'evalExclusivo' => 'examenes.Evaluador',
            'expAnexos' => 'examenes.EvalCopia',
            'priImpresion' => 'examenes.PI',
        ];

        $query = Examen::join('estudios', 'examenes.IdEstudio', '=', 'estudios.Id')
            ->join('reportes', 'examenes.IdReporte', '=', 'reportes.Id')
            ->join('proveedores as efector', 'examenes.IdProveedor', '=', 'efector.Id')
            ->join('proveedores as informador', 'examenes.IdProveedor2', '=', 'informador.Id')
            ->select(
                'examenes.Id as IdExamen',
                'examenes.Inactivo as Inactivo',
                'examenes.PI as prioridadImpresion',
                'examenes.Nombre as NombreExamen',
                'examenes.DiasVencimiento as Vto',
                'examenes.Cod as CodigoExamen',
                'examenes.Cod2 as CodigoEfector',
                'estudios.Nombre as Estudio',
                'efector.Nombre as ProvEfector',
                'informador.Nombre as ProvInformador',
                'reportes.Nombre as NombreReporte'
            )
            ->where('examenes.Id', '<>', 0);

            $query->when($examen, function ($query) use ($examen) {
                $query->where('examenes.Id', $examen);
            });

            $query->when($especialidad, function ($query) use ($especialidad) {
                $query->where('efector.Id', $especialidad);
            });

            $query->when(isset($lstAtributos[$atributos]), function ($query) use ($atributos, $lstAtributos) {
                $query->where($lstAtributos[$atributos], 1);
            });

            $query->when(isset($lstOpciones[$opciones]), function ($query) use ($opciones, $lstOpciones) {
                $query->where($lstOpciones[$opciones], 1);
            });

            $query->when($opciones === 'formulario', function ($query) {
                $query->where('examenes.IdForm', '>', 0);
            });

            $query->when($opciones === 'sinReporte', function ($query) {
                $query->where('examenes.IdReporte', 0);
            });

            $query->when($opciones === 'sinVencimiento', function ($query) {
                $query->where('examenes.DiasVencimiento', 0);
            });

            $query->when($estado === 'ausente', function ($query) {
                $query->where('examenes.Ausente', 1);
            });

            $query->when($estado === 'devolucion', function ($query) {
                $query->where('examenes.Devol', 1);
            });

            $query->when($codigoex, function ($query) use ($codigoex) {
                $query->where('examenes.Cod', 'LIKE', '%'.$codigoex.'%');
            });

            $query->when($activo === 'Activo', function ($query) {
                $query->where('examenes.Inactivo', 0);
            });

            $query->when($activo === 'nActivo', function ($query) {
                $query->where('examenes.Inactivo', 1);
            });

            $query->when($activo === 'tActivo', function ($query) {
                $query->where(function ($subquery) {
                    $subquery->where('examenes.Inactivo', 0)
                        ->orWhere('examenes.Inactivo', 1);
                });
            });

            $result = $query->where('examenes.Inactivo', '<>', 2)->orderBy('examenes.Nombre', 'ASC');

            return Datatables::of($result)->make(true);
    }

    public function updateExamen(Request $request)
    {
        if(!$this->hasPermission("examenes_edit")) {
            return response()->json(["msg" => "No tiene permisos"], 403);
        }

        $examen = Examen::find($request->Id);
        if($examen)
        {
            $examen->Nombre = $request->Examen ?? '';
            $examen->IdEstudio = $request->IdEstudio ?? 0;
            $examen->Descripcion = $request->Descripcion ?? '';
            $examen->IdReporte = $request->IdReporte ?? 0;
            $examen->IdProveedor = $request->IdProveedor ?? 0;
            $examen->IdProveedor2 = $request->IdProveedor2 ?? 0;
            $examen->DiasVencimiento = $request->DiasVencimiento ?? 0;
            $examen->Inactivo = ($request->Inactivo === 'true' ? '1' : '0');
            $examen->IdForm = $request->IdForm ?? 0;
            $examen->Cod = $request->Cod ?? '';
            $examen->Cod2 = $request->Cod2 ?? '';
            $examen->Ausente = $request->Ausente ?? 0;
            $examen->Devol = ($request->Devol === 'true' ? '1' : '0');
            $examen->Informe = ($request->Informe === 'true' ? '1' : '0');
            $examen->Adjunto = ($request->Adjunto === 'true' ? '1' : '0');
            $examen->NoImprime = ($request->NoImprime === 'true' ? '1' : '0');
            $examen->PI = ($request->PI === 'true' ? '1' : '0');
            $examen->Evaluador = ($request->Evaluador === 'true' ? '1' : '0');
            $examen->EvalCopia = ($request->EvalCopia === 'true' ? '1' : '0');
            $examen->aliasexamen = $request->aliasexamen ?? '';
            $examen->save();

            return response()->json(['msg' => 'Se ha actualizado el exámen correctamente'], 200);
        }else{
            return response()->json(['msg' => 'No se ha podido actualizar el examen'], 406);
        }
        
    }

    public function excel(Request $request)
    {
        $ids = $request->input('Ids');
        if (! is_array($ids)) {
            $ids = [$ids];
        }

        $examenes = Examen::with(['estudios','proveedor1', 'proveedor2', 'reportes'])->whereIn('Id', $ids)->get();

        if($examenes) {
            return $this->listadoExamen($examenes);
        }else{
            return response()->json(['msg' => 'No se ha podido generar el archivo'], 409);
        }
    }

}
 