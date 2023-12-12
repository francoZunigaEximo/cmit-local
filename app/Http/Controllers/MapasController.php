<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Prestacion;
use App\Models\ItemPrestacion;
use App\Models\Mapa;
use App\Models\Paciente;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Yajra\DataTables\DataTables;
use Barryvdh\DomPDF\Facade\Pdf;

use App\Traits\ObserverMapas;
class MapasController extends Controller
{
    use ObserverMapas;

    public function index()
    {
        return view('layouts.mapas.index');
    }

    public function search(Request $request): mixed
    {

        $Nro = $request->Nro;
        $Art = $request->Art;
        $Empresa = $request->Empresa;
        $Estado = $request->Estado;
        $corteDesde = $request->corteDesde;
        $corteHasta = $request->corteHasta;
        $entregaDesde = $request->entregaDesde;
        $entregaHasta = $request->entregaHasta;
        $Vencimiento = $request->Vencimiento;
        $Ver = $request->Ver;

        if ($request->ajax()) {

            $query = DB::table('mapas')
                ->select(
                    'mapas.Id AS Id', 
                    'mapas.Nro AS Nro', 
                    'clientes.RazonSocial AS Art', 
                    'clientes2.RazonSocial AS Empresa', 
                    'mapas.Fecha AS Fecha', 
                    'mapas.FechaE AS FechaE', 
                    'prestaciones.eEnviado AS eEnviado', 
                    'prestaciones.Cerrado AS Cerrado', 
                    'prestaciones.Entregado AS Entregado', 
                    'prestaciones.Finalizado AS Finalizado', 
                    'prestaciones.IdPaciente AS IdPaciente', 
                    'prestaciones.Id AS IdPrestacion',
                    'mapas.Cmapeados AS Cmapeados',
                    'mapas.Cpacientes AS contadorPacientes')
                ->selectRaw('COALESCE((SELECT COUNT(*) FROM prestaciones WHERE IdMapa = mapas.Id AND prestaciones.Anulado = 0), 0) AS contadorPrestaciones')
                ->selectRaw("COALESCE((SELECT COUNT(*) FROM mapas JOIN prestaciones ON prestaciones.IdPaciente = pacientes.Id WHERE pacientes.Id = prestaciones.IdPaciente AND mapas.Nro = ? AND prestaciones.Anulado = 0), 0) AS cdorPacientesAnulados", [$request->Nro])
                ->leftJoin('prestaciones', 'mapas.Id', '=', 'prestaciones.IdMapa')
                ->leftJoin('pacientes', 'prestaciones.IdPaciente', '=', 'pacientes.Id')
                ->join('clientes', 'mapas.IdART', '=', 'clientes.Id')
                ->join('clientes AS clientes2', 'mapas.IdEMpresa', '=', 'clientes2.Id')
                ->where('mapas.Nro', '<>', '0')
                ->where('mapas.Inactivo', '=', '0')
                ->orderByDesc('pacientes.Id');

            $query->when($Nro, function ($query) use ($Nro) {
                $query->where('mapas.Nro', '=', $Nro);
                    
            });

            $query->when($Art, function ($query) use ($Art) {
                $query->join('clientes as art_clientes', 'art_clientes.Id', '=', 'mapas.IdART')
                    ->where('art_clientes.RazonSocial', 'LIKE', '%'.$Art.'%');
            });

            $query->when($Empresa, function ($query) use ($Empresa) {
                $query->join('clientes as empresa_clientes', 'empresa_clientes.Id', '=', 'mapas.IdEMpresa')
                    ->where('empresa_clientes.RazonSocial', 'LIKE', '%'.$Empresa.'%');
            });

            //No eEnviado
            $query->when($Estado == 'NOeEnviado', function ($query) {
                $query->where('prestaciones.eEnviado', '=', 0)
                    ->addSelect(DB::raw("'no eEnviado' as estado"));
            });

            //Terminado
            $query->when($Estado == 'terminado', function ($query) {
                $query->addSelect(DB::raw("'Terminado' as estado"))
                    ->where('prestaciones.Entregado', '=', 1)
                    ->where('prestaciones.Cerrado', '=', 1)
                    ->where('prestaciones.eEnviado', '=', 1);
            });

            //Abierto
            $query->when($Estado == 'abierto', function ($query) {
                $query->addSelect(DB::raw("'Abierto' as estado"))
                    ->where('prestaciones.Finalizado', '=', 0)
                    ->where('prestaciones.Cerrado', '=', 0);
            });

            //Cerrado
            $query->when($Estado == 'cerrado', function ($query) {
                $query->addSelect(DB::raw("'Cerrado' as estado"))
                    ->where('prestaciones.Cerrado', '=', 1);
            });

            //eEnviado
            $query->when($Estado == 'eEnviado', function ($query) {
                $query->addSelect(DB::raw("'eEnviado' as estado"))
                    ->where('prestaciones.eEnviado', '=', 1);
            });

            //enProceso
            $query->when($Estado == 'enProceso', function ($query) {
                $query->addSelect(DB::raw("'en proceso' as estado"))
                    ->where('prestaciones.Finalizado', '=', 0)
                    ->where(function ($subquery) {
                        $subquery->where('prestaciones.Cerrado', '=', 0)
                            ->orWhere('prestaciones.Cerrado', '=', 1);
                    });
            });

            //conEenviados
            $query->when($Estado == 'conEenviados', function ($query) {
                $query->addSelect(DB::raw("'con eEnviados' as estado"))
                    ->where(function ($subquery) {
                        $subquery->where('prestaciones.eEnviado', '=', 1)
                            ->orWhere('prestaciones.eEnviado', '=', 0);
                    });
            });

            //Todos
            $query->when($Estado == 'todos', function ($query) {
                $query->addSelect(DB::raw("'Todos' as estado"));
            });

            $query->when(! empty($corteDesde) && ! empty($corteHasta), function ($query) use ($corteDesde, $corteHasta) {
                $query->whereBetween('mapas.Fecha', [$corteDesde, $corteHasta]);
            });

            $query->when(! empty($entregaDesde) && ! empty($entregaHasta), function ($query) use ($entregaDesde, $entregaHasta) {
                $query->whereBetween('mapas.FechaE', [$entregaDesde, $entregaHasta]);
            });

            $query->when($Vencimiento == 'corteVencido', function ($query) {
                $query->where('mapas.Fecha', '<', Carbon::now()->format('Y-m-d'))
                    ->where('mapas.Nro', '<>', 0)
                    ->where('mapas.Fecha', '<>', '0000-00-00')
                    ->where('mapas.Fecha', '<>', null);
            });
            
            $query->when($Vencimiento == 'corteVigente', function ($query) {
                $query->where('mapas.Fecha', '>=', Carbon::now()->format('Y-m-d'))
                    ->where('mapas.Nro', '<>', 0)
                    ->where('mapas.Fecha', '<>', '0000-00-00')
                    ->where('mapas.Fecha', '<>', null);
            });

            $query->when($Vencimiento == 'entregaVigente', function ($query) {
                $query->where('mapas.FechaE', '>=', Carbon::now()->format('Y-m-d'))
                    ->where('mapas.Nro', '<>', 0)
                    ->where('mapas.FechaE', '<>', '0000-00-00')
                    ->where('mapas.FechaE', '<>', null);
            });
            
            $query->when($Vencimiento == 'entregaVencida', function ($query) {
                $query->where('mapas.FechaE', '<', Carbon::now()->format('Y-m-d'))
                    ->where('mapas.Nro', '<>', 0)
                    ->where('mapas.FechaE', '<>', '0000-00-00')
                    ->where('mapas.FechaE', '<>', null);
            });

            $query->when($Ver == 'activo', function ($query) {
                $query->where('mapas.Inactivo', '=', 0);
            });

            $query->when($Ver == 'inactivo', function ($query) {
                $query->where('mapas.Inactivo', '=', 1);
            });

            $result = $query->groupBy('Nro');

            return Datatables::of($result)->make(true);
        }

        return view('layouts.mapas.index');

    }

