<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Provincia;
use App\Models\Localidad;
use App\Traits\ObserverClientes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Yajra\DataTables\DataTables;
use App\Traits\CheckPermission;
use App\Services\ReportesExcel\ReporteExcel;

class ClientesController extends Controller
{
    use ObserverClientes, CheckPermission;

    protected $reporteExcel;

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

        if (!$this->hasPermission("clientes_show")) {abort(403);}

        if ($request->ajax()) {

            $tipo = trim($request->tipo);
            $filtro = $request->filtro;
            $buscar = trim($request->buscar);
            $fpago = $request->formaPago;

            $query = Cliente::select(
                'Identificacion',
                'RazonSocial',
                'ParaEmpresa',
                'TipoCliente',
                'Bloqueado',
                'Id',
                'FPago');

            $query->when(!empty($filtro) && (is_array($filtro) && in_array('bloqueados', $filtro)), function ($query) {
                $query->where('Bloqueado', 1);
            });

            $query->when(!empty($fpago) && $fpago !== 'A', function ($query) use ($fpago) {
                $query->where('FPago', $fpago);
            });

            $query->when(!empty($fpago) && $fpago === 'A', function ($query) {
                $query->whereIn('FPago', [null, '', 'A']);
            });

            $query->when(!empty($request->tipo), function ($query) use ($request) {
                $query->where('TipoCliente', $request->tipo);
            });

            $query->when(is_array($filtro) && in_array('sinMailFact', $filtro), function ($query) {
                $query->where('EmailFactura', '');
            });

            $query->when(!empty($filtro) && is_array($filtro) && in_array('entregaDomicilio', $filtro), function ($query) use ($filtro) {
                $opciones = [5, 4, 3, 2, 1];
                $intersectedOptions = array_intersect($opciones, $filtro);

                if (! empty($intersectedOptions)) {
                    $query->whereIn('Entrega', $intersectedOptions);
                }
            });

            $query->when(!empty($filtro) && is_array($filtro) && in_array('sinMailInfor', $filtro), function ($query) {
                $query->where('EMailInformes', 1);
            });

            $query->when(!empty($filtro) && is_array($filtro) && in_array('sinMailResultados', $filtro), function ($query) {
                $query->where('EMailResultados', 1);
            });

            $query->when(!empty($filtro) && is_array($filtro) && in_array('retiraFisico', $filtro), function ($query) {
                $query->where('RF', 1);
            });

            $query->when(!empty($filtro) && is_array($filtro) && in_array('factSinPaquetes', $filtro), function ($query) {
                $query->where('SinPF', 1);
            });

            $query->when(!empty($filtro) && is_array($filtro) && in_array('sinEval', $filtro), function ($query) {
                $query->where('SinEval', 1);
            });

            $query->when(!empty($buscar), function ($query) use ($buscar) {
                $query->where('ParaEmpresa', 'LIKE', '%'.$buscar.'%')
                    ->orWhere(function ($query) use ($buscar) {
                        $formatearIdent = $buscar;
                        if (!strpos($buscar, '-')) {
                            if (strlen($buscar) === 11) {
                                $formatearIdent = substr($buscar, 0, 2).'-'.substr($buscar, 2, 8).'-'.substr($buscar, -1);
                            } elseif (strlen($buscar) >= 3 && strlen($buscar) <= 10) {

                                $partes = explode('-', $buscar);
                                if (count($partes) === 1) {

                                    $formatearIdent = substr($buscar, 0, 2).'-'.str_pad(substr($buscar, 2), 8, '0', STR_PAD_RIGHT);
                                } else {

                                    $formatearIdent = preg_replace('/(\d{2})(\d{1,8})(\d)?/', '$1-$2-$3', $buscar);
                                }
                            } else {

                                $formatearIdent = substr($buscar, 0, 2).'-'.str_pad(substr($buscar, 2), 8, '0', STR_PAD_LEFT);
                            }
                        } else {
                            $partes = explode('-', $buscar);

                            if (count($partes) === 3 && strlen($partes[0]) === 2 && strlen($partes[2]) === 1) {
                                if (strlen($partes[1]) < 8) {

                                    $partes[1] = str_pad($partes[1], 8, '0', STR_PAD_LEFT);
                                }
                                $formatearIdent = implode('-', $partes);
                            }
                        }

                        $query->where('Identificacion', 'LIKE', '%'.$formatearIdent.'%');
                    })
                    ->orWhere('RazonSocial', 'LIKE', '%'.$buscar.'%')
                    ->orWhere(function ($query) use ($buscar) {
                        $query->where('ParaEmpresa', 'LIKE', '%'.$buscar.'%')
                            ->where('RazonSocial', 'LIKE', '%'.$buscar.'%');
                    });
            });

            $result = $query->where('Estado', 1)->orderBy('Id', 'DESC');

            return Datatables::of($result)->make(true);
        }
        return view('layouts.clientes.index');
    }

    public function create()
    {
        if (!$this->hasPermission("clientes_add")) {abort(403);}
        return view('layouts.clientes.create')->with('provincias', Provincia::all());
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

        return view('layouts.clientes.edit', compact(['cliente', 'provincias', 'detailsLocalidad', 'paraEmpresas']));
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

        $ids = (array) $request->input('ids');

        Cliente::whereIn('id', $ids)->update(['Estado' => 0]);
        return response()->json(['msg' => 'Se ha dado de baja correctamente'], 200);
    }

    public function baja(Request $request)
    {
        if (!$this->hasPermission("clientes_delete")) {
            return response()->json(['msg' => 'No tiene permisos'], 403);
        }

        $cliente = Cliente::find($request->Id);
        
        if($cliente)
        {
            $cliente->update(['Estado' => 0]);
            return response()->json(['msg' => 'Se ha dado de baja correctamente'], 200);
        }

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

        $ids = (array) $request->input('Id');

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
}
