<?php

namespace App\Http\Controllers;

use App\Models\NotaCredito;
use Illuminate\Http\Request;

class NotasCreditoController extends Controller
{
    public function checkNotaCredito(Request $request)
    {
        $query = NotaCredito::where('Id', $request->Id)->exits();
        return response()->json($query, 200); 

    }
}