    public function create(): mixed
    {
        return view('layouts.mapas.create');
    }

    public function delete(Request $request)
    {

        $mapa = Mapa::find($request->Id);
        $mapa->Inactivo = 1;
        $mapa->save();
    }

    public function store(Request $request)
    {
        $nuevoId = Mapa::max('Id') + 1;

        Mapa::create([
            'Id' => $nuevoId,
            'Nro' => $request->Nro,
            'IdART' => $request->IdART,
            'IdEMpresa' => $request->IdEmpresa,
            'Fecha' => $request->Fecha ?? '0000-00-00',
            'FechaE' => $request->FechaE ?? '0000-00-00',
            'Estado' => $request->Estado,
            'Cpacientes' => $request->Cpacientes,
            'Cmapeados' => $request->Cpacientes,
            'Inactivo' => $request->Estado ?? 0,
            'Obs' => $request->Obs,
            'eEnviado' => 0,
        ]);

        return redirect()->route('mapas.edit', ['mapa' => $nuevoId]);

    }

    public function edit(Mapa $mapa)
    {
        $art = Cliente::where('Id', '=', $mapa->IdART)->value('RazonSocial');
        $empresa = Cliente::where('Id', '=', $mapa->IdEMpresa)->value('RazonSocial');

        $conteo = Prestacion::select(
            DB::raw('COUNT(*) as TotalPrestaciones'),
            DB::raw('SUM(CASE WHEN Cerrado = 0 THEN 1 ELSE 0 END) as abiertas'),
            DB::raw('SUM(CASE WHEN Cerrado = 1 THEN 1 ELSE 0 END) as cerradas'),
            DB::raw('SUM(CASE WHEN Forma = 1 OR Incompleto = 1 OR Ausente = 1 OR Devol = 1 THEN 1 ELSE 0 END) as conEstados'),
            DB::raw('SUM(CASE WHEN Finalizado = 1 THEN 1 ELSE 0 END) as finalizados'),
            DB::raw('SUM(CASE WHEN Entregado = 1 THEN 1 ELSE 0 END) as entregados'),
            DB::raw('(SELECT COUNT(*) FROM itemsprestaciones WHERE itemsprestaciones.IdPrestacion = prestaciones.Id AND (itemsprestaciones.CAdj IN (3, 4, 5, 6) OR itemsprestaciones.Cinfo = 3)) as completa')
        )
        ->whereIn('IdMapa', function ($query) use ($mapa) {
            $query->select('Id')
                ->from('mapas')
                ->where('Nro', $mapa->Nro);
        })
        ->first();

        $totalEnProceso =  $conteo->cerradas + $conteo->abiertas;

        $remitos = $this->contadorRemitos($mapa->Id);
            

        return view('layouts.mapas.edit', compact(['mapa', 'art', 'empresa', 'totalEnProceso', 'conteo', 'remitos']));
    }

