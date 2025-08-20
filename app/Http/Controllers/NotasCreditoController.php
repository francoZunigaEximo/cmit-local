<?php

namespace App\Http\Controllers;

use App\Helpers\Tools;
use App\Models\Auditor;
use App\Models\Cliente;
use App\Models\ItemPrestacion;
use App\Models\ItemsFacturaVenta;
use App\Models\NotaCredito;
use App\Models\NotaCreditoIt;
use App\Models\Prestacion;
use App\Services\Reportes\ReporteService;
use App\Services\ReportesExcel\ReporteExcel;
use Clue\React\Redis\Client;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;

class NotasCreditoController extends Controller
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
        $this->sendPath = storage_path('app/public/temp/cmit-' . Tools::randomCode(15) . '-informe.pdf');
        $this->fileNameExport = 'reporte-' . Tools::randomCode(15);
        $this->tempFile = 'app/public/temp/file-';
        $this->reporteExcel = $reporteExcel;
    }

    public function index(): mixed
    {
        return view('layouts.notasCredito.index');
    }

    public function getItemsAnulados($id)
    {
        $cliente = Cliente::find($id);
        return view('layouts.notasCredito.itemsanulados', ['idEmpresa' => $id, 'cliente' => $cliente]);
    }

    public function editarNotasCredito($id)
    {
        $notaCredito = NotaCredito::find($id);
        $cliente = Cliente::find($notaCredito->IdEmpresa);
        if (!$notaCredito) {
            return redirect()->route('notasCredito.index')->with('error', 'Nota de crédito no encontrada.');
        }
        return view('layouts.notasCredito.editarnotacredito', ['notaCredito' => $notaCredito, 'cliente' => $cliente]);
    }

    public function getClientes(Request $request)
    {
        if ($request->ajax()) {
            $query =  $this->buildQueryClientes($request);

            return DataTables::of($query)->make(true);
        }
    }

    public function buildQueryClientes(Request $request)
    {
        //cuando tenga datos completo esta parte
        $query = DB::table('prestaciones')
            ->join('clientes', 'prestaciones.IdEmpresa', '=', 'clientes.Id')
            ->join('itemsprestaciones', 'prestaciones.Id', '=', 'itemsprestaciones.IdPrestacion')
            ->leftJoin('notascredito_it', 'itemsprestaciones.Id', '=', 'notascredito_it.IdIP')
            ->where('prestaciones.Id', '<>', 0)
            ->where('prestaciones.Facturado', '=', 1)
            ->where('itemsprestaciones.Anulado', '=', 1)
            ->whereNull('notascredito_it.Id')
            ->groupBy('clientes.Id', 'clientes.ParaEmpresa', 'clientes.Identificacion')
            ->select('clientes.Id as Id', 'clientes.ParaEmpresa as Cliente', 'clientes.Identificacion as CUIT', DB::raw('COUNT(itemsprestaciones.Id) as TotalItems'));

        if ($request->has('IdEmpresa') && $request->IdEmpresa != 0) {
            $query->where('prestaciones.IdEmpresa', '=', $request->IdEmpresa);
        }

        if ($request->has('CUIT') && $request->CUIT != '') {
            $query->where('clientes.Identificacion', 'like', '%' . $request->CUIT . '%');
        }

        if ($request->has('fechaDesde') && $request->fechaDesde != '') {
            $query->where('itemprestaciones.FechaAnulado', '>=', $request->fechaDesde);
        }

        if ($request->has('fechaHasta') && $request->fechaHasta != '') {
            $query->where('itemprestaciones.FechaAnulado', '<=', $request->fechaHasta);
        }

        return $query;
    }

    public function checkNotaCredito(Request $request)
    {
        $query = NotaCredito::where('Id', $request->Id)->exits();
        return response()->json($query, 200);
    }

    public function getItemsFacturaVenta(Request $request)
    {
        $items_notas = DB::table('notascredito_it')
            ->select('notascredito_it.IdIP')
            ->where('notascredito_it.Baja', 0);

        $items_facturas = DB::table('itemsprestaciones')
            ->join('prestaciones', 'itemsprestaciones.IdPrestacion', '=', 'prestaciones.Id')
            ->join('examenes', 'itemsprestaciones.IdExamen', '=', 'examenes.Id')
            ->join('pacientes', 'prestaciones.IdPaciente', '=', 'pacientes.Id')
            ->join('facturasventa', 'itemsprestaciones.NumeroFacturaVta', '=', 'facturasventa.Id')
            ->whereNotIn('itemsprestaciones.Id', $items_notas)
            ->where('prestaciones.IdEmpresa', '=', $request->IdEmpresa)
            ->where('prestaciones.Facturado', '=', 1)
            //->where('itemsprestaciones.NumeroFacturaVta', '>', 0)
            ->where('itemsprestaciones.Anulado', '=', 1)
            ->select('itemsprestaciones.Id as Id', 'prestaciones.Cerrado as cerrado','facturasventa.NroFactura as NroFactura', 'facturasventa.Sucursal as Sucursal', 'facturasventa.Tipo as Tipo', 'itemsprestaciones.FechaAnulado as FechaAnulado', 'prestaciones.Id as Prestacion', 'examenes.Nombre as Examen', 'pacientes.Nombre as Paciente');

        if ($request->has('fechaDesde') && $request->fechaDesde != '') {
            $items_facturas->where('itemsprestaciones.FechaAnulado', '>=', $request->fechaDesde);
        }

        if ($request->has('fechaHasta') && $request->fechaHasta != '') {
            $items_facturas->where('itemsprestaciones.FechaAnulado', '<=', $request->fechaHasta);
        }

        if ($request->has('NroFactura') && $request->NroFactura != '') {
            $items_facturas->where('itemsprestaciones.NumeroFacturaVta', 'like', '%' . $request->NroFactura . '%');
        }

        if ($request->has('IdPrestacion') && $request->IdPrestacion != 0) {
            $items_facturas->where('prestaciones.Id', '=', $request->IdPrestacion);
        }

        return DataTables::of($items_facturas)->make(true);
    }

    public function reactivarItem(Request $request)
    {
        $id = $request->id;
        $item = ItemPrestacion::find($id);
        $prestacion = Prestacion::find($item->IdPrestacion);
        
        if ($item && $prestacion->Cerrado == 0) {
            $item->Anulado = 0;
            $item->save();
            Auditor::setAuditoria($id, 8,4,Auth::user()->name);
            return response()->json(['success' => true, 'message' => 'Item reactivado correctamente.'], 200);
        } else if($prestacion->Cerrado == 1) {
            return response()->json(['success' => false, 'message' => 'La prestación está cerrada.'], 400);
        } else {
            return response()->json(['success' => false, 'message' => 'Item no encontrado.'], 404);
        }
    }

    public function crearNotaCredito(Request $request)
    {
        $Id = NotaCredito::max('Id') + 1;

        $cliente = Cliente::find($request->IdCliente);

        $notaCredito = NotaCredito::create([
            'Id' => $Id, // Asignar un nuevo ID
            'Estado' => 0, // Estado activo
            'FechaAnulado' => now(), // Fecha actual
            'Baja' => 0, // Baja
            'IdFactura' => 0, // Inicialmente no asociado a ninguna factura
            'IdP' => 0,
            'Tipo' => $request->Tipo, // Tipo de nota de crédito
            'Sucursal' => $request->Sucursal, // Sucursal por defecto
            'Nro' => $request->NroNotaCredito,
            'Fecha' => $request->Fecha,
            'IdEmpresa' => $request->IdCliente, // ID del cliente
            'TipoCliente' => $cliente->TipoCliente, // Tipo de cliente
            'TipoNC' => 1, // Tipo de nota de crédito (1 para anulación)
            'Obs' => $request->Observacion, // Observación de la nota de crédito
        ]);

        foreach ($request->items as $item) {
            $notaCreditoIt = new NotaCreditoIt();
            $notaCreditoIt->Id = NotaCreditoIt::max('Id') + 1; // Asignar un nuevo ID
            $notaCreditoIt->IdIP = $item;
            $notaCreditoIt->IdNC = $Id;
            $notaCreditoIt->Estado = 0; // Estado activo
            $notaCreditoIt->FechaAnulado = now();
            $notaCreditoIt->Baja = 0; // Baja
            $notaCreditoIt->save();
        }

        Auditor::setAuditoria($notaCredito->Id, 7,1,Auth::user()->name);

        return response()->json(['success' => true, 'message' => 'Nota de crédito creada correctamente.'], 200);
    }

    public function getNotas(Request $request)
    {
        if ($request->ajax()) {
            $query =  $this->buildQueryNotas($request);

            return DataTables::of($query)->make(true);
        }
    }

    public function buildQueryNotas(Request $request)
    {
        $notas = DB::table('notascredito')
            ->join('clientes', 'notascredito.IdEmpresa', '=', 'clientes.Id')
            ->where('notascredito.Baja', 0)
            ->select('notascredito.Id as Id', 'notascredito.Tipo as Tipo', 'notascredito.Sucursal as Sucursal', 'notascredito.Nro as NroNotaCredito', 'notascredito.Fecha as Fecha', 'clientes.ParaEmpresa as Empresa', 'clientes.Identificacion as CUIT', 'notascredito.Obs as Observacion')
            ->orderBy('notascredito.Fecha', 'desc');

        if ($request->has('IdEmpresa') && $request->IdEmpresa != 0) {
            $notas->where('notascredito.IdEmpresa', '=', $request->IdEmpresa);
        }

        if ($request->has('NroDesde') && $request->NroDesde != '') {
            $notas->where('notascredito.Nro', '>=', $request->NroDesde);
        }

        if ($request->has('NroHasta') && $request->NroHasta != '') {
            $notas->where('notascredito.Nro', '<=', $request->NroHasta);
        }

        if ($request->has('fechaDesde') && $request->fechaDesde != '') {
            $notas->where('notascredito.Fecha', '>=', $request->fechaDesde);
        }

        if ($request->has('fechaHasta') && $request->fechaHasta != '') {
            $notas->where('notascredito.Fecha', '<=', $request->fechaHasta);
        }

        return $notas;
    }

     public function exportClientesItemsAnuladosExcel(Request $request)
    {
        $query = $this->buildQueryClientes($request);
        $reporte = $this->reporteExcel->crear('clientesItemsAnulados');
        return $reporte->generar($query->get());
    }

    public function getItemsNotaCredito(Request $request)
    {
        $items_nota = DB::table('notascredito_it')
            ->join('itemsprestaciones', 'notascredito_it.IdIP', '=', 'itemsprestaciones.Id')
            ->join('prestaciones', 'itemsprestaciones.IdPrestacion', '=', 'prestaciones.Id')
            ->join('examenes', 'itemsprestaciones.IdExamen', '=', 'examenes.Id')
            ->join('clientes', 'prestaciones.IdEmpresa', '=', 'clientes.Id')
            ->join('facturasventa', 'itemsprestaciones.NumeroFacturaVta', '=', 'facturasventa.Id')
            ->where('notascredito_it.IdNC', $request->IdNC)
            ->where('notascredito_it.Baja', 0)
            ->select('notascredito_it.Id as Id', 'itemsprestaciones.Fecha as Fecha', 'prestaciones.Id as IdPrestacion', 'clientes.ParaEmpresa as Cliente', 'examenes.Nombre as Examen', 'itemsprestaciones.Id as IdItemPrestacion', 'facturasventa.NroFactura as NroFactura', 'facturasventa.Sucursal as Sucursal', 'facturasventa.Tipo as Tipo');

        return DataTables::of($items_nota)->make(true);
    }

    public function editarNotasCreditoPost(Request $request)
    {
        $notaCredito = NotaCredito::find($request->id);
        if (!$notaCredito) {
            return response()->json(['success' => false, 'message' => 'Nota de crédito no encontrada.'], 404);
        }

        $notaCredito->update([
            'Nro' => $request->NroNotaCredito,
            'Tipo' => $request->Tipo,
            'Sucursal' => $request->Sucursal,
            'Fecha' => $request->Fecha,
            'Obs' => $request->Observacion,
        ]);

        // Eliminar los items que se hayan marcado para eliminar
        if (isset($request->itemsEliminar) && is_array($request->itemsEliminar)) {
            foreach ($request->itemsEliminar as $itemId) {
                $notaCreditoIt = NotaCreditoIt::find($itemId);
                if ($notaCreditoIt) {
                    $notaCreditoIt->Baja = 1; // Marcar como eliminado
                    $notaCreditoIt->save();
                }
            }
        }

        return response()->json(['success' => true, 'message' => 'Nota de crédito actualizada correctamente.'], 200);
    }

    function eliminarNotaCredito(Request $request)
    {
        try {
            $id = $request->id;

            if (!$id) {
                return response()->json(['success' => false, 'message' => 'ID de nota de crédito no proporcionado.'], 400);
            }

            $control = $this->eliminarNota($id);
            if ($control == 0) {
                Auditor::setAuditoria($id, 7,3,Auth::user()->name);
                return response()->json(['success' => true, 'message' => 'Nota de crédito eliminada correctamente.'], 200);
            } else if ($control == 1) {
                return response()->json(['success' => false, 'message' => 'No se puede eliminar la nota de crédito porque tiene items asociados.'], 400);
            } else {
                return response()->json(['success' => false, 'message' => 'No se puede eliminar la nota de crédito porque tiene items asociados que están anulados.'], 400);
            }
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error al eliminar la nota de crédito: ' . $e->getMessage()], 500);
        }
    }

    private function eliminarNota($id)
    {
        $notaCredito = NotaCredito::find($id);
        if (!$notaCredito) {
            return 1;
        }
        $items_notas = NotaCreditoIt::where('IdNC', $id)->where('Baja', 0)->get();

        if ($items_notas->isEmpty()) {
            // Marcar la nota de crédito como eliminada
            $notaCredito->Baja = 1;
            $notaCredito->save();
            Auditor::setAuditoria($id, 7,3,Auth::user()->name);
            return 0;
        } else {
            return 2;
        }
    }

    public function exportDetalleNotaCreditoExcel(Request $request)
    {
        $query = $this->buildQueryNotas($request);
        $reporte = $this->reporteExcel->crear('notaCredito');
        return $reporte->generar($query->get());
    }

    public function eliminarNotaCreditoMasivo(Request $request)
    {
        $ids = $request->ids;

        if (empty($ids) || !is_array($ids)) {
            return response()->json(['success' => false, 'message' => 'IDs de notas de crédito no proporcionados.'], 400);
        }

        $result = ["eliminadas"=>0, "no_eliminadas"=>0, "no_encontradas"=>0];
        foreach ($ids as $id) {
            $control = $this->eliminarNota($id);
            if ($control == 0) {
                $result["eliminadas"]++;
            } else if ($control == 1) {
                $result["no_encontradas"]++;
            } else {
                $result["no_eliminadas"]++;
            }
            
            Auditor::setAuditoria($id, 7,3,Auth::user()->name);
        }
        return response()->json(['success' => true, 'result' => $result], 200);
    }
}
