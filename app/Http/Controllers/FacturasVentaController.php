<?php

namespace App\Http\Controllers;

use App\Models\ExamenCuenta;
use App\Models\ExamenCuentaIt;
use Illuminate\Http\Request;
use App\Models\FacturaDeVenta;
use App\Models\NotaCredito;
use App\Traits\CheckPermission;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;

class FacturasVentaController extends Controller
{

    use CheckPermission;

    public function index()
    {
        if (!$this->hasPermission("facturacion_show")) {
            abort(403);
        }

        return view('layouts.facturas.index');
    }

    public function create()
    {
        if(!$this->hasPermission("facturacion_add"))
        {
            abort(403);
        }

        return view('layouts.facturas.create');
    }

    public function edit(FacturaDeVenta $factura)
    {
        if(!$this->hasPermission("facturacion_edit"))
        {
            abort(403);
        }

        return view('layouts.facturas.edit', compact('factura'));
    }

    
    public function search(Request $request)
    {
        if(!$this->hasPermission("facturacion_show"))
        {
            return response()->json(['message' => 'No tienes permisos para realizar esta acción'], 403);
        }

        if ($request->ajax())
        {
            $query = null;

            if ($request->Tipo === 'facturas') 
            {
                $query = $this->facturas();

                $this->filtroRegistrosCero($query, 'facturasventa');
                $this->filtroFechaCompleta($query, $request);
                $this->filtroFechaDesde($query, $request);

                $this->filtroFactura($query, $request, 'facturasventa');
                $this->filtroFacturaDesHas($query, $request, 'facturasventa');

                $this->filtroEmpresa($query, $request);
                $this->filtroArt($query, $request);

                $result = $query->orderBy('Tipo', 'DESC')
                           ->orderBy('Sucursal', 'DESC')
                           ->orderBy('NroFactura', 'DESC')
                           ->groupBy('Id')
                           ->get();

            } elseif($request->Tipo === 'exacuenta') {

                $query = $this->examenesCuenta();
                $this->filtroRegistrosCero($query, 'pagosacuenta');
                $this->filtroFechaCompleta($query, $request);
                $this->filtroFechaDesde($query, $request);

                $this->filtroFactura($query, $request, 'pagosacuenta');
                $this->filtroFacturaDesHas($query, $request, 'pagosacuenta');

                $this->filtroEmpresa($query, $request);
                $this->filtroArt($query, $request);

                $result = $query->orderBy('Tipo', 'DESC')
                    ->orderBy('Suc', 'DESC')
                    ->orderBy('Nro', 'DESC')
                    ->get();
                               
            } elseif($request->Tipo === 'todo') {
                    
                $query1 = $this->facturas();
                $query2 = $this->examenesCuenta();
                $this->filtroRegistrosCero($query1, 'facturasventa');
                $this->filtroRegistrosCero($query2, 'pagosacuenta');
                $this->filtroFechaCompleta($query1, $request);
                $this->filtroFechaCompleta($query2, $request);

                $this->filtroFechaDesde($query1, $request, 'facturasventa');
                $this->filtroFechaDesde($query2, $request, 'pagosacuenta');

                $this->filtroFactura($query1, $request, 'facturasventa');
                $this->filtroFactura($query2, $request, 'pagosacuenta');

                $this->filtroFacturaDesHas($query1, $request, 'facturasventa');
                $this->filtroFacturaDesHas($query2, $request, 'pagosacuenta');

                $this->filtroEmpresa($query1, $request);
                $this->filtroEmpresa($query2, $request);
                $this->filtroArt($query1, $request);
                $this->filtroArt($query2, $request);

                $result = $query1->union($query2)->get();
                
            }
              
            return DataTables::of($result)->make(true);
        }

        return view('layouts.facturas.index');
    }

    public function delete(Request $request)
    {
        if(!$this->hasPermission("facturacion_delete"))
        {
            return response()->json(['message' => 'No tienes permisos para realizar esta acción'], 403);
        }

        $Ids = $request->Ids;
        $Tipos = $request->Tipo;

        if (!is_array($Ids)) {
            $Ids = [$Ids];
        }

        if(count($Ids) !== count($Tipos)) {
            return response()->json(['message' => 'Hay un error en la petición. No coinciden los rangos en el proceso de eliminación.'], 409);
        }
        
        $mergeArr = [];
        $count = count($Tipos);

        for ($i = 0; $i < $count; $i++) {
            $mergeArr[] = [
                'tipo' => $Tipos[$i],
                'id' => $Ids[$i],
            ];
        }
        
        $respuestas = [];

        foreach ($mergeArr as $dato) {
            $respuesta = [];
            if($dato['tipo'] == 1) {
                $factura = FacturaDeVenta::find($dato['id']);
                if($factura) {
                    if($this->checkNotaCredito($factura->Id)) {
                        $respuesta = ['msg' => 'No se puede eliminar la factura, ya que tiene una nota de crédito asociada.', 'tipo' => 'warning'];
                    }else{
                        $factura->delete();
                        $respuesta = ['msg' => 'Factura eliminada con éxito.', 'tipo' => 'success'];
                    } 
                }
            } elseif($dato['tipo'] == 2) {
                $examenCuenta = ExamenCuenta::find($dato['id']);
                if($examenCuenta) {
                    if($this->checkFacturaConExamen($examenCuenta->Id) > 0) {
                       $respuesta = ['msg' => 'No se puede eliminar el examen a cuenta '.$examenCuenta->Id.', ya que tiene un examen asociado.', 'tipo' => 'warning'];
                    }else{
                        $examenCuenta->delete();
                    $respuesta = ['msg' => 'Examen a cuenta eliminado con exito', 'tipo' => 'success'];
                    } 
                }
            }

            $respuestas[] = $respuesta;
        }

        return response()->json($respuestas);
    }

    public function getFactura(Request $request)
    {
        $factura = FacturaDeVenta::where('IdPrestacion', $request->Id)->first(['Tipo', 'Sucursal', 'NroFactura']);

        if($factura)
        {
            return response()->json(['factura' => $factura]);
        }
    }

    private function facturas()
    {
        return FacturaDeVenta::join('clientes', 'facturasventa.IdEmpresa', '=', 'clientes.Id')
            ->join('itemsprestaciones', 'facturasventa.IdPrestacion', '=', 'itemsprestaciones.Id')
            ->leftJoin('notascredito', 'facturasventa.Id', '=', 'notascredito.IdFactura')
            ->select(
                'facturasventa.Id as Id',
                'facturasventa.Tipo as Tipo',
                'facturasventa.Sucursal as Sucursal',
                'facturasventa.NroFactura as NroFactura',
                'facturasventa.Fecha as Fecha',
                'clientes.RazonSocial as RazonSocial',
                'clientes.Identificacion as Identificacion',
                DB::raw('1 as Pagado'), // Columna virtual para equiparar la comparativa en la unión.
                DB::raw('(SELECT COUNT(notascredito.IdFactura) where notascredito.IdFactura = facturasventa.Id) as Total'),
                DB::raw('1 as Opcion')
            );
    }


    private function examenesCuenta()
    {
        return ExamenCuenta::join('clientes', 'pagosacuenta.IdEmpresa', '=', 'clientes.Id')
        ->select(
            'pagosacuenta.Id as Id',
            'pagosacuenta.Tipo as Tipo',
            'pagosacuenta.Suc as Sucursal',
            'pagosacuenta.Nro as NroFactura',
            'pagosacuenta.Fecha as Fecha',
            'clientes.RazonSocial as RazonSocial',
            'clientes.Identificacion as Identificacion',
            'pagosacuenta.Pagado as Pagado',
            DB::raw('0 as Total'),
            DB::raw('2 as Opcion')
        );
    }

    private function filtroFechaCompleta($query, $request)
    {
        return $query->when(!empty($request->FechaDesde) && !empty($request->FechaHasta), function($query) use ($request) {
            $query->when($request->Tipo === 'facturas', function($query) use ($request) {
                $query->whereBetween('facturasventa.Fecha', [$request->FechaDesde, $request->FechaHasta]);
            });
            $query->when($request->Tipo === 'exacuenta', function($query) use ($request) {
                $query->whereBetween('pagosacuenta.Fecha', [$request->FechaDesde, $request->FechaHasta]);
            });
            $query->when($request->Tipo === 'todo', function($query) use ($request) {
                $query->whereBetween('pagosacuenta.Fecha', [$request->FechaDesde, $request->FechaHasta]);
                        $query->where(function($query) use ($request) {
                            $query->whereBetween('facturasventa.Fecha', [$request->FechaDesde, $request->FechaHasta]);
                        });   
            });
        });
    }