    public function prestaciones(Request $request)
    {
        $query = Prestacion::select(
            'mapas.Id AS Id',
            'mapas.Nro AS Nro',
            'mapas.Fecha AS Fecha',
            'mapas.FechaE AS FechaE',
            'mapas.Cpacientes AS contadorPacientes',
            'prestaciones.eEnviado AS eEnviado',
            'prestaciones.Cerrado AS Cerrado',
            'prestaciones.Entregado AS Entregado',
            'prestaciones.Finalizado AS Finalizado',
            'prestaciones.IdPaciente AS IdPaciente',
            'prestaciones.Id AS IdPrestacion',
            'prestaciones.NroCEE AS NroCEE',
            'prestaciones.Facturado AS Facturado',
            'pacientes.Apellido AS Apellido',
            'pacientes.Nombre AS Nombre',
            DB::raw('(SELECT CASE WHEN COUNT(*) = SUM(CASE WHEN items.CAdj IN (3, 4, 5, 6) AND items.CInfo = 3 THEN 1 ELSE 0 END) THEN "Completo" ELSE "Incompleto" END FROM itemsprestaciones AS items WHERE items.IdPrestacion = prestaciones.Id) AS Etapa')
        )
        ->join('mapas', 'prestaciones.IdMapa', '=', 'mapas.Id')
        ->join('pacientes', 'prestaciones.IdPaciente', '=', 'pacientes.Id')
        ->leftJoin('itemsprestaciones', 'prestaciones.Id', '=', 'itemsprestaciones.IdPrestacion')
        ->where('mapas.Nro', '=', $request->mapa);

        $result = $query->distinct()->get();
        return response()->json($result);
    }

    public function updateMapa(Request $request): void
    {
        $mapa = Mapa::where('Id', $request->Id)->first();

        if($mapa){
            $mapa->Nro = $request->Nro;
            $mapa->IdART = $request->IdART;
            $mapa->IdEMpresa = $request->IdEmpresa;
            $mapa->Fecha = $request->Fecha;
            $mapa->FechaE = $request->FechaE;
            $mapa->Inactivo = $request->Estado;
            $mapa->Obs = $request->Obs;
            $mapa->Cmapeados = $request->Cmapeados;
            $mapa->Cpacientes = $request->Cpacientes;
            $mapa->save();
        }
    }

