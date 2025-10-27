<?php

namespace App\Http\Controllers;

use App\Models\FacturaCompra;
use App\Models\ItemPrestacion;
use App\Models\ItemsFacturaCompra;
use App\Models\ItemsFacturaCompra2;
use App\Models\Profesional;
use App\Services\Reportes\Cuerpos\FacturaCompra as CuerposFacturaCompra;
use App\Services\Reportes\Cuerpos\FacturaCompraCuerpo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;

use App\Services\Reportes\ReporteService;
use App\Services\Reportes\Titulos\Basico;
use App\Services\Reportes\Titulos\Empresa;
use App\Services\ReportesExcel\ReporteExcel;

class FacturaCompraController extends Controller
{
    //
    private $reporteService;
    private $reporteExcel;

    public function __construct(ReporteService $reporteService, ReporteExcel $reporteExcel)
    {
        $this->reporteService = $reporteService;
        $this->reporteExcel = $reporteExcel;
    }
    public function index()
    {
        // if (!$this->hasPermission("facturacion_show")) {
        //     abort(403);
        // }

        return view('layouts.facturaCompra.index');
    }

    public function buscarEfectores(Request $request)
    {
        if ($request->ajax()) {
            if (isset($request->fechaDesde) && isset($request->fechaHasta)) {
                $tabla = DB::Select("CALL getEfectoresFacturar(?,?, ?)", [$request->fechaDesde, $request->fechaHasta, $request->tipo]);
                return DataTables::of($tabla)->make(true);
            } else {
                return response()->json(['success' => false, 'message' => 'tiene que proporcionar fecha desde y fecha hasta.'], 400);
            }
        }
    }

    public function buscarFacturasCompras(Request $request)
    {
        if ($request->ajax()) {
            if (isset($request->fechaDesde) && isset($request->fechaHasta)) {
                $tabla = DB::Select("CALL getExamenesCompra(?,?,?,?,?)", [$request->fechaDesde, $request->fechaHasta, $request->idProfesional, $request->nroFacturaDesde, $request->nroFacturaHasta]);
                return DataTables::of($tabla)->make(true);
            } else {
                return response()->json(['success' => false, 'message' => 'tiene que proporcionar fecha desde y fecha hasta.'], 400);
            }
        }
    }

    public function crearFacturaCompra($id)
    {
        $profesional = Profesional::where('id', $id)->first();
        return view('layouts.facturaCompra.crearFacturaCompra', ['profesional' => $profesional]);
    }

    public function editarFacturaCompra($id)
    {
        $facturaCompra = FacturaCompra::find($id);
        $profesional = Profesional::where('id', $facturaCompra->IdProfesional)->first();
        $pago = $profesional->Pago == 1 ? 'Hora' : 'No Hora';

        $fechasPrestaciones = DB::Select("CALL getPrestacionDesdeHastaFacturaCompra(?)", [$facturaCompra->Id]);
        $fechaDesde = $fechasPrestaciones[1]->Fecha ?? null;
        $fechaHasta = $fechasPrestaciones[0]->Fecha ?? null;
        return view('layouts.facturaCompra.editarFactura', ['profesional' => $profesional, 'facturaCompra' => $facturaCompra, 'pago' => $pago, 'fechasPrestaciones' => $fechasPrestaciones, 'fechaDesde' => $fechaDesde, 'fechaHasta' => $fechaHasta]);
    }

    public function eliminarFacturaCompra(Request $request)
    {
        if ($request->ajax()) {
            if (isset($request->idFactura)) {
                $factura = FacturaCompra::find($request->idFactura);
                if ($factura) {
                    $items = ItemsFacturaCompra::where('IdFactura', $factura->Id)->get();
                    if ($items->count() == 0) {
                        $factura->Baja = 1;
                        $factura->save();
                        return response()->json(['success' => true, 'message' => 'Factura de compra eliminada con exito.'], 200);
                    } else {
                        return response()->json(['success' => false, 'message' => 'No se puede eliminar la factura de compra porque tiene items asociados.'], 400);
                    }
                } else {
                    return response()->json(['success' => false, 'message' => 'Factura de compra no encontrada.'], 404);
                }
            } else {
                return response()->json(['success' => false, 'message' => 'tiene que proporcionar id factura.'], 400);
            }
        }
    }