    private function filtroFechaDesde($query, $request, string $tabla = null)
    {
        return $query->when(empty($request->FechaDesde) && !empty($request->FechaHasta), function($query) use ($request, $tabla) {
            
            $query->when($request->Tipo === 'facturas', function($query) use ($request) {
                $query->whereDate('facturasventa.Fecha', '<=', $request->FechaHasta);
            });
            $query->when($request->Tipo === 'exacuenta', function($query) use ($request) {
                $query->whereDate('pagosacuenta.Fecha', '<=', $request->FechaHasta);
            });
            $query->when($request->Tipo === 'todo', function($query) use ($request, $tabla) {
                $query->whereDate($tabla.'.Fecha', '<=', $request->FechaHasta);      
            });
        });
    }

    private function filtroRegistrosCero($query, string $tabla)
    {
        return $query->when(!empty($tabla), function($query) use ($tabla) {
            $query->whereNot($tabla.'.Id', 0);
        });
    }

    private function filtroFactura($query, $request, string $tabla)
    {
        return $query->when(!empty($request->FacturaDesde) && empty($request->FacturaHasta), function($query) use ($request, $tabla) {
            
            $data = explode('-', $request->FacturaDesde);

            $query->when($tabla === 'facturasventa', function($query) use ($data) {
                $query->where('facturasventa.Tipo', $data[0]);
                $query->where('facturasventa.Sucursal', intval($data[1]));
                $query->where('facturasventa.NroFactura', intval($data[2]));
            });

            $query->when($tabla === 'pagosacuenta', function($query) use ($data) {
                $query->where('pagosacuenta.Tipo', $data[0]);
                $query->where('pagosacuenta.Suc', intval($data[1]));
                $query->where('pagosacuenta.Nro', intval($data[2]));
            });
        });
    }

    private function filtroFacturaDesHas($query, $request, string $tabla)
    {
        return $query->when(!empty($request->FacturaDesde) && !empty($request->FacturaHasta), function($query) use ($request, $tabla) {
            
            $dataDesde = explode('-', $request->FacturaDesde);
            $dataHasta = explode('-', $request->FacturaHasta);

            $query->when($tabla === 'facturasventa', function($query) use ($dataDesde, $dataHasta) {
                $query->where('facturasventa.Tipo', '>=', $dataDesde[0]);
                $query->where('facturasventa.Tipo', '<=', $dataHasta[0]);
                $query->where('facturasventa.Sucursal', '>=', intval($dataDesde[1]));
                $query->where('facturasventa.Sucursal', '<=', intval($dataHasta[1]));
                $query->where('facturasventa.NroFactura', '>=', intval($dataDesde[2]));
                $query->where('facturasventa.NroFactura', '<=', intval($dataHasta[2]));
            });

            $query->when($tabla === 'pagosacuenta', function($query) use ($dataDesde, $dataHasta) {
                $query->where('pagosacuenta.Tipo', '>=', $dataDesde[0]);
                $query->where('pagosacuenta.Tipo', '<=', $dataHasta[0]);
                $query->where('pagosacuenta.Suc', '>=', intval($dataDesde[1]));
                $query->where('pagosacuenta.Suc', '<=', intval($dataHasta[1]));
                $query->where('pagosacuenta.Nro', '>=', intval($dataDesde[2]));
                $query->where('pagosacuenta.Nro', '<=', intval($dataHasta[2]));
            });
        });
    }

    private function filtroEmpresa($query, $request)
    {
        return $query->when(!empty($request->Empresa), function($query) use ($request) {
            $query->where('clientes.Id', $request->Empresa);
        });
    }

    private function filtroArt($query, $request)
    {
        return $query->when(!empty($request->Art), function($query) use ($request) {
            $query->where('clientes.Id', $request->Art);
        });
    }

    //Chequeo de factura con nota de crédito
    private function checkNotaCredito(int $Id)
    {
        return NotaCredito::where('IdFactura', $Id)->exists();
    }

    //Chequeo de Examen a Cuenta con Examen asociado
    private function checkFacturaConExamen(int $Id)
    {
        return ExamenCuentaIt::where('IdPago', $Id)->whereNot('IdExamen', 0)->whereNot('IdPrestacion', 0)->whereNot('Obs', 'provisorio')->count();
    }


}
