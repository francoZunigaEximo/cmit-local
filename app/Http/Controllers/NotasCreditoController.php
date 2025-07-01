<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\ItemsFacturaVenta;
use App\Models\NotaCredito;
use App\Models\NotaCreditoIt;
use App\Models\Prestacion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;

class NotasCreditoController extends Controller
{
    public function index(): mixed{
        $notasCredito = NotaCredito::all();
        return view('layouts.notasCredito.index');
    }

    public function getItemsAnulados($id)
    {
        return view('layouts.notasCredito.itemsanulados',['idEmpresa'=> $id]);
    }

    public function getClientes(Request $request)
    {
         if ($request->ajax()) {
            $query =  $this->buildQueryClientes();
            
            return DataTables::of($query)->make(true);
        }
    }

    public function buildQueryClientes(){
        //cuando tenga datos completo esta parte
        $prestacionesFacturadas = DB::table('prestaciones')
        ->select('prestaciones.IdEmpresa')
        ->join('facturasventa', 'prestaciones.Id', '=', 'facturasventa.IdPrestacion')
        ->join('itemsprestaciones','prestaciones.Id', '=', 'itemsprestaciones.IdPrestacion')
        ->where('prestaciones.Id', '<>', 0)
        ->where('itemsprestaciones.Anulado', '=', 1);
        
        $query = DB::table('clientes')
        ->whereIn('clientes.Id', $prestacionesFacturadas)
        ->select('clientes.Id as Id', 'clientes.ParaEmpresa as Cliente', 'clientes.Identificacion as CUIT');
        
        return $query;
    }

    public function checkNotaCredito(Request $request)
    {
        $query = NotaCredito::where('Id', $request->Id)->exits();
        return response()->json($query, 200); 
    }

    public function getItemsFacturaVenta(Request $request)
    {
        $items_facturas = DB::table('itemsprestaciones')
            ->join('prestaciones','itemsprestaciones.IdPrestacion', '=', 'prestaciones.Id')
            ->join('facturasventa', 'prestaciones.Id', '=', 'facturasventa.IdPrestacion')
            ->join('examenes', 'itemsprestaciones.IdExamen', '=', 'examenes.Id')
            ->join('pacientes', 'prestaciones.IdPaciente', '=', 'pacientes.Id')
            ->where('prestaciones.IdEmpresa', '=', $request->IdEmpresa)
            ->where('itemsprestaciones.Anulado', '=', 1)
            ->select('itemsprestaciones.Id as Id','itemsprestaciones.NumeroFacturaVta as NroFactura', 'itemsprestaciones.FechaPagado as FechaAnulado', 'prestaciones.Id as Prestacion', 'examenes.Nombre as Examen', 'pacientes.Nombre as Paciente');
        
        return DataTables::of($items_facturas)->make(true);
    }
}
