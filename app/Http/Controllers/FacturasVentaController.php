<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FacturaDeVenta;

class FacturasVentaController extends Controller
{
    
    public function getFactura(Request $request)
    {
        $factura = FacturaDeVenta::where('IdPrestacion', $request->Id)->first(['Tipo', 'Sucursal', 'NroFactura']);

        if($factura)
        {
            return response()->json(['factura' => $factura]);
        }
    }
}
