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

        $nuevoId = Cliente::max('Id') + 1;

        Cliente::create([
            'Id' => $nuevoId,
            'TipoCliente' => $request->TipoCliente,
            'Identificacion' => $request->Identificacion,
            'ParaEmpresa' => $request->ParaEmpresa,
            'RazonSocial' => $request->RazonSocial,
            'CondicionIva' => $request->CondicionIva,
            'NombreFantasia' => $request->NombreFantasia,
            'EMail' => $request->EMail ?? '',
            'Telefono' => $request->Telefono,
            'ObsEMail' => $request->ObsEMail ?? '',
            'Direccion' => $request->Direccion ?? '',
            'IdLocalidad' => $request->IdLocalidad,
            'Provincia' => $request->Provincia,
            'CP' => $request->CP,
            'Bloqueado' => '0',
            'FPago' => $request->FPago
        ]);
        
        $this->setTelefono($nuevoId, $request->telefonos);

        return redirect()->route('clientes.edit', ['cliente' => $nuevoId]);

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
        if($cliente)
        {
            $cliente->fill($request->all())->save();
            $this->setTelefono($request->Id, $request->telefonos);
        }

        return back();

    }

    public function multipleDown(Request $request): mixed
    {

        if (!$this->hasPermission("clientes_delete")) {
            return response()->json(['msg' => 'No tiene permisos'], 403);
        }

        $ids = $request->input('ids');
        if (! is_array($ids)) {
            $ids = [$ids];
        }

        $messages = [];

        foreach($ids as $id)
        {
            $cliente = Cliente::find($id);

            if($cliente)
            {
                if($this->checkPrestaciones($id))
                {
                    $messages [] = ['msg' => 'No se puede dar de baja al cliente '. $cliente->RazonSocial .' porque posee prestaciones asociadas', 'estado' => 'warning'];
                }else {

                    $cliente->Estado = 0;
                    $cliente->save();

                    $messages [] = ['msg' => 'Se dado de baja al cliente '. $cliente->RazonSocial .' correctamente', 'estado' => 'success'];
                }  
            }
        }

        return response()->json($messages);
    }

    public function block(Request $request)
    {
        if (!$this->hasPermission("clientes_edit")) {
            return response()->json(['msg' => 'No tiene permisos'], 403);
        }

        $cliente = Cliente::find($request->cliente);
        if($cliente)
        {
            $cliente->update(['Motivo' => $request->motivo, 'Bloqueado' => '1']);
            return response()->json(['msg' => 'El bloqueo se ha realizado de manera correcta'], 200);
        }

    }

    public function verifyIdentificacion(Request $request): mixed
    {
        if (!$this->hasPermission("clientes_edit")) {
            return response()->json(['msg' => 'No tiene permisos'], 403);
        }

        $cliente = Cliente::where('Identificacion', $request->Identificacion)->first();
        $existe = $cliente !== null;

        return response()->json(['existe' => $existe, 'cliente' => $cliente]);
    }

    public function verifyCuitEmpresa(Request $request): mixed
    {
        if (!$this->hasPermission("clientes_edit")) {
            return response()->json(['msg' => 'No tiene permisos'], 403);
        }

        $cliente = Cliente::where('Identificacion', $request->Identificacion)
            ->where('ParaEmpresa', $request->ParaEmpresa)
            ->first();
        $existe = $cliente !== null;

        return response()->json(['existe' => $existe, 'cliente' => $cliente]);
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
        }
    }

    //Exportar clientes
    public function excel(Request $request)
    {
        if (!$this->hasPermission("clientes_export")) {
            return response()->json(['msg' => 'No tiene permisos'], 403);
        }

        $ids = $request->input('Id');
        if (! is_array($ids)) {
            $ids = [$ids];
        }

        $clientes = Cliente::with(['localidad'])->whereIn('Id', $ids)->get();

        if($clientes) {
            $reporte = $this->reporteExcel->crear('clientes');
            return $reporte->generar($clientes);

        }else{
            return response()->json(['msg' => 'Error al generar el reporte'], 409);
        }
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

        $resultados = Cache::remember('clientes_'.$buscar, 5, function () use ($buscar, $tipo) {
            $clientes = Cliente::where(function ($query) use ($buscar, $tipo) {
                if($tipo === 'E')
                {
                    $query->where('RazonSocial', 'LIKE', '%'.$buscar.'%')
                        ->orWhere('NombreFantasia', 'LIKE', '%'.$buscar.'%')
                        ->orWhere('ParaEmpresa', 'LIKE', '%'.$buscar.'%');

                }elseif($tipo === 'A'){
                    $query->where('RazonSocial', 'LIKE', '%'.$buscar.'%');
                }
                
            })
                ->where('TipoCliente', $tipo)
                ->get();

            $resultados = [];

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

            return $resultados;
        });

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