    public function excel(Request $request)
    {
        
        $ids = $request->input('Id');
        if (! is_array($ids)) {
            $ids = [$ids];
        }

        $mapas = Mapa::join('prestaciones', 'mapas.Id', '=', 'prestaciones.IdMapa')
            ->join('pacientes', 'prestaciones.IdPaciente', '=', 'pacientes.Id')
            ->select(
                'mapas.Id as Id',
                'mapas.Nro as Nro',
                DB::raw('(Select RazonSocial from clientes where Id = mapas.IdART) AS Art'),
                DB::raw('(Select RazonSocial from clientes where Id = mapas.IdEMpresa) AS Empresa'),
                'mapas.Fecha as Fecha',
                'mapas.FechaE as FechaE',
                'mapas.Inactivo as Inactivo',
                'mapas.Obs as Obs',
                'prestaciones.eEnviado as eEnviado',
                'prestaciones.Cerrado as Cerrado',
                'prestaciones.Entregado as Entregado',
                'prestaciones.Finalizado as Finalizado',
                'prestaciones.NroCEE as NroCEE',
                DB::raw('(Select Nombre from pacientes where Id = prestaciones.IdPaciente) AS Nombre'),
                DB::raw('(Select Apellido from pacientes where Id = prestaciones.IdPaciente) AS Apellido'),
                'prestaciones.Id as IdPrestacion',
                DB::raw('COALESCE((SELECT COUNT(*) FROM prestaciones WHERE IdMapa = mapas.Id), 0) as contadorPrestaciones'),
                DB::raw('COALESCE((SELECT COUNT(*) FROM pacientes WHERE pacientes.Id = prestaciones.IdPaciente), 0) as contadorPacientes'),
                DB::raw('COALESCE((SELECT COUNT(*) FROM pacientes WHERE pacientes.Id = prestaciones.IdPaciente AND prestaciones.Anulado = 0), 0) as cdorPacientesAnulados')
                
            );
            
        $mapas->when($request->modulo === 'remito', function ($mapas) use ($ids, $request) {
            $mapas->whereIn('prestaciones.NroCEE', $ids)
                    ->where('mapas.Nro', $request->mapa);
        });

        $mapas->when($request->tipo === null, function ($mapas) use ($ids) {
            $mapas->whereIn('mapas.Id', $ids);
        });

        $result = $mapas->orderBy('pacientes.Id', 'DESC')->get(); 


        $csv = "Id,Nro,Art,Empresa,Fecha Corte,Fecha Entrega,Inactivo,Nro de Remito, eEnviado,Cerrado,Entregado,Finalizado,Apellido y Nombre, Total de Prestaciones,Observación\n";

        foreach ($result as $row) {
            $Id = $row->Id ?? '-';
            $Nro = $row->Nro ?? '-';
            $Art = $row->Art ?? '-';
            $Empresa = $row->Empresa ?? '-';
            $Fecha = $row->Fecha ?? '-';
            $FechaE = $row->FechaE ?? '-';
            $Inactivo = ($row->Inactivo === 0 ? 'No' : ($row->Inactivo === 1 ? 'Sí' : '-')) ?? '-';
            $nroRemito = ($row->NroCEE === '' || $row->NroCEE === null ? '-' : $row->NroCEE);
            $eEnviado = ($row->eEnviado === 0 ? 'No' : ($row->eEnviado === 1 ? 'Sí' : '-')) ?? '-';
            $Cerrado = ($row->Cerrado === 0 ? 'No' : ($row->Cerrado === 1 ? 'Sí' : '-')) ?? '-';
            $Entregado = ($row->Entregado === 0 ? 'No' : ($row->Entregado === 1 ? 'Sí' : '-')) ?? '-';
            $Finalizado = ($row->Finalizado === 0 ? 'No' : ($row->Finalizado === 1 ? 'Sí' : '-')) ?? '-';
            $NombreCompleto = $row->Apellido.' '.$row->Nombre;
            $TotalPrestaciones = $row->contadorPrestaciones ?? '-';
            $Obs = str_replace(["\r", "\n", ','], ' ', $row->Obs);

            $csv .= "$Id,$Nro,$Art,$Empresa,$Fecha,$FechaE,$Inactivo,$nroRemito,$eEnviado,$Cerrado,$Entregado,$Finalizado,$NombreCompleto,$TotalPrestaciones,$Obs\n";
        }

        // Generar un nombre aleatorio para el archivo
        $name = Str::random(10).'.xlsx';

        // Guardar el archivo en la carpeta de almacenamiento
        $filePath = storage_path('app/public/'.$name);
        file_put_contents($filePath, $csv);
        chmod($filePath, 0777);

        // Devolver la ruta del archivo generado
        return response()->json(['filePath' => $filePath]);

    }

    public function pdf(Request $request)
    {
        $ids = $request->input('Id');
        if (! is_array($ids)) {
            $ids = [$ids];
        }

        $mapas = Mapa::select(
                'mapas.Id as Id',
                'mapas.Nro as Nro',
                DB::raw('(Select RazonSocial from clientes where Id = mapas.IdART) AS Art'),
                DB::raw('(Select RazonSocial from clientes where Id = mapas.IdEMpresa) AS Empresa'),
                'mapas.Fecha as Fecha',
                'mapas.FechaE as FechaE',
                'mapas.Inactivo as Inactivo',
                'mapas.Obs as Obs',
                'prestaciones.eEnviado as eEnviado',
                'prestaciones.Cerrado as Cerrado',
                'prestaciones.Entregado as Entregado',
                'prestaciones.Finalizado as Finalizado',
                'prestaciones.NroCEE as NroCEE',
                DB::raw('(Select Nombre from pacientes where Id = prestaciones.IdPaciente) AS Nombre'),
                DB::raw('(Select Apellido from pacientes where Id = prestaciones.IdPaciente) AS Apellido'),
                'prestaciones.Id as IdPrestacion',
                DB::raw('COALESCE((SELECT COUNT(*) FROM prestaciones WHERE IdMapa = mapas.Id), 0) as contadorPrestaciones'),
                DB::raw('COALESCE((SELECT COUNT(*) FROM pacientes WHERE pacientes.Id = prestaciones.IdPaciente), 0) as contadorPacientes'),
                DB::raw('COALESCE((SELECT COUNT(*) FROM pacientes WHERE pacientes.Id = prestaciones.IdPaciente AND prestaciones.Anulado = 0), 0) as cdorPacientesAnulados'))
            ->join('prestaciones', 'mapas.Id', '=', 'prestaciones.IdMapa')
            ->join('pacientes', 'prestaciones.IdPaciente', '=', 'pacientes.Id')
            ->whereIn('prestaciones.NroCEE', $ids)
            ->where('mapas.Nro', $request->mapa)
            ->orderBy('pacientes.Id', 'DESC')
            ->get(); 

        $pdf = PDF::loadView('layouts.mapas.pdf', ['result' => $mapas]);
        $path = storage_path('app/public/');
        $fileName = time() . '.pdf';
        $pdf->save($path . $fileName);
        
        $pdfPath = $path . $fileName;
        chmod($pdfPath, 0777);
        return response()->json(['filePath' => $pdfPath]);
    }

