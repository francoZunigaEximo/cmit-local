<?php

namespace App\Http\Controllers;

use App\Models\Localidad;
use Illuminate\Http\Request;

class LocalidadController extends Controller
{
    public function searchLocalidad(Request $request)
    {
        $query = Localidad::find($request->Id);

        return response()->json(['resultado' => $query]);
    }
}
