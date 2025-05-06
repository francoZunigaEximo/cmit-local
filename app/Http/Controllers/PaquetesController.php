<?php

namespace App\Http\Controllers;

use App\Models\PaqueteFacturacion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class PaquetesController extends Controller
{
    public function index()
    {
        
        return view('layouts.paquetes.index');
    }
}