    public function show($id)
    {
        // Show
    }

    //Obtenemos listado mapas
    public function getMapas(Request $request)
    {
        $empresa= $request->empresa;
        $art = $request->art;

        $resultados = Cache::remember('mapas_', 5, function () use ($empresa, $art) {

            $mapas = Mapa::join('clientes as Art', 'mapas.IdART', '=', 'Art.Id')
                ->join('clientes as Empresa', 'mapas.IdEMpresa', '=', 'Empresa.Id')
                ->select(
                    'mapas.Id as Id',
                    'mapas.Nro as Nro',
                    'Art.RazonSocial as RSArt',
                    'Empresa.RazonSocial as RSE')
                ->where('mapas.IdART', '=', $art)
                ->where('mapas.IdEMpresa', '=', $empresa)
                ->where('mapas.Cmapeados', '>=', 1)
                ->whereDate('mapas.Fecha', '>', now()->toDateString())
                ->get();

            return $mapas;
        });

        return response()->json(['mapas' => $resultados]);
    }

    public function saveRemitos(Request $request)
    {
        $prestaciones = Prestacion::where('NroCEE', $request->Id)
            ->where('Entregado', 0)
            ->get();

        foreach ($prestaciones as $prestacion) {
            $prestacion->Entregado = 1;
            $prestacion->FechaEntrega = $request->FechaE;
            $prestacion->save();
        }

        $this->constanciaseRemito($request->Id, $request->Obs);
    }


    public function searchMapaPres(Request $request)
    {

            $NroPrestacion = $request->NroPrestacion;
            $NroRemito = $request->NroRemito;
            $Etapa = $request->Etapa;
            $Estado = $request->Estado;

            $estadoColumnas = [
                'cerrado' => 'Cerrado',
                'finalizado' => 'Finalizado',
                'entregado' => 'Entregado',
                'anulado' => 'Anulado',
                'eEnviado' => 'eEnviado'
            ];
            
            $query = Prestacion::select(
                'mapas.Id AS Id',
                'mapas.Nro AS Nro',
                'mapas.Fecha AS Fecha',
                'mapas.FechaE AS FechaE',
                'mapas.Cpacientes AS contadorPacientes',
                'prestaciones.eEnviado AS eEnviado',
                'prestaciones.Cerrado AS Cerrado',
                'prestaciones.Entregado AS Entregado',
                'prestaciones.Finalizado AS Finalizado',
                'prestaciones.IdPaciente AS IdPaciente',
                'prestaciones.Id AS IdPrestacion',
                'prestaciones.NroCEE AS NroCEE',
                'prestaciones.Facturado AS Facturado',
                'pacientes.Apellido AS Apellido',
                'pacientes.Nombre AS Nombre',
                DB::raw('(SELECT CASE WHEN COUNT(*) = SUM(CASE WHEN items.CAdj IN (3, 4, 5, 6) AND items.CInfo = 3 THEN 1 ELSE 0 END) THEN "Completo" ELSE "Incompleto" END FROM itemsprestaciones AS items WHERE items.IdPrestacion = prestaciones.Id) AS Etapa')
            )
            ->join('mapas', 'prestaciones.IdMapa', '=', 'mapas.Id')
            ->join('pacientes', 'prestaciones.IdPaciente', '=', 'pacientes.Id')
            ->leftJoin('itemsprestaciones', 'prestaciones.Id', '=', 'itemsprestaciones.IdPrestacion')
            ->where('mapas.Nro', '=', $request->mapa);

            
            $query->when($NroPrestacion, function ($query) use ($NroPrestacion) {
                $query->where('prestaciones.Id', $NroPrestacion);
            });
            
            $query->when($NroRemito, function ($query) use ($NroRemito) {
                $query->where('prestaciones.NroCEE', $NroRemito);
            });
            
            $query->when($Estado == 'abierto', function ($query) use ($Estado) {
                $query->where('prestaciones.Finalizado', '=', 0)
                    ->where('prestaciones.Cerrado', '=', 0);
            });
            
            if (isset($estadoColumnas[$Estado])) {
                $query->where("prestaciones.{$estadoColumnas[$Estado]}", '=', 1);
            }

            if ($Etapa === 'completa') {
                $query->where(function ($query) {
                    $query->whereIn('itemsprestaciones.CAdj', [3, 4, 5, 6])
                        ->where('itemsprestaciones.CInfo', 3);
                });
            } 
            
            if ($Etapa === 'incompleta') {
                $query->where(function ($query) {
                    $query->whereNotIn('itemsprestaciones.CAdj', [3, 4, 5, 6])
                        ->orWhere('itemsprestaciones.CInfo', '<>', 3);
                });
            }
              
            $result = $query->distinct()->get();

            /*$result = $result->filter(function ($item) {
                return $item->Etapa == 'Completo' || $item->Etapa == 'Incompleto';
            });*/
            
            return response()->json(['result' => $result]);
    }

