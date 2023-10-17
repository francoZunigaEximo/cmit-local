<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use App\Models\Proveedor;

class ProveedoresController extends Controller
{
    public function getProveedores(Request $request)
    {
        $buscar = $request->buscar;

        $resultados = Cache::remember('proveedores' . $buscar, 5, function() use ($buscar){

            $proveedores = Proveedor::where('Nombre', 'LIKE', '%'. $buscar . '%')
            ->where('Inactivo', 0)
            ->where('Id', '<>', 0)
            ->get();

            $resultados = [];

            foreach($proveedores as $proveedor){
                $resultados[] = [
                    'id' => $proveedor->Id,
                    'text' => $proveedor->Nombre
                ];
            }
            return $resultados;
        });

        return response()->json(['proveedores' => $resultados]);
    }

    
}