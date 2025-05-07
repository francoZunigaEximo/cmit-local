<?php

namespace App\Http\Controllers;

use App\Models\PaqueteEstudio;
use App\Models\PaqueteFacturacion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Yajra\DataTables\DataTables;

class PaquetesController extends Controller
{
    public function index()
    {
        
        return view('layouts.paquetes.index');
    }

    public function searchExamenes(Request $request){
        if ($request->ajax()) {
            $query = $this->buildQuery($request);
            return DataTables::of($query)->make(true);
        }
    }

    private function buildQuery(Request $request){
        return PaqueteEstudio::where('Nombre', 'LIKE', '%'.$request->buscar.'%');
    }

    public function crearPaqueteExamen(){
        return view('layouts.paquetes.create');
    }
}
