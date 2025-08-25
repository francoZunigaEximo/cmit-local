<?php

namespace App\Http\Controllers;

use App\Helpers\Tools;
use App\Models\Cliente;
use App\Models\Estudio;
use App\Models\Examen;
use App\Models\GrupoClientes;
use App\Models\PaqueteEstudio;
use App\Models\PaqueteFacturacion;
use App\Models\RelacionPaqueteEstudio;
use App\Models\RelacionPaqueteFacturacion;
use App\Services\Reportes\ReporteService;
use App\Services\Reportes\Titulos\Empresa;
use App\Services\ReportesExcel\ReporteExcel;
use GuzzleHttp\Psr7\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;

class PaquetesController extends Controller
{
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
        $this->sendPath = storage_path('app/public/temp/cmit-'.Tools::randomCode(15).'-informe.pdf');
        $this->fileNameExport = 'reporte-'.Tools::randomCode(15);
        $this->tempFile = 'app/public/temp/file-';
        $this->reporteExcel = $reporteExcel;
    }

    public function index()
    {
        if(!$this->hasPermission("paquetes_show")) {
            return response()->json(["msg" => "No tiene permisos"], 403);
        }

        return view('layouts.paquetes.index');
    }

    public function searchExamenes(Request $request){
        if(!$this->hasPermission("paquetes_show")) {
            return response()->json(["msg" => "No tiene permisos"], 403);
        }


        if ($request->ajax()) {
            $query = $this->buildQuery($request);
            return DataTables::of($query)->make(true);
        }
    }

    private function buildQuery(Request $request){
        $consulta = DB::table('paqestudios')
        ->join('relpaqest', 'paqestudios.id', '=', 'relpaqest.IdPaquete')
        ->where('paqestudios.Baja', '=', 0)
        ->groupBy('paqestudios.Id', 'paqestudios.Nombre', 'paqestudios.Descripcion', 'paqestudios.Alias');
        

        if($request->buscar){
            $consulta->where('paqestudios.Id', '=', $request->buscar);
        }

        if($request->alias){
            $consulta->where('paqestudios.Alias', 'LIKE', '%'.$request->alias.'%');
        }

        if($request->id){
            $consulta->where('paqestudios.Id', '=', $request->id);
        }
        
        $consulta->select('paqestudios.Id as Id', 'paqestudios.Nombre as Nombre', 'paqestudios.Descripcion as Descripcion', 'paqestudios.Alias as Alias', DB::raw('COUNT(relpaqest.Id) as CantidadExamenes'));
        return $consulta;

    }

    private function buildQueryDetalleEstudio(Request $request){
         $consulta = DB::table('paqestudios')
         ->join('relpaqest', 'paqestudios.id', '=', 'relpaqest.IdPaquete')
         ->join('estudios', 'relpaqest.IdEstudio', '=', 'estudios.id')
         ->join('examenes', 'relpaqest.IdExamen', '=', 'examenes.id')
         ->join('proveedores', 'examenes.IdProveedor', '=', 'proveedores.Id')
         ->where('paqestudios.Baja', '=', 0);

        if($request->examen){
            $consulta->where('estudios.id', '=', $request->examen);
        }
        if($request->paquete){
             $consulta->where('relpaqest.IdPaquete', '=', $request->paquete);
        }

        if($request->especialidad){
            $consulta->where('examenes.IdProveedor', '=', $request->especialidad);
        }

        $consulta->select('paqestudios.Id as Id','paqestudios.Nombre as Nombre', 'examenes.Nombre as NombreExamen', 'proveedores.Nombre as Especialidad');
        return $consulta;
    }

    //paquetes examenes
    public function crearPaqueteExamen(){
        $codigo = PaqueteEstudio::max('Id') + 1;
        return view('layouts.paquetes.create_paquete_estudios',['Codigo'=>$codigo]);
    }

    public function postPaqueteExamen(Request $request){
        $nombre = $request->nombre;
        $descripcion = $request->descripcion == null ? "" : $request->descripcion ;
        $alias = $request->alias == null ? "" : $request->alias ;
        $estudios = $request->estudios;
        
         if(PaqueteEstudio::where('Nombre', '=', $nombre)->where('Baja', '=', 0)->exists()){
            return response()->json(['error' => 'El nombre del paquete ya existe.'], 400);
        }

        //cargamos el paquete
        $nuevoId = PaqueteEstudio::max('Id') + 1;
        PaqueteEstudio::create([
            'Id' => $nuevoId,
            'Nombre' => $nombre,
            'Descripcion' => $descripcion,
            'Alias' => $alias
        ]);

        // cargamos los estudios de paquete
        foreach($estudios as $estudio){
            $id = RelacionPaqueteEstudio::max('Id') + 1;
            RelacionPaqueteEstudio::create([
                'Id' => $id,
                'IdPaquete' => $nuevoId,
                'IdEstudio' => $estudio['IdEstudio'],
                'IdExamen' => $estudio['Id']
            ]);
        }
        return response()->json(['id' => $nuevoId], 200);
    }

    public function editPaqueteExamen($id){
        $paquete = PaqueteEstudio::find($id);
        return view('layouts.paquetes.edit_paquete_estudios', compact(['paquete']));
    }

    public function postEditPaqueteExamen(Request $request){
        
        $idPaquete = $request->id;
        $nombre = $request->nombre;
        $descripcion = $request->descripcion == null ? "" : $request->descripcion ;
        $alias = $request->alias == null ? "" : $request->alias ;
        $estudios = $request->estudios == null ? [] : $request->estudios;
        $estudiosEliminar = $request->estudiosEliminar == null ? [] : $request->estudiosEliminar;
        
        if(PaqueteEstudio::where('Nombre', '=', $nombre)->where('Baja', '=', 0)->where('Id', '<>', $idPaquete)->exists()){
            return response()->json(['error' => 'El nombre del paquete ya existe (pertenece a otro paquete).'], 400);
        }

        PaqueteEstudio::find($idPaquete)
        ->update(['Nombre'=>$nombre, 'Descripcion'=>$descripcion, 'Alias'=>$alias]);

        //eliminamos los paquetes viejos
        foreach($estudiosEliminar as $idEliminar){
            $estudioEliminar = RelacionPaqueteEstudio::where('IdExamen', '=', $idEliminar)->where('IdPaquete', '=', $idPaquete)->first();
            if($estudioEliminar) $estudioEliminar->update(['Baja'=>1]);
        }

        // cargamos los estudios de paquete
        foreach($estudios as $estudio){
            $id = RelacionPaqueteEstudio::max('Id') + 1;
            RelacionPaqueteEstudio::create([
                'Id' => $id,
                'IdPaquete' => $idPaquete,
                'IdEstudio' => $estudio['IdEstudio'],
                'IdExamen' => $estudio['Id']
            ]);
        }
    }

    public function getPaqueteExamen(Request $request){
        $id = $request->id;
        $paquete = PaqueteEstudio::find($id);
        $estudiosPaquete = RelacionPaqueteEstudio::where('IdPaquete', '=', $id)
        ->where("Baja", "=", 0)
        ->get();
            
        return response()->json(['Paquete'=>$paquete, 'Estudios'=>$estudiosPaquete]);
    }

    public function exportExcel(Request $request){
        $query = $this->buildQuery($request);
        $reporte = $this->reporteExcel->crear('paqueteEstudiosFull');
        return $reporte->generar($query->get());
    }

    public function eliminarPaqueteEstudio(Request $request){
        $id = $request->id;
        if($id){
            PaqueteEstudio::find($id)->update(['Baja'=>1]);
        }
    }

    public function detalleEstudios(){
        return view('layouts.paquetes.detalles_paquete_estudios');
    }

    public function searchDetalleEstudios(Request $request){
          if ($request->ajax()) {
            $query = $this->buildQueryDetalleEstudio($request);
            return DataTables::of($query)->make(true);
        }
    }
    public function exportDetalleExcel(Request $request){
        $query = $this->buildQueryDetalleEstudio($request);
        $reporte = $this->reporteExcel->crear('paqueteEstudiosDetalleFull');
        return $reporte->generar($query->get());
    }

    public function searchPaquetesFacturacion(Request $request){
        if ($request->ajax()) {
            $query = $this->buildQueryPaqueteFacturacion($request);
            return DataTables::of($query)->make(true);
        }
    }

    public function buildQueryPaqueteFacturacion(Request $request){
        $consulta = DB::table('paqfacturacion')
        ->leftJoin('clientesgrupos', 'paqfacturacion.IdGrupo', '=', 'clientesgrupos.Id')
        ->leftJoin('clientes', 'paqfacturacion.IdEmpresa', '=', 'clientes.Id');

        if($request->IdPaquete){
            $consulta->where('paqfacturacion.Id', '=', $request->IdPaquete);
        }

        if($request->IdGrupo){
            $consulta->where('paqfacturacion.IdGrupo', '=', $request->IdGrupo);
        }else if($request->IdEmpresa){
            $consulta->where('paqfacturacion.IdEmpresa', '=', $request->IdEmpresa);
        }
        
        if($request->Codigo){
            $consulta->where('paqfacturacion.Cod','=', $request->Codigo);
        }
        $consulta->where('paqfacturacion.Baja', '=', 0);
        
        $consulta->select('paqfacturacion.Id as Id','paqfacturacion.Cod as Codigo','paqfacturacion.Nombre as Nombre', 'paqfacturacion.CantExamenes as CantExamenes', 'clientesgrupos.Nombre as NombreGrupo', 'clientes.ParaEmpresa as NombreEmpresa');
        
        return $consulta;
    }

    public function getExamenesPaqueteId(Request $request)
    {
        if(empty($request->IdPaquete)){
            return response()->json(['msg' => 'No se pudo obtener el paquete'], 500);
        }

        $examenes = DB::select('CALL getExamenesPaquete(?)', [$request->IdPaquete]);
            
        return response()->json(['examenes' => $examenes], 200);
         
    }

    public function getExamenesPaqueteEstudio(Request $request){
        if(empty($request->IdPaquete)){
            return response()->json(['msg' => 'No se pudo obtener el paquete'], 500);
        }

        $examenes = DB::select('CALL getExamenesPaquete(?)', [$request->IdPaquete]);
            
        return response()->json(['examenes' => $examenes], 200);
        
    }

    public function createPaqueteFacturacion(){
        $codigo = PaqueteFacturacion::max('Id') + 1;
        return view('layouts.paquetes.create_paquete_facturacion',compact(['codigo']));
    }

    public function postPaqueteFacturacionCreate(Request $request){
        $nombre = $request->Nombre;
        $codigo = $request->Codigo;
        $alias = $request->Alias;
        $descripcion = $request->Descripcion;
        $idEmpresa = $request->IdEmpresa;
        $idGrupo = $request->IdGrupo;

        $estudios = $request->Examenes;

        if(PaqueteFacturacion::where('Nombre', '=', $nombre)->where('Baja', '=', 0)->exists()){
            return response()->json(['error' => 'El nombre del paquete ya existe.'], 400);
        }


        $nuevoId = PaqueteFacturacion::max('Id') + 1;

        //realizamos las operaciones de creacion
        PaqueteFacturacion::create([
            'Id' => $nuevoId,
            'Nombre'=> $nombre,
            'Descripcion' => $descripcion != null? $descripcion : "",
            'Alias' => $alias,
            'Cod' => $codigo,
            'IdEmpresa' => ($idEmpresa != null && $idGrupo == null) ? $idEmpresa : 0,
            'IdGrupo'=>  ($idGrupo != null && $idEmpresa == null) ? $idGrupo : 0,
            'CantExamenes' => count($estudios),
            'Baja' => 0
        ]);

        //cargamos las empresa
         // cargamos los estudios de paquete
        if($estudios != null){
            foreach($estudios as $estudio){
                $id = RelacionPaqueteFacturacion::max('Id') + 1;
                RelacionPaqueteFacturacion::create([
                    'Id' => $id,
                    'IdPaquete' => $nuevoId,
                    'IdEstudio' => $estudio['IdEstudio'],
                    'IdExamen' => $estudio['Id']
                ]);
            }
        }

        return response()->json(['id' => $nuevoId], 200);
    }

    public function editPaqueteFacturacion($id){
        $paquete = PaqueteFacturacion::find($id);
        $grupo = $paquete->IdGrupo;
        $empresa = $paquete->IdEmpresa;

        return view('layouts.paquetes.edit_paquete_facturacion', compact(['paquete', 'grupo', 'empresa']));
    }

    public function getCliente(Request $request){
        $cliente = Cliente::where('id', '=', $request->id)->first();
        return response()->json($cliente);
    }

     public function getGrupo(Request $request){
        $grupo = GrupoClientes::where('id', '=', $request->id)->first();
        return response()->json($grupo);
    }


    public function getEstudiosPaqueteEstudio(Request $request){
        $paqueteRelacion = RelacionPaqueteEstudio::where('idPaquete', '=', $request->id)->where('baja', '=', 0)->get();
        $estudios = [];

        foreach($paqueteRelacion as $r){
            $estudio = Examen::find($r->IdExamen);
            array_push($estudios, $estudio);
        }

        return response()->json($estudios);
    }

    public function getEstudiosPaqueteFacturacion(Request $request){
        $paqueteRelacion = RelacionPaqueteFacturacion::where('idPaquete', '=', $request->id)->where('baja', '=', 0)->get();
        
        $estudios = [];
        foreach($paqueteRelacion as $r){
            $estudio = Examen::find($r->IdExamen);
            array_push($estudios, $estudio);
        }

        return response()->json($estudios);
    }

    
    public function postEditPaqueteFactutacion(Request $request){
        
        $idPaquete = $request->id;
        $nombre = $request->nombre;
        $descripcion = $request->descripcion == null ? "" : $request->descripcion ;
        $alias = $request->alias == null ? "" : $request->alias ;
        $codigo = $request->codigo == null ? "" : $request->codigo;
        $idEmpresa = $request->IdEmpresa;
        $idGrupo = $request->IdGrupo;
        
        $estudios = $request->estudios == null ? [] : $request->estudios;
        $estudiosEliminar = $request->estudiosEliminar == null ? [] : $request->estudiosEliminar;
        
        if(PaqueteFacturacion::where('Nombre', '=', $nombre)->where('Baja', '=', 0)->where('Id', '<>', $idPaquete)->exists()){
            return response()->json(['error' => 'El nombre del paquete ya existe (pertenece a otro paquete).'], 400);
        }

        PaqueteFacturacion::find($idPaquete)
        ->update(['Nombre'=>$nombre, 'Descripcion'=>$descripcion, 'Alias'=>$alias, 'Cod'=>$codigo]);

        if($idEmpresa != null && $idGrupo == null){
            PaqueteFacturacion::find($idPaquete)
            ->update(['IdEmpresa'=>$idEmpresa, 'IdGrupo'=>0]);
        }else if($idGrupo != null && $idEmpresa == null){
            PaqueteFacturacion::find($idPaquete)
            ->update(['IdEmpresa'=>0, 'IdGrupo'=>$idGrupo]);
        }else{
            PaqueteFacturacion::find($idPaquete)
            ->update(['IdEmpresa'=>$idEmpresa, 'IdGrupo'=>0, 'IdEmpresa'=>0]);
        }

        //eliminamos los paquetes viejos
        foreach($estudiosEliminar as $idEliminar){
            $estudioEliminar = RelacionPaqueteFacturacion::where('IdExamen', '=', $idEliminar)->where('IdPaquete', '=', $idPaquete)->first();
            if($estudioEliminar) $estudioEliminar->update(['Baja'=>1]);
        }

        // cargamos los estudios de paquete
        if($estudios != null){
            foreach($estudios as $estudio){
                $id = RelacionPaqueteFacturacion::max('Id') + 1;
                RelacionPaqueteFacturacion::create([
                    'Id' => $id,
                    'IdPaquete' => $idPaquete,
                    'IdEstudio' => $estudio['IdEstudio'],
                    'IdExamen' => $estudio['Id']
                ]);
            }
        }
    }

    public function getPaqueteFacturacion(Request $request){
        $id = $request->id;
        $paquete = PaqueteFacturacion::find($id);
        $estudiosPaquete = RelacionPaqueteFacturacion::where('IdPaquete', '=', $id)
        ->where("Baja", "=", 0)
        ->get();
            
        return response()->json(['Paquete'=>$paquete, 'Estudios'=>$estudiosPaquete]);
    }

    public function detallesFacturacion(){
        return view('layouts.paquetes.detalles_paquete_facturacion');
    }

    public function searchDetalleFacturacion(Request $request){
        if ($request->ajax()) {
            $query = $this->buildQueryDetalleFacturacion($request);
            return DataTables::of($query)->make(true);
        }
    }

    private function buildQueryDetalleFacturacion(Request $request){
         $consulta = DB::table('paqfacturacion')
         ->join('relpaqfact', 'paqfacturacion.id', '=', 'relpaqfact.IdPaquete')
         ->join('estudios', 'relpaqfact.IdEstudio', '=', 'estudios.id')
         ->join('examenes', 'relpaqfact.IdExamen', '=', 'examenes.id')
         ->join('proveedores', 'examenes.IdProveedor', '=', 'proveedores.Id')
         ->leftJoin('clientes', 'clientes.Id', '=', 'paqfacturacion.IdEmpresa')
         ->leftJoin('clientesgrupos', 'clientesgrupos.Id', '=', 'paqfacturacion.IdGrupo')
         ->where('paqfacturacion.Baja', '=', 0);

        if($request->examen){
            $consulta->where('estudios.id', '=', $request->examen);
        }
        if($request->paquete){
             $consulta->where('relpaqfact.IdPaquete', '=', $request->paquete);
        }

        if($request->empresa){
            $consulta->where('clientes.Id', '=', $request->empresa);
        }

        if($request->grupo){
            $consulta->where('clientesgrupos.Id', '=', $request->grupo);
        }

        $consulta->select('paqfacturacion.Id as Id','paqfacturacion.Nombre as Nombre', 'examenes.Nombre as NombreExamen', 'proveedores.Nombre as Especialidad', 'clientes.ParaEmpresa as Empresa', 'clientesgrupos.Nombre as Grupo');
        return $consulta;
    }

    public function exportDetalleFacturacionExcel(Request $request){
        $query = $this->buildQueryDetalleFacturacion($request);
        $reporte = $this->reporteExcel->crear('paqueteFacturacionDetalle');
        return $reporte->generar($query->get());
    }

    public function eliminarPaqueteFacturacion(Request $request){
        $id = $request->id;
        if($id){
            PaqueteFacturacion::find($id)->update(['Baja'=>1]);
        }
    }

}