    public function getPacienteMapa(Request $request)
    {
        $prestacion = Prestacion::where('Id', $request->prestacion)->first('IdPaciente');
        $paciente = Paciente::where('Id', $prestacion->IdPaciente)->first(['Nombre', 'Apellido', 'TipoDocumento', 'Documento']);

        return response()->json($paciente);

    }

    public function examenes(Request $request)
    {
        
        $query = DB::table('itemsprestaciones')->select(
            'examenes.Id AS IdExamen',
            'examenes.Nombre AS NombreExamen',
            'itemsprestaciones.CAdj AS CAdj',
            'itemsprestaciones.CInfo AS CInfo',
            DB::raw('(SELECT Nombre FROM proveedores WHERE Id = examenes.IdProveedor) AS NombreProveedor'),
            DB::raw('(SELECT Nombre FROM profesionales WHERE Id = itemsprestaciones.IdProfesional) AS NombreEfector'),
            DB::raw('(SELECT Apellido FROM profesionales WHERE Id = itemsprestaciones.IdProfesional) AS ApellidoEfector'),
            DB::raw('(SELECT Nombre FROM profesionales WHERE Id = itemsprestaciones.IdProfesional2) AS NombreInformador'),
            DB::raw('(SELECT Apellido FROM profesionales WHERE Id = itemsprestaciones.IdProfesional2) AS ApellidoInformador')
        )->join('examenes', 'itemsprestaciones.IdExamen', '=', 'examenes.Id')
         ->leftJoin('proveedores', 'itemsprestaciones.IdProveedor', '=', 'proveedores.Id')
         ->leftJoin('profesionales', 'itemsprestaciones.IdProfesional', '=', 'profesionales.Id')
         ->where('itemsprestaciones.IdPrestacion', $request->prestacion)
         ->get();

         //*var_dump($query);
         return response()->json($query);
    }

    public function cerrar(Request $request): mixed
    {

        $NroPresCerrar = $request->prestacion;
        $NroRemitoCerrar = $request->remito;
        $EstadoCerrar = $request->estado;

        $estadoColumnas = [
            'cerrado' => 'Cerrado',
            'finalizado' => 'Finalizado',
            'entregado' => 'Entregado',
            'anulado' => 'Anulado',
            'eEnviado' => 'eEnviado'
        ];

        $query = Prestacion::select(
            'prestaciones.Id as IdPrestacion',
            'prestaciones.Fecha as Fecha',
            DB::raw('(SELECT Nombre FROM pacientes WHERE Id = prestaciones.IdPaciente) AS NombrePaciente'),
            DB::raw('(SELECT Apellido FROM pacientes WHERE Id = prestaciones.IdPaciente) AS ApellidoPaciente'),
            'prestaciones.Finalizado as Finalizado',
            'prestaciones.eEnviado as eEnviado',
            'prestaciones.Cerrado as Cerrado',
            'prestaciones.Entregado AS Entregado',
            'prestaciones.Anulado AS Anulado')
            ->join('pacientes', 'prestaciones.IdPaciente', '=', 'pacientes.Id')
            ->leftJoin('mapas', 'prestaciones.IdMapa', '=', 'mapas.Id')
            ->leftJoin('itemsprestaciones', 'prestaciones.Id', '=', 'itemsprestaciones.IdPrestacion')
            ->where('itemsprestaciones.Ausente', 0)
            ->where('itemsprestaciones.Incompleto', 0)
            ->where('prestaciones.Cerrado', 0)
            ->where('prestaciones.Finalizado', 0)
            ->where('mapas.Nro', '=', $request->mapa);

        $query->when($NroPresCerrar, function ($query) use ($NroPresCerrar) {
            $query->where('prestaciones.Id', $NroPresCerrar);
        });

        $query->when($NroRemitoCerrar, function ($query) use ($NroRemitoCerrar) {
            $query->where('prestaciones.NroCEE', $NroRemitoCerrar);
        });

        $query->when($EstadoCerrar == 'abierto', function ($query) {
            $query->where('prestaciones.Finalizado', '=', 0)
                ->where('prestaciones.Cerrado', '=', 0);
        });
        
        if (isset($estadoColumnas[$EstadoCerrar])) {
            $query->where("prestaciones.{$estadoColumnas[$EstadoCerrar]}", '=', 1);
        }

        $result = $query->distinct()->get();

        return response()->json($result);
    }

    public function saveCerrar(Request $request)
    {
        $ids = $request->ids;

        foreach ($ids as $id) {
            $prestacion = Prestacion::where('Id', $id)->first();
            
            if($prestacion){
                $prestacion->Cerrado = 1;
                $prestacion->FechaCierre = Carbon::now()->format('Y-m-d');
                $prestacion->save();
            } 
        }
    }

