<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FacturaDeVenta;
use App\Traits\CheckPermission;
use PhpOffice\PhpSpreadsheet\Calculation\Statistical\Distributions\F;

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
        if(!$this->hasPermission("facturacion_create"))
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

    
    public function getFactura(Request $request)
    {
        $factura = FacturaDeVenta::where('IdPrestacion', $request->Id)->first(['Tipo', 'Sucursal', 'NroFactura']);

        if($factura)
        {
            return response()->json(['factura' => $factura]);
        }
    }
}
