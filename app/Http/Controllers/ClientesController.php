<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Provincia;
use App\Models\Localidad;
use App\Models\Prestacion;
use App\Traits\ObserverClientes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Yajra\DataTables\DataTables;
use App\Traits\CheckPermission;
use App\Services\ReportesExcel\ReporteExcel;
use PhpOffice\PhpSpreadsheet\Calculation\Logical\Boolean;
use App\Models\Auditor;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ClientesController extends Controller
{
    use ObserverClientes, CheckPermission;

    protected $reporteExcel;

    public $helper = '
    <ul>
        <li>Los <b class="negrita_verde">EMail Masivos</b> se usan en Clientes. Enviar</li>
        <li>Los <b class="negrita_verde">EMail Informes</b> se usan en Prestaciones</li>
        <li>Los <b class="negrita_verde">EMail Factura</b> se usan en Facturas. Enviar</li>
        <li>Los <b class="negrita_verde">Email Solo Anexo</b> se usan reporte eAnexos</li>
    </ul>
    ';

    public function __construct(ReporteExcel $reporteExcel)
    {
        $this->reporteExcel = $reporteExcel;
    }

    public function index(Request $request)
    {
        if (!$this->hasPermission("clientes_show")) {abort(403);}

        if ($request->ajax()) {

            $query = Cliente::select(
                'Identificacion',
                'RazonSocial',
                'ParaEmpresa',
                'TipoCliente',
                'Bloqueado',
                'Id',
                'FPago')
                ->orderBy('Id', 'DESC')
                ->where('Estado', '1');

            return Datatables::of($query)->make(true);

        }

        return view('layouts.clientes.index');
    }


    public function search(Request $request)
    {
        if (!$this->hasPermission("clientes_show")) {
            abort(403);
        }

        if ($request->ajax()) {
            $filtro = $request->filtro;
            $buscar = trim($request->buscar);
            $fpago = $request->formaPago;

            // Filtrar solo registros con Estado = 0
            $query = Cliente::select(
                'Identificacion',
                'RazonSocial',
                'ParaEmpresa',
                'TipoCliente',
                'Bloqueado',
                'Id',
                'FPago',
                'Estado'
            )->where('Estado', 1); 

            $query->when(!empty($filtro) && is_array($filtro), function ($query) use ($filtro) {
                if (in_array('bloqueados', $filtro)) {
                    $query->where('Bloqueado', 1);
                }

                if (in_array('sinMailFact', $filtro)) {
                    $query->where('EmailFactura', '');
                }

                if (in_array('entregaDomicilio', $filtro)) {
                    $opciones = [5, 4, 3, 2, 1];
                    $intersectedOptions = array_intersect($opciones, $filtro);

                    if (!empty($intersectedOptions)) {
                        $query->whereIn('Entrega', $intersectedOptions);
                    }
                }

                if (in_array('sinMailInfor', $filtro)) {
                    $query->where('EMailInformes', 1);
                }

                if (in_array('sinMailResultados', $filtro)) {
                    $query->where('EMailResultados', 1);
                }

                if (in_array('retiraFisico', $filtro)) {
                    $query->where('RF', 1);
                }

                if (in_array('factSinPaquetes', $filtro)) {
                    $query->where('SinPF', 1);
                }

                if (in_array('sinEval', $filtro)) {
                    $query->where('SinEval', 1);
                }
            });

            $query->when(!empty($fpago) && $fpago !== 'A', function ($query) use ($fpago) {
                $query->where('FPago', $fpago);
            });

            $query->when(!empty($fpago) && $fpago === 'A', function ($query) {
                $query->whereIn('FPago', [null, '', 'A']);
            });

            $query->when(!empty($buscar) && strlen($buscar) >= 3, function ($query) use ($buscar) {
                $formatearIdent = $this->formatearIdentificacion($buscar);

                $query->where(function ($query) use ($buscar, $formatearIdent) {
                    $query->where('ParaEmpresa', 'LIKE', '%' . $buscar . '%')
                        ->orWhere('Identificacion', 'LIKE', '%' . $formatearIdent . '%')
                        ->orWhere('RazonSocial', 'LIKE', '%' . $buscar . '%');
                });
            });

            $result = $query->orderBy('Id', 'DESC');

            return Datatables::of($result)->make(true);
        }

        return view('layouts.clientes.index');
    }

    public function create()
    {
        if (!$this->hasPermission("clientes_add")) {abort(403);}
        return view('layouts.clientes.create', ['helper' => $this->helper])->with('provincias', Provincia::all());
    }


    public function store(Request $request)
    {
        if (!$this->hasPermission("clientes_edit")) {
            return response()->json(['msg' => 'No tiene permisos'], 403);
        }
        
        $cliente = Cliente::create($request->all());
        $this->setTelefono($cliente->Id, $request->telefonos);

        Auditor::setAuditoria($cliente->Id, 3, 44, Auth::user()->name);

        return redirect()->route('clientes.edit', ['cliente' => $cliente->Id]);
    }

    public function edit(Cliente $cliente)
    {
        if (!$this->hasPermission("clientes_edit")) {abort(403);}
        
        $provincias = Provincia::all();            
        $detailsLocalidad = Localidad::where('Id', $cliente->IdLocalidad)->first(['Nombre', 'CP', 'Id']);
        $paraEmpresas = Cliente::where('Identificacion', $cliente->Identificacion)->get();

        return view('layouts.clientes.edit', compact(['cliente', 'provincias', 'detailsLocalidad', 'paraEmpresas']), ['helper' => $this->helper]);
    }

    public function update(Request $request, Cliente $cliente)
    {
        if (!$this->hasPermission("clientes_edit")) {
            return response()->json(['msg' => 'No tiene permisos'], 403);
        }

        $cliente = Cliente::find($request->Id);
        if(empty($cliente)) return;

        $cliente->fill($request->all())->save();
        $this->setTelefono($request->Id, $request->telefonos);

         Auditor::setAuditoria($request->Id, 3, 2, Auth::user()->name, "Cambios en los datos basicos");

        return back();

    }

    public function baja(Request $request): mixed
    {
        if (!$this->hasPermission("clientes_delete")) {
            return response()->json(['msg' => 'No tiene permisos'], 403);
        }

        $ids = (array) $request->input('ids');

        if(empty($ids)) {
            response()->json(['msg' => 'No hay clientes para dar de baja'], 409);
        }

        Cliente::whereIn('id', $ids)->update(['Estado' => 0]);

        foreach($ids as $id){
            Auditor::setAuditoria($id, 3, 3, Auth::user()->name);
        }

        return response()->json(['msg' => 'Se ha dado de baja correctamente'], 200);
    }


    public function block(Request $request)
    {
        if (!$this->hasPermission("clientes_edit")) {
            return response()->json(['msg' => 'No tiene permisos'], 403);
        }

        if(empty($request->cliente)) {
            response()->json(['msg' => 'No hay clientes para bloquear'], 409);
        }

        Cliente::find($request->cliente)->update(
            ['Motivo' => $request->motivo, 
            'Bloqueado' => '1']
        );

        Auditor::setAuditoria($request->cliente, 3, 12, Auth::user()->name, "Cliente bloqueado");
        
        return response()->json(['msg' => 'El bloqueo se ha realizado de manera correcta'], 200);
    }

    public function verificarCuit(Request $request): mixed
    {
        if (!$this->hasPermission("clientes_edit")) {
            return response()->json(['msg' => 'No tiene permisos'], 403);
        }

        $query = Cliente::where('Identificacion', $request->Identificacion);

        if(!empty($request->ParaEmpresa)) {
            $query->where('ParaEmpresa', $request->ParaEmpresa);
        }

        $cliente = $query->first();

        return response()->json($cliente);
    }

    public function setObservaciones(Request $request)
    {
        if (!$this->hasPermission("clientes_edit")) {
            return response()->json(['msg' => 'No tiene permisos'], 403);
        }

        $cliente = Cliente::find($request->Id);
       
        if($cliente)
        {
            $cliente->fill($request->all());
            $cliente->save();

            Auditor::setAuditoria($request->Id, 3, 1, Auth::user()->name, "Se agrega una observacion.");
        }
    }

    public function checkEmail(Request $request): mixed
    {   
        if (!$this->hasPermission("clientes_edit")) {
            return response()->json(['msg' => 'No tiene permisos'], 403);
        }

        $cliente = Cliente::find($request->Id);
        if($cliente)
        {
            $cliente->fill([
                'EMailResultados' => $request->resultados ?? '',
                'EMailInformes' => $request->informes ?? '',
                'EMailFactura' => $request->facturas ?? '',
                'EMailAnexo' => $request->anexo ?? ''
            ]);
            $cliente->SEMail = ($request->sinEnvio === 'true') ? 1 : 0;
            $cliente->save();

             Auditor::setAuditoria($request->Id, 3, 1, Auth::user()->name, "Se realizan cambios en emails.");
        }

        return response()->json(['msg' => '¡Se han registrado los cambios correctamente!'], 200);
    }

    public function checkOpciones(Request $request)
    {
        if (!$this->hasPermission("clientes_edit")) {
            return response()->json(['msg' => 'No tiene permisos'], 403);
        }
        
        $cliente = Cliente::find($request->Id);

        if($cliente)
        {
            $cliente->fill([
                'RF' => $request->fisico,
                'SinEval' => $request->sinEvaluacion,
                'SinPF' => $request->facturacionSinPaq,
                'Bloqueado' => $request->bloqueado,
                'Anexo' => $request->anexo,
                'Motivo' => $request->motivo
            ]);
            $cliente->Entrega = ($request->mensajeria === 'true' ? 2 : ($request->correo === 'true' ? 4 : 0)); 
            $cliente->save();

            Auditor::setAuditoria($request->Id, 3, 2, Auth::user()->name, "Se realizan cambios en las opciones");
        }
    }

    //Exportar clientes
    public function excel(Request $request)
    {
        if (!$this->hasPermission("clientes_export")) {
            return response()->json(['msg' => 'No tiene permisos'], 403);
        }

        $ids = (array) $request->input('Id');
        $clientes = Cliente::with(['localidad'])->with(['actividad'])->whereIn('Id', $ids)->get();

        if(empty($clientes)) {
            return response()->json(['msg' => 'Error al generar el reporte'], 409);
        }

        $reporte = $this->reporteExcel->crear('clientes');
        return $reporte->generar($clientes);
    }

    public function checkParaEmpresa(Request $request)
    {
        if (!$this->hasPermission("clientes_edit")) {
            return response()->json(['msg' => 'No tiene permisos'], 403);
        }

        $cliente = Cliente::find($request->empresa);

        if($cliente)
        {
            return response()->json(['cliente' => $cliente]);
        }

        return response()->json(['msg' => 'No se ha encontrado la Para Empresa'], 404);
    }

    public function getBloqueo(Request $request)
    {   
        if (!$this->hasPermission("clientes_edit")) {
            return response()->json(['msg' => 'No tiene permisos'], 403);
        }

        $cliente = Cliente::where('Id', $request->Id)->first(['Bloqueado', 'Motivo']);
        
        if($cliente->Bloqueado === 1)
        {
            return response()->json(['cliente' => $cliente]);
        }
    }

    public function getClientes(Request $request)
    {
        $buscar = $request->buscar;
        $tipo = $request->tipo;

        $resultados = [];

        $resultados = $clientes = Cliente::where(function ($query) use ($buscar, $tipo) {
            
            if($tipo === 'E'){
                $query->where('RazonSocial', 'LIKE', '%'.$buscar.'%')
                    ->orWhere('NombreFantasia', 'LIKE', '%'.$buscar.'%')
                    ->orWhere('ParaEmpresa', 'LIKE', '%'.$buscar.'%');

            }elseif($tipo === 'A'){
                $query->where('RazonSocial', 'LIKE', '%'.$buscar.'%');
            } 
        })
        ->where('TipoCliente', $tipo)
        ->where('Estado', 1)
        ->get();

        foreach ($clientes as $cliente) {
                if($tipo === 'E')
                {
                    $text = 'Empresa: '.$cliente->RazonSocial.' | Alias: '.$cliente->Alias. ' | ParaEmpresa: '.$cliente->ParaEmpresa;
                }elseif($tipo === 'A'){
                    $text = 'ART: '.$cliente->RazonSocial;
                }
                

            $resultados[] = [
                'id' => $cliente->Id,
                'text' => $text,
            ];
        }

        return response()->json(['clientes' => $resultados]);
    }

    public function cambioEstado(Request $request)
    {
        return response()->json($this->checkPrestaciones($request->Id));
    }

    public function formaPago(Request $request)
    {
        $cliente = Cliente::where('Id', $request->Id)->first(['FPago']);
        return response()->json($cliente);
    }

    public function getLocalidad(Request $request)
    {
        $localidad = Localidad::where('Id', $request->Id)->first(['Nombre', 'CP']);
        return response()->json(['localidad' => $localidad]);
    }

    public function getAuditorias(Request $request)
    {
        if($request->ajax()) {

            $query = Auditor::join('auditoriatablas', 'auditoria.IdTabla', '=', 'auditoriatablas.Id')
                ->join('auditoriaacciones', 'auditoria.IdAccion', '=', 'auditoriaacciones.Id')
                ->select(
                    DB::raw('DATE_FORMAT(auditoria.Fecha, "%d/%m/%Y") as fecha'),
                    'auditoria.IdUsuario as usuario',
                    'auditoriaacciones.Nombre as accion',
                    'auditoria.Observaciones as observacion'
                )->where('auditoria.IdRegistro', $request->Id);

            $query->when(!empty($request->usuario), function($query) use ($request) {
                $query->where('auditoria.IdUsuario', $request->usuario);
            });

            $query->when(!empty($request->fecha), function ($query) use ($request) {
                $query->whereDate('auditoria.Fecha', $request->fecha);
            });
         

            $result = $query->orderBy('auditoria.Fecha', 'DESC');

            return Datatables::of($result)->make(true);
        }
    }

    public function adminBloqueos()
    {
        return view('layouts.clientes.bloqueos');
    }

    public function listadoBloqueados(Request $request)
    {
        if($request->ajax()) {

            $query = Cliente::select(
                'RazonSocial as empresa',
                'Identificacion as cuit',
                'ParaEmpresa as paraEmpresa',
                'NombreFantasia as alias',
                'Bloqueado as bloqueado',
                'Id as id'
            )->where('Estado', 0)
            ->whereNot('Id', 0);

            return Datatables::of($query)->make(true);
        }
    }

    public function restaurarEliminado(Request $request)
    {
        $query = Cliente::find($request->Id);

        if($query) {
            $query->update(['Estado' => 1]);
            return response()->json(['msg' => 'Se ha restaudado al cliente correctamente', 'status' => 'success'], 200);
        }
    }

    private function formatearIdentificacion($identificacion)
    {
        if (strpos($identificacion, '-') !== false) {
            return $identificacion;
        }

        if (strlen($identificacion) < 3) {
            return $identificacion;
        }

        $parte1 = substr($identificacion, 0, 2);
        $parte2 = str_pad(substr($identificacion, 2, 8), 8, '0', STR_PAD_RIGHT);
        $parte3 = substr($identificacion, -1);

        return "{$parte1}-{$parte2}-{$parte3}";
    }

    private function checkPrestaciones(int $idCliente)
    {
        return Prestacion::where('IdEmpresa', $idCliente)->orWhere('IdART', $idCliente)->exists();
    }
}