    public function saveFinalizar(Request $request): void
    {
        
        $ids = $request->ids;

        foreach ($ids as $id) {
            $prestacion = Prestacion::where('Id', $id)->where('Cerrado', 1)->first();
            
            if($prestacion){
                $prestacion->Finalizado = 1;
                $prestacion->FechaFinalizado = Carbon::now()->format('Y-m-d');
                $prestacion->save();
            } 
        }
    }

    public function saveEstado(Request $request)
    {
        $estados = [
            'Finalizado' => 'FechaFinalizado',
            'Cerrado' => 'FechaCierre'  
        ];
    
        if (array_key_exists($request->estado, $estados)) {
            $estado = $request->estado;
            $fecha = $estados[$estado];
    
            $ids = $request->ids;
    
            foreach ($ids as $id) {
                $prestacion = Prestacion::where('Id', $id)->first();
                
                if ($prestacion) {
                    $prestacion->$estado = 1;
                    $prestacion->$fecha = Carbon::now()->format('Y-m-d');
                    $prestacion->save();
                } 
            }            
        }   
    }

    public function checker(Request $request)
    {
        $mapa = Mapa::where('Nro', $request->Nro)->exists();

        return response()->json($mapa);
    }

    public function finalizar(Request $request): mixed
    {

        $NroRemito = $request->remito;
        $NroPrestacion = $request->prestacion;

        $query = Prestacion::select(
            'prestaciones.Id as IdPrestacion',
            'prestaciones.Fecha as Fecha',
            DB::raw('(SELECT Nombre FROM pacientes WHERE Id = prestaciones.IdPaciente) AS NombrePaciente'),
            DB::raw('(SELECT Apellido FROM pacientes WHERE Id = prestaciones.IdPaciente) AS ApellidoPaciente'),
            'prestaciones.Finalizado as Finalizado',
            'prestaciones.eEnviado as eEnviado',
            'prestaciones.Cerrado as Cerrado',
            'prestaciones.Entregado AS Entregado',
            'prestaciones.Anulado AS Anulado')
            ->join('pacientes', 'prestaciones.IdPaciente', '=', 'pacientes.Id')
            ->join('mapas', 'prestaciones.IdMapa', '=', 'mapas.Id')
            ->where('prestaciones.Cerrado', 1)
            ->where('prestaciones.Finalizado', 0)
            ->where('prestaciones.Forma', 0)
            ->where('prestaciones.Devol', 0)
            ->where('prestaciones.RxPreliminar', 0)
            ->where('prestaciones.SinEsc', 0)
            ->where('mapas.Nro', '=', $request->mapa);

            $query->when($NroPrestacion, function ($query) use ($NroPrestacion) {
                $query->where('prestaciones.Id', $NroPrestacion);
            });
    
            $query->when($NroRemito, function ($query) use ($NroRemito) {
                $query->where('prestaciones.NroCEE', $NroRemito);
            });

            $result = $query->distinct()->get();

        return response()->json($result);
    }

    public function eEnviar(Request $request): mixed
    {
        $desde = $request->desde;
        $hasta = $request->hasta;
        $eEnviado = $request->eEnviado;
        $prestacion = $request->prestacion;
        $mapa = $request->mapa;

        $query = Prestacion::select(
            'prestaciones.Id AS IdPrestacion',
            'prestaciones.Fecha AS Fecha',
            'prestaciones.TipoPrestacion AS TipoPrestacion',
            'prestaciones.eEnviado AS eEnviado',
            DB::raw('(SELECT Nombre FROM pacientes WHERE Id = prestaciones.IdPaciente) AS NombrePaciente'),
            DB::raw('(SELECT Apellido FROM pacientes WHERE Id = prestaciones.IdPaciente) AS ApellidoPaciente'),
            'pacientes.Documento AS Documento')
            ->join('pacientes', 'prestaciones.IdPaciente', '=', 'pacientes.Id')
            ->leftJoin('itemsprestaciones', 'prestaciones.Id', '=', 'itemsprestaciones.IdPrestacion')
            ->join('mapas', 'prestaciones.IdMapa', '=', 'mapas.Id')
            ->where('prestaciones.Cerrado', 1)
            ->where('prestaciones.Finalizado', 1)
            ->whereIn('itemsprestaciones.CAdj',[3, 4, 5, 6])
            ->where('itemsprestaciones.CInfo', 3)
            ->where('mapas.Nro', $mapa);

        $query->when($prestacion, function($query) use ($prestacion) {
            $query->where('prestaciones.Id', $prestacion);
        });

        $query->when($eEnviado === 'eEnviadas', function($query) {
            $query->where('prestaciones.eEnviado', 1);
        }, function($query){
            $query->where('prestaciones.eEnviado', 0);
        });

        $query->when(!empty($desde) && !empty($hasta), function ($query) use ($desde, $hasta){
            $query->whereBetween('prestaciones.Fecha', [$desde, $hasta]);
        });

        $resutl = $query->distinct()->get();

        return response()->json($resutl);
    }

    public function saveEnviar(Request $request): void
    {
        
        $ids = $request->ids;

        foreach ($ids as $id) {
            $prestacion = Prestacion::where('Id', $id)->first();
            
            if($prestacion){
                $prestacion->eEnviado = 1;
                $prestacion->FechaEnviado = Carbon::now()->format('Y-m-d');
                $prestacion->save();
            } 
        }
    }

    public function getCerrar(Request $request)
    {
        $query = Prestacion::select(
            'prestaciones.Id as IdPrestacion',
            'prestaciones.Fecha as Fecha',
            DB::raw('(SELECT Nombre FROM pacientes WHERE Id = prestaciones.IdPaciente) AS NombrePaciente'),
            DB::raw('(SELECT Apellido FROM pacientes WHERE Id = prestaciones.IdPaciente) AS ApellidoPaciente'),
            'prestaciones.Finalizado as Finalizado',
            'prestaciones.eEnviado as eEnviado',
            'prestaciones.Cerrado as Cerrado',
            'prestaciones.Entregado AS Entregado',
            'prestaciones.Anulado AS Anulado')
            ->join('pacientes', 'prestaciones.IdPaciente', '=', 'pacientes.Id')
            ->leftJoin('mapas', 'prestaciones.IdMapa', '=', 'mapas.Id')
            ->leftJoin('itemsprestaciones', 'prestaciones.Id', '=', 'itemsprestaciones.IdPrestacion')
            ->where('itemsprestaciones.Ausente', 0)
            ->where('itemsprestaciones.Incompleto', 0)
            ->where(function($query) {
                $query->where('prestaciones.Cerrado', 0)
                      ->orWhere('prestaciones.Cerrado', 1);
            })
            ->where('prestaciones.Finalizado', 0)
            ->where('mapas.Nro', '=', $request->mapa)
            ->distinct()->get();
        
        return response()->json($query);
    }

    public function getFinalizar(Request $request)
    {
        $query = Prestacion::select(
            'prestaciones.Id as IdPrestacion',
            'prestaciones.Fecha as Fecha',
            DB::raw('(SELECT Nombre FROM pacientes WHERE Id = prestaciones.IdPaciente) AS NombrePaciente'),
            DB::raw('(SELECT Apellido FROM pacientes WHERE Id = prestaciones.IdPaciente) AS ApellidoPaciente'),
            'prestaciones.Finalizado as Finalizado',
            'prestaciones.eEnviado as eEnviado',
            'prestaciones.Cerrado as Cerrado',
            'prestaciones.Entregado AS Entregado',
            'prestaciones.Anulado AS Anulado')
            ->join('pacientes', 'prestaciones.IdPaciente', '=', 'pacientes.Id')
            ->join('mapas', 'prestaciones.IdMapa', '=', 'mapas.Id')
            ->where('prestaciones.Cerrado', 1)
            ->where(function($query){
                $query->where('prestaciones.Finalizado', 0)
                    ->orWhere('prestaciones.FInalizado', 1);
            })
            ->where('prestaciones.Forma', 0)
            ->where('prestaciones.Devol', 0)
            ->where('prestaciones.RxPreliminar', 0)
            ->where('prestaciones.SinEsc', 0)
            ->where('mapas.Nro', '=', $request->mapa)
            ->distinct()->get();

        return response()->json($query);
    }

    public function geteEnviar(Request $request)
    {
        $query = Prestacion::select(
            'prestaciones.Id AS IdPrestacion',
            'prestaciones.Fecha AS Fecha',
            'prestaciones.TipoPrestacion AS TipoPrestacion',
            'prestaciones.eEnviado AS eEnviado',
            DB::raw('(SELECT Nombre FROM pacientes WHERE Id = prestaciones.IdPaciente) AS NombrePaciente'),
            DB::raw('(SELECT Apellido FROM pacientes WHERE Id = prestaciones.IdPaciente) AS ApellidoPaciente'),
            'pacientes.Documento AS Documento')
            ->join('pacientes', 'prestaciones.IdPaciente', '=', 'pacientes.Id')
            ->leftJoin('itemsprestaciones', 'prestaciones.Id', '=', 'itemsprestaciones.IdPrestacion')
            ->join('mapas', 'prestaciones.IdMapa', '=', 'mapas.Id')
            ->where('prestaciones.Cerrado', 1)
            ->whereIn('itemsprestaciones.CAdj',[3, 4, 5, 6])
            ->where('itemsprestaciones.CInfo', 3)
            ->where('prestaciones.Finalizado', 1)
            ->where(function($query){
                $query->where('prestaciones.eEnviado', 1)
                    ->orWhere('prestaciones.eEnviado', 0);
            })
            ->where('mapas.Nro', $request->mapa)
            ->distinct()->get();

        return response()->json($query);
    }

    private function contadorRemitos($id)
    {
        $conteo = Prestacion::select('NroCEE', DB::raw('COUNT(*) as contadorRemitos'))
            ->where('IdMapa', $id)
            ->where('Entregado', 0)
            ->groupBy('NroCEE')
            ->get();
        
        return $conteo;
    }


} 
