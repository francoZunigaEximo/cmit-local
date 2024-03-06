<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ExamenesCuentaController extends Controller
{
    public function index()
    {
        return view('layouts.examenesCuenta.index');
    }
}