    //alta de factura

    public function listarExamenesEfector(Request $request)
    {
        if ($request->ajax()) {
            if (isset($request->idProfesional)) {
                $fechaDesde = isset($request->fechaDesde) ? $request->fechaDesde : null;
                $fechaHasta = isset($request->fechaHasta) ? $request->fechaHasta : null;
                $tabla = DB::Select("CALL getExamenesEfectorFacturar(?,?,?)", [$request->idProfesional, $fechaDesde, $fechaHasta]);
                return DataTables::of($tabla)->make(true);
            } else {
                return response()->json(['success' => false, 'message' => 'tiene que proporcionar id profesional, fecha desde y fecha hasta.'], 400);
            }
        }
    }

    public function listarExamenesInformador(Request $request)
    {
        if ($request->ajax()) {
            if (isset($request->idProfesional)) {
                $fechaDesde = isset($request->fechaDesde) ? $request->fechaDesde : null;
                $fechaHasta = isset($request->fechaHasta) ? $request->fechaHasta : null;
                $tabla = DB::Select("CALL getExamenesInformadorFacturar(?,?,?)", [$request->idProfesional, $fechaDesde, $fechaHasta]);
                return DataTables::of($tabla)->make(true);
            } else {
                return response()->json(['success' => false, 'message' => 'tiene que proporcionar id profesional, fecha desde y fecha hasta.'], 400);
            }
        }
    }

    public function listarExamenesFacturaEfector(Request $request)
    {
        if ($request->ajax()) {
            if (isset($request->idFactura)) {
                $tabla = DB::Select("CALL getExamenesFacturaCompraEfector(?)", [$request->idFactura]);
                return DataTables::of($tabla)->make(true);
            } else {
                return response()->json(['success' => false, 'message' => 'tiene que proporcionar id factura.'], 400);
            }
        }
    }

    public function listarExamenesFacturaInformador(Request $request)
    {
        if ($request->ajax()) {
            if (isset($request->idFactura)) {
                $tabla = DB::Select("CALL getExamenesFacturaCompraInformador(?)", [$request->idFactura]);
                return DataTables::of($tabla)->make(true);
            } else {
                return response()->json(['success' => false, 'message' => 'tiene que proporcionar id factura.'], 400);
            }
        }
    }

    public function cantidadExamenesEfector(Request $request)
    {
        if ($request->ajax()) {
            if (isset($request->idProfesional)) {
                $fechaDesde = isset($request->fechaDesde) ? $request->fechaDesde : null;
                $fechaHasta = isset($request->fechaHasta) ? $request->fechaHasta : null;
                $tabla = DB::Select("CALL getCantidadExamenesEfectorFacturar(?,?,?)", [$request->idProfesional, $fechaDesde, $fechaHasta]);
                return DataTables::of($tabla)->make(true);
            } else {
                return response()->json(['success' => false, 'message' => 'tiene que proporcionar id profesional, fecha desde y fecha hasta.'], 400);
            }
        }
    }

    public function cantidadExamenesInformador(Request $request)
    {
        if ($request->ajax()) {
            if (isset($request->idProfesional)) {
                $fechaDesde = isset($request->fechaDesde) ? $request->fechaDesde : null;
                $fechaHasta = isset($request->fechaHasta) ? $request->fechaHasta : null;
                $tabla = DB::Select("CALL getCantidadExamenesInformadorFacturar(?,?,?)", [$request->idProfesional, $fechaDesde, $fechaHasta]);
                return DataTables::of($tabla)->make(true);
            } else {
                return response()->json(['success' => false, 'message' => 'tiene que proporcionar id profesional, fecha desde y fecha hasta.'], 400);
            }
        }
    }

    public function facturar(Request $request)
    {
        try {
            if ($request->ajax()) {
                $mensaje = "";
                if (isset($request->idProfesional)) {
                    $mensaje .= "tiene que proporcionar id profesional. ";
                }

                if (isset($request->idsItemsEfectores) || isset($request->todosItemsEfectores) || isset($request->idsItemsInformador) || isset($request->todosItemsInformador)) {
                    $mensaje .= "tiene que proporcionar ids items efectores. ";
                }

                if (isset($request->TipoFactura)) {
                    $mensaje .= "tiene que proporcionar tipo factura. ";
                }

                if (isset($request->SucursalFactura)) {
                    $mensaje .= "tiene que proporcionar sucursal factura. ";
                }

                if (isset($request->NroFactura)) {
                    $mensaje .= "tiene que proporcionar nro factura. ";
                }

                if ($mensaje != "") {
                    //cargamos los diferentes tipos de facturas
                    $idFacturaCompra = FacturaCompra::max('Id') + 1;
                    $compra = FacturaCompra::create([
                        'Id' => $idFacturaCompra,
                        'Tipo' => $request->TipoFactura,
                        'Sucursal' => $request->SucursalFactura,
                        'NroFactura' => $request->NroFactura,
                        'Fecha' => now(),
                        'IdProfesional' => $request->idProfesional,
                        'Obs' => $request->Obs == null ? '' : $request->Obs
                    ]);
                    //cargamos items facturas de efectores
                    $fechaDesde = isset($request->fechaDesde) ? $request->fechaDesde : null;
                    $fechaHasta = isset($request->fechaHasta) ? $request->fechaHasta : null;

                    if (isset($request->todosItemsEfectores) && $request->todosItemsEfectores == true) {
                        $itemsPrestacion = DB::Select("CALL getExamenesInformadorFacturar(?,?,?)", [$request->idProfesional, $fechaDesde, $fechaHasta]);
                        $idsPrestaciones = array_map(fn($p) =>  $p->Id, $itemsPrestacion);
                        $this->facturarItem($idFacturaCompra, $idsPrestaciones);
                    } else if (isset($request->idsItemsEfectores) && count($request->idsItemsEfectores) > 0) {
                        $this->facturarItem($idFacturaCompra, $request->idsItemsEfectores);
                    }

                    if (isset($request->todosItemsInformadores) && $request->todosItemsInformadores == true) {
                        $itemsPrestacion = DB::Select("CALL getExamenesInformadorFacturar(?,?,?)", [$request->idProfesional, $fechaDesde, $fechaHasta]);
                        $idsPrestaciones = array_map(fn($p) =>  $p->Id, $itemsPrestacion);
                        $this->facturarItem2($idFacturaCompra, $idsPrestaciones);
                    } else if (isset($request->idsItemsInformador) && count($request->idsItemsInformador) > 0) {
                        $this->facturarItem2($idFacturaCompra, $request->idsItemsInformador);
                    }
                    return response()->json(['success' => true, 'message' => 'Factura de compra creada con exito.'], 200);
                } else {
                    return response()->json(['success' => false, 'message' => $mensaje], 400);
                }
            }
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error al crear la factura de compra: ' . $e->getMessage()], 500);
        }
    }

    function facturarItem($idFacturaCompra, $items)
    {
        foreach ($items as $itemPrestacion) {
            $idItemFacturaCompra = ItemsFacturaCompra::max('Id') + 1;

            ItemsFacturaCompra::create([
                'Id' => intval($idItemFacturaCompra),
                'IdFactura' => intval($idFacturaCompra),
                'IdItemPrestacion' => $itemPrestacion
            ]);

            ItemPrestacion::find($itemPrestacion)
                ->update([
                    'NroFactCompra' => intval($idFacturaCompra),
                    'FechaPagado' => now()
                ]);
        }
    }

    function facturarItem2($idFacturaCompra, $items)
    {
        foreach ($items as $itemPrestacion) {
            $idItemFacturaCompra = ItemsFacturaCompra::max('Id') + 1;

            ItemsFacturaCompra2::create([
                'Id' => intval($idItemFacturaCompra),
                'IdFactura' => intval($idFacturaCompra),
                'IdItemPrestacion' => $itemPrestacion
            ]);

            ItemPrestacion::find($itemPrestacion)
                ->update([
                    'NroFactCompra2' => intval($idFacturaCompra),
                    'FechaPagado2' => now()
                ]);
        }
    }

    //edicion de factura

    public function editarFactura(Request $request)
    {
        try {
            if ($request->ajax()) {
                $mensaje = "";
                if (!isset($request->idFactura)) {
                    $mensaje .= "tiene que proporcionar id factura. ";
                }

                if (!isset($request->TipoFactura)) {
                    $mensaje .= "tiene que proporcionar tipo factura. ";
                }

                if (!isset($request->SucursalFactura)) {
                    $mensaje .= "tiene que proporcionar sucursal factura. ";
                }

                if (!isset($request->NroFactura)) {
                    $mensaje .= "tiene que proporcionar nro factura. ";
                }

                if(!isset($request->Fecha)) {
                    $mensaje .= "tiene que proporcionar fecha factura. ";
                }

                if ($mensaje != "") {
                    return response()->json(['success' => false, 'message' => $mensaje], 400);
                } else {
                    $factura = FacturaCompra::find($request->idFactura);
                    $factura->Tipo = $request->TipoFactura;
                    $factura->Sucursal = $request->SucursalFactura;
                    $factura->NroFactura = $request->NroFactura;
                    $factura->Fecha = $request->Fecha;
                    $factura->Obs = $request->Obs == null ? '' : $request->Obs;
                    $factura->save();

                    return response()->json(['success' => true, 'message' => 'Factura de compra editada con exito.'], 200);
                }
            }
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error al editar la factura de compra: ' . $e->getMessage()], 500);
        }
    }

    public function eliminarItemFacturaCompraEfector(Request $request)
    {
        try {
            if ($request->ajax()) {
                if (isset($request->idItemFactura)) {
                    $eliminado = $this->eliminarItemEfector($request->idItemFactura);
                    if ($eliminado) {
                        return response()->json(['success' => true, 'message' => 'Item de factura de compra eliminado con exito.'], 200);
                    } else {
                        return response()->json(['success' => false, 'message' => 'Item de factura de compra no encontrado.'], 404);
                    }
                } else {
                    return response()->json(['success' => false, 'message' => 'tiene que proporcionar id item factura.'], 400);
                }
            }
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error al eliminar el item de la factura de compra: ' . $e->getMessage()], 500);
        }
    }

    public function eliminarItemsFacturaCompraEfectorMasivo(Request $request)
    {
        try {
            if ($request->ajax()) {
                if (isset($request->idsItemsFactura)) {
                    foreach ($request->idsItemsFactura as $idItemFactura) {
                        $this->eliminarItemEfector($idItemFactura);
                    }
                    return response()->json(['success' => true, 'message' => 'Items de factura de compra eliminados con exito.'], 200);
                } else {
                    return response()->json(['success' => false, 'message' => 'tiene que proporcionar ids items factura.'], 400);
                }
            }
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error al eliminar los items de la factura de compra: ' . $e->getMessage()], 500);
        }
    }

    private function eliminarItemEfector($idItemFactura)
    {
        $itemFactura = ItemsFacturaCompra::find($idItemFactura);
        if ($itemFactura) {
            $itemPrestacion = ItemPrestacion::find($itemFactura->IdItemPrestacion);
            if ($itemPrestacion) {
                $itemPrestacion->NroFactCompra = null;
                $itemPrestacion->FechaPagado = null;
                $itemPrestacion->save();
            }
            $itemFactura->delete();
            return true;
        } else {
            return false;
        }
    }

    public function eliminarItemFacturaCompraInformador(Request $request)
    {
        try {
            if ($request->ajax()) {
                if (isset($request->idItemFactura)) {
                    $eliminado = $this->eliminarItemInformador($request->idItemFactura);
                    if ($eliminado) {
                        return response()->json(['success' => true, 'message' => 'Item de factura de compra eliminado con exito.'], 200);
                    } else {
                        return response()->json(['success' => false, 'message' => 'Item de factura de compra no encontrado.'], 404);
                    }
                } else {
                    return response()->json(['success' => false, 'message' => 'tiene que proporcionar id item factura.'], 400);
                }
            }
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error al eliminar el item de la factura de compra: ' . $e->getMessage()], 500);
        }
    }

    public function eliminarItemsFacturaCompraInformadorMasivo(Request $request)
    {
        try {
            if ($request->ajax()) {
                if (isset($request->idsItemsFactura)) {
                    foreach ($request->idsItemsFactura as $idItemFactura) {
                        $this->eliminarItemInformador($idItemFactura);
                    }
                    return response()->json(['success' => true, 'message' => 'Items de factura de compra eliminados con exito.'], 200);
                } else {
                    return response()->json(['success' => false, 'message' => 'tiene que proporcionar ids items factura.'], 400);
                }
            }
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error al eliminar los items de la factura de compra: ' . $e->getMessage()], 500);
        }
    }

    private function eliminarItemInformador($idItemFactura)
    {
        $itemFactura = ItemsFacturaCompra2::find($idItemFactura);
        if ($itemFactura) {
            $itemPrestacion = ItemPrestacion::find($itemFactura->IdItemPrestacion);
            if ($itemPrestacion) {
                $itemPrestacion->NroFactCompra2 = null;
                $itemPrestacion->FechaPagado2 = null;
                $itemPrestacion->save();
            }
            $itemFactura->delete();
            return true;
        } else {
            return false;
        }
    }

    public function imprimirReporte(Request $request){
        if($request->ajax()){
            if(isset($request->idFactura)){
                //Llamar al servicio de reportes para generar el PDF
                $paramsTitulo = [
                    'detalles' => 'Factura Compra',
                    'id' => $request->idFactura
                ];
                $paramsSubtitulo = [];
                $paramsCuerpo = ['id' =>$request->idFactura];

                return $this->reporteService->generarReporte(
                    null,
                    null,
                    FacturaCompraCuerpo::class,
                    null,
                    "guardar",
                    storage_path('app/public/archivo.pdf'),
                    $request->idFactura,
                    $paramsTitulo, 
                    $paramsSubtitulo,
                    $paramsCuerpo,
                    null,
                    false
                );
            }else{
                return response()->json(['success' => false, 'message' => 'tiene que proporcionar id factura.'], 400);
            }
        }
    }

    public function exportarExcel(Request $request){
        if($request->ajax()){
            if (isset($request->fechaDesde) && isset($request->fechaHasta)) {
                $query = DB::Select("CALL getExamenesCompra(?,?,?,?,?)", [$request->fechaDesde, $request->fechaHasta, $request->idProfesional, $request->nroFacturaDesde, $request->nroFacturaHasta]);
                $reporte = $this->reporteExcel->crear('facturaCompra');
                return $reporte->generar($query);
            } else {
                return response()->json(['success' => false, 'message' => 'tiene que proporcionar fecha desde y fecha hasta.'], 400);
            }
        }
    }

    public function exportarExcelIndividual(Request $request){
        if($request->ajax()){
            if (isset($request->idFactura)) {
                
                $facturaCompra = FacturaCompra::find($request->idFactura);
                $profesional = Profesional::where('id', $facturaCompra->IdProfesional)->first();

                $examenesFacturaEfector = DB::Select("CALL getExamenesFacturaCompraEfector(?)", [$request->idFactura]);
                $examenesFacturaInformador = DB::Select("CALL getExamenesFacturaCompraInformador(?)", [$request->idFactura]);

                $cantidadExamenesFacturaEfector = DB::Select("CALL getCantidadExamenesFacturaCompraEfector(?)", [$request->idFactura]);
                $cantidadExamenesFacturaInformador = DB::Select("CALL getCantidadExamenesFacturaCompraInformador(?)", [$request->idFactura]);

                $reporte = $this->reporteExcel->crear('FacturaCompraIndivisual');
                return $reporte->generar($examenesFacturaEfector, $profesional, $facturaCompra, $examenesFacturaInformador, $cantidadExamenesFacturaEfector, $cantidadExamenesFacturaInformador );
            } else {
                return response()->json(['success' => false, 'message' => 'tiene que proporcionar fecha desde y fecha hasta.'], 400);
            }
        }
    }
}