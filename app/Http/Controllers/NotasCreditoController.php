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
        $items_facturas = DB::table('notascredito_it')
        ->join('itemsprestaciones', 'notascredito_it.IdIP', '=', 'itemsprestaciones.IdPrestacion')
        ->join('prestaciones','itemsprestaciones.IdPrestacion', '=', 'prestaciones.Id')
        ->where('prestaciones.Facturado', '=', 1)
        ->where('prestaciones.IdEmpresa', '=', $id)
        ->get();
        
        return view('layouts.notasCredito.itemsanulados',['items_facturas' => $items_facturas]);
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
        $prestacionesFacturadas = DB::table('prestaciones')->select('IdEmpresa')->where('prestaciones.Facturado','=', 1);
        
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
}
