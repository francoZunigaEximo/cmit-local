<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Provincia;
use App\Models\Localidad;
use App\Traits\ObserverClientes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Auth;

class ClientesController extends Controller
{

    use ObserverClientes;

    public function index(Request $request)
    {
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
                'FPago')
                ->orderBy('Id', 'DESC')
                ->where('Estado', '1');

            $query->when($buscar, function ($query) use ($buscar) {
                $query->where(function ($query) use ($buscar) {
                    $query->where('ParaEmpresa', 'LIKE', '%'.$buscar.'%')
                        ->orWhere(function ($query) use ($buscar) {
                            $formatearIdent = $buscar;
                            if (! strpos($buscar, '-')) {
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
            });

            $query->when($tipo, function ($query) use ($tipo) {
                $query->where('TipoCliente', '=', $tipo);
            });

            $query->when($fpago, function ($query) use ($fpago) {
                $query->where('FPago', '=', $fpago);
            });

            $query->when(is_array($filtro) && in_array('bloqueados', $filtro), function ($query) {
                $query->where('Bloqueado', '=', '1');
            });

            $query->when(is_array($filtro) && in_array('sinMailFact', $filtro), function ($query) {
                $query->where('EmailFactura', '=', '');
            });

            $query->when(is_array($filtro) && in_array('entregaDomicilio', $filtro), function ($query) use ($filtro) {
                $opciones = [5, 4, 3, 2, 1];
                $intersectedOptions = array_intersect($opciones, $filtro);

                if (! empty($intersectedOptions)) {
                    $query->whereIn('Entrega', $intersectedOptions);
                }
            });

            $query->when(is_array($filtro) && in_array('sinMailInfor', $filtro), function ($query) {
                $query->where('EMailInformes', '=', '1');
            });

            $query->when(is_array($filtro) && in_array('sinMailResultados', $filtro), function ($query) {
                $query->where('EMailResultados', '=', '1');
            });

            $query->when(is_array($filtro) && in_array('retiraFisico', $filtro), function ($query) {
                $query->where('RF', '=', '1');
            });

            $query->when(is_array($filtro) && in_array('factSinPaquetes', $filtro), function ($query) {
                $query->where('SinPF', '=', '1');
            });

            $query->when(is_array($filtro) && in_array('sinEval', $filtro), function ($query) {
                $query->where('SinEval', '=', '1');
            });

            return Datatables::of($query)->make(true);

        }

        return view('layouts.clientes.index');
    }

    public function create()
    {
        if (!Auth::user()->role->permiso->can('clientes_add')){
            abort(403);
        }

        return view('layouts.clientes.create')->with('provincias', Provincia::all());
    }


    public function store(Request $request)
    {
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
        ]);
        
        $this->setTelefono($nuevoId, $request->telefonos);

        return redirect()->route('clientes.edit', ['cliente' => $nuevoId]);

    }

    public function edit(Cliente $cliente)
    {
        $hasPermission = false;

        foreach (Auth::user()->role as $rol) {
            if ($rol->permiso->contains('slug', 'clientes_edit')) {
                $hasPermission = true;
                break;
            }
        }

        if (!$hasPermission) {
            abort(403);
        }
        
        $provincias = Provincia::all();            
        $detailsLocalidad = Localidad::where('Id', $cliente->IdLocalidad)->first(['Nombre', 'CP', 'Id']);
        $paraEmpresas = Cliente::where('Identificacion', $cliente->Identificacion)->get();

        return view('layouts.clientes.edit', compact(['cliente', 'provincias', 'detailsLocalidad', 'paraEmpresas']));
    }

    public function update(Request $request, Cliente $cliente)
    {
        $cliente = Cliente::find($request->Id);
        if($cliente)
        {
            $cliente->fill($request->all())->save();
            $this->setTelefono($request->Id, $request->telefonos);
        }

        return back();

    }

    public function multipleDown(Request $request): void
    {
        $ids = $request->input('ids');
        if (! is_array($ids)) {
            $ids = [$ids];
        }

        Cliente::whereIn('id', $ids)->update(['Estado' => 0]);
    }

    public function baja(Request $request): void
    {
        $cliente = Cliente::find($request->Id);
        
        if($cliente)
        {
            $cliente->update(['Estado' => 0]);
        }

    }

    public function block(Request $request): void
    {
        $cliente = Cliente::find($request->cliente);
        if($cliente)
        {
            $cliente->update(['Motivo' => $request->motivo, 'Bloqueado' => '1']);
        }

    }

    public function verifyIdentificacion(Request $request)
    {
        $cliente = Cliente::where('Identificacion', $request->Identificacion)->first();
        $existe = $cliente !== null;

        return response()->json(['existe' => $existe, 'cliente' => $cliente]);
    }

    public function verifyCuitEmpresa(Request $request)
    {
        $cliente = Cliente::where('Identificacion', $request->Identificacion)
            ->where('ParaEmpresa', $request->ParaEmpresa)
            ->first();
        $existe = $cliente !== null;

        return response()->json(['existe' => $existe, 'cliente' => $cliente]);
    }

    public function setObservaciones(Request $request): void
    {
        $cliente = Cliente::find($request->Id);
       
        if($cliente)
        {
            $cliente->fill($request->all());
            $cliente->save();
        }
    }

    public function checkEmail(Request $request):void
    {
        $cliente = Cliente::find($request->Id);
        if($cliente)
        {
            $cliente->fill([
                'EMailResultados' => $request->resultados,
                'EMailInformes' => $request->informes,
                'EMailFactura' => $request->facturas,
                'EMailAnexo' => $request->anexo
            ]);
            $cliente->SEMail = ($request->sinEnvio === 'true') ? 1 : 0;
            $cliente->save();
        }
    }

    public function checkOpciones(Request $request)
    {
        
        $cliente = Cliente::find($request->Id);

        if($cliente)
        {
            $cliente->fill([
                'RF' => $request->fisico,
                'SinEval' => $request->sinEvaluacion,
                'SinPF' => $request->facturacionSinPaq,
                'Bloqueado' => $request->bloqueado,
                'Anexo' => $request->anexo,
            ]);
            $cliente->Entrega = ($request->mensajeria === 'true' ? 2 : ($request->correo === 'true' ? 4 : 0)); 
            $cliente->save();
        }
    }

    //Exportar clientes
    public function excel(Request $request)
    {
        $ids = $request->input('Id');
        if (! is_array($ids)) {
            $ids = [$ids];
        }

        $clientes = Cliente::join('provincias', 'provincias.Id', '=', 'clientes.Provincia')
            ->join('localidades', 'localidades.Id', '=', 'clientes.IdLocalidad')
            ->whereIn('clientes.Id', $ids)
            ->select(
                'clientes.Id as Id',
                'clientes.RazonSocial as RazonSocial',
                'clientes.Identificacion as Identificacion',
                'clientes.CondicionIva as CondicionIva',
                'clientes.ParaEmpresa as ParaEmpresa',
                'clientes.Direccion as Direccion',
                'provincias.Nombre as NomProvincia',
                'localidades.Nombre as NomLocalidad',
                'clientes.CP as CodigoPostal')
            ->get();

        $csv = "Numero,Razon Social,Identificacion,Condicion Iva,Para Empresa,Dirección,Provincia,Localidad,Código Postal\n";
        foreach ($clientes as $row) {
            $numero = $row->Id ?? '-';
            $razonSocial = $row->RazonSocial ?? '-';
            $identificacion = $row->Identificacion ?? '-';
            $condicionIva = $row->CondicionIva ?? '-';
            $paraEmpresa = $row->ParaEmpresa ?? '-';
            $direccion = $row->Direccion ?? '-';
            $provincia = Provincia::where('Id', $row->NomProvincia)->first()->nombre ?? $row->NomProvincia ?? '-';
            $localidad = $row->NomLocalidad ?? '-';
            $codigoPostal = $row->CodigoPostal ?? '-';

            $csv .= "$numero,$razonSocial,$identificacion,$condicionIva,$paraEmpresa,$direccion,$provincia,$localidad,$codigoPostal\n";
        }

        // Generar un nombre aleatorio para el archivo
        $name = Str::random(10).'.csv';

        // Guardar el archivo en la carpeta de almacenamiento
        $filePath = storage_path('app/public/'.$name);
        file_put_contents($filePath, $csv);
        chmod($filePath, 0777);

        // Devolver la ruta del archivo generado
        return response()->json(['filePath' => $filePath]);
    }

    public function checkParaEmpresa(Request $request)
    {

        $cliente = Cliente::find($request->empresa);

        if($cliente)
        {
            return response()->json(['cliente' => $cliente]);
        }
    }

    public function getBloqueo(Request $request)
    {
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
