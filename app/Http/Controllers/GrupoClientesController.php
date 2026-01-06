<?php

namespace App\Http\Controllers;

use App\Helpers\Tools;
use App\Models\Cliente;
use App\Models\GrupoClientes;
use App\Models\PaqueteEstudio;
use App\Models\PaqueteFacturacion;
use App\Models\RelacionGrupoCliente;
use App\Models\RelacionPaqueteEstudio;
use App\Services\Reportes\ReporteService;
use App\Services\ReportesExcel\ReporteExcel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;

use App\Traits\CheckPermission;

class GrupoClientesController extends Controller
{
    use CheckPermission;

    protected $reporteService;
    protected $outputPath;
    protected $sendPath;
    protected $fileNameExport;
    private $tempFile;
    protected $reporteExcel;

    public function __construct(ReporteService $reporteService, ReporteExcel $reporteExcel)
    {
        $this->reporteService = $reporteService;
        $this->outputPath = storage_path('app/public/temp/fusionar.pdf');
        $this->sendPath = storage_path('app/public/temp/cmit-' . Tools::randomCode(15) . '-informe.pdf');
        $this->fileNameExport = 'reporte-' . Tools::randomCode(15);
        $this->tempFile = 'app/public/temp/file-';
        $this->reporteExcel = $reporteExcel;
    }

    public function index()
    {
        if(!$this->hasPermission("grupos_show")) {
            return response()->json(["msg" => "No tiene permisos"], 403);
        }

        return view('layouts.grupos.index');
    }

    public function searchGrupos(Request $request)
    {
        if(!$this->hasPermission("grupos_show")) {
            return response()->json(["msg" => "No tiene permisos"], 403);
        }

        if ($request->ajax()) {
            $query = $this->buildQuery($request);
            return DataTables::of($query)->make(true);
        }
    }

    private function buildQuery(Request $request)
    {
        $consulta = GrupoClientes::where('Nombre', 'LIKE', '%' . $request->buscar . '%')->where('Baja', '=', 0);
        return $consulta;
    }

    public function exportExcel(Request $request)
    {
        $query = $this->buildQuery($request);
        $reporte = $this->reporteExcel->crear('grupoClienteFull');
        return $reporte->generar($query->get());
    }

    public function create()
    {
        $Codigo = GrupoClientes::max('Id') + 1;
        return view('layouts.grupos.create', ['Codigo' => $Codigo]);
    }

    public function getCliente(Request $request)
    {
        $empresa = Cliente::find($request->id);
        return response()->json($empresa);
    }

    public function getGrupo(Request $request)
    {
        $grupo = GrupoClientes::find($request->id);
        return response()->json($grupo);
    }

    public function postGrupoCliente(Request $request)
    {
        $nombre = $request->Nombre;
        $empresas = $request->Empresas;

        if(GrupoClientes::where('Nombre', '=', $nombre)->where('Baja', '=', 0)->exists()) {
            return response()->json(['error' => 'El nombre del grupo ya existe.'], 400);
        }

        $id = GrupoClientes::max('Id') + 1;
        GrupoClientes::create([
            'Id' => $id,
            'Nombre' => $nombre
        ]);

        foreach ($empresas as $empresa) {
            $idRegulacionGrupo = RelacionGrupoCliente::max('Id') + 1;

            RelacionGrupoCliente::create([
                'Id' => $idRegulacionGrupo,
                'IdGrupo' => $id,
                'IdCliente' => $empresa['Id'],
                'Baja' => 0
            ]);
        }

        return response()->json(['id' => $id], 200);
    }

    public function edit($id)
    {
        $grupo = GrupoClientes::find($id);

        return view('layouts.grupos.edit', compact(['grupo']));
    }

    public function getEmpresasGrupoCliente(Request $request)
    {
        $empresasGrupos = RelacionGrupoCliente::where('IdGrupo', '=', $request->Id)->where('Baja', '=', 0)->get();
        return response()->json($empresasGrupos);
    }

    public function postEditGrupoCliente(Request $request)
    {
        $id = $request->Id;
        $nombre = $request->Nombre;

        $nuevosClientes = $request->ClientesNuevos == null ? [] : $request->ClientesNuevos;
        $clientesEliminar = $request->ClientesEliminar == null ? [] : $request->ClientesEliminar;

        GrupoClientes::find($id)->update([
            'Nombre' => $nombre
        ]);

        foreach ($nuevosClientes as $nuevos) {
            $idRegulacionGrupo = RelacionGrupoCliente::max('Id') + 1;
            RelacionGrupoCliente::create([
                'Id' => $idRegulacionGrupo,
                'IdGrupo' => $id,
                'IdCliente' => $nuevos['Id'],
                'Baja' => 0
            ]);
        }

        foreach ($clientesEliminar as $eliminar) {
            RelacionGrupoCliente::where('IdGrupo', '=', $id)
                ->where('IdCliente', '=', $eliminar)
                ->update(['Baja' => 1]);
        }
    }

    public function deleteGrupoCliente(Request $request)
    {
        if(!$this->hasPermission("grupos_delete")) {
            $id = $request->id;
            GrupoClientes::find($id)->update(['Baja' => 1]);
        }
    }

    public function detalle()
    {
        return view('layouts.grupos.detalle');
    }

    public function detalleSearch(Request $request)
    {
        if ($request->ajax()) {
            $query = $this->buildQueryDetalle($request);
            return DataTables::of($query)->make(true);
        }
    }

    public function exportDetalleExcel(Request $request)
    {
        $query = $this->buildQueryDetalle($request);
        $reporte = $this->reporteExcel->crear('grupoClienteDestalleFull');
        return $reporte->generar($query->get());
    }

    private function buildQueryDetalle(Request $request)
    {
        $consulta = DB::table('clientesgrupos')
            ->join('clientesgrupos_it', 'clientesgrupos.Id', '=', 'clientesgrupos_it.IdGrupo')
            ->join('clientes', 'clientesgrupos_it.IdCliente', '=', 'clientes.Id')
            ->where('clientesgrupos.Nombre', 'LIKE', '%' . $request->buscar . '%')
            ->where('clientesgrupos.Baja', '=', 0);

        if ($request->IdGrupo) {
            $consulta->where('clientesgrupos.Id', '=', $request->IdGrupo);
        }
        if ($request->IdCliente) {
            $consulta->where('clientesgrupos_it.IdCliente', '=', $request->IdCliente);
        }
        if ($request->NroCliente) {
            $consulta->where('clientes.Id', 'LIKE', $request->NroCliente);
        }

        $consulta->select('clientesgrupos.Nombre as NombreGrupo', 'clientes.Id as NroCliente', 'clientes.RazonSocial as RazonSocial', 'clientes.ParaEmpresa as ParaEmpresa', 'clientes.Identificacion as CUIT');
        return $consulta;
    }

    public function grupos(Request $request): mixed
    {

        $buscar = $request->buscar;

        $resultados = Cache::remember('Grupo'.$buscar, 5, function () use ($buscar) {

            $grupos = GrupoClientes::where('Nombre', 'LIKE', '%'.$buscar.'%')
            ->where('Baja', '=', 0)
            ->get();

            $resultados = [];

            foreach ($grupos as $grupo) {
                $resultados[] = [
                    'id' => $grupo->Id,
                    'text' => $grupo->Nombre,
                ];
            }

            return $resultados;

        });

        return response()->json(['grupo' => $resultados]);
    }

}
