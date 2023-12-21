<?php

namespace App\Http\Controllers;

use App\Models\Noticia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class NoticiasController extends Controller 
{
	public function index()
    {
    	$noticia = Noticia::find(1);
        return view('layouts.noticias.index', with([
        	'noticia' 			=> $noticia,
        	'estiloSubitutlo' 	=>  $noticia->Urgente ? "color:#f44336" : ""
        ]));
    }

    public function edit(Noticia $noticia): mixed
    {
        return view('layouts.noticias.edit', with(['noticia' => $noticia]));
    }

    public function update(Request $request, Noticia $noticia)
    {
        $noticia 			= Noticia::find(1);
        $noticia->Titulo 	= $request->Titulo;
        $noticia->Subtitulo = $request->Subtitulo;
        $noticia->Texto 	= $request->Texto;
        $noticia->Urgente 	= $request->Urgente ? 1 : 0;

        if ($request->hasFile('Ruta')) {

            $fotoExistente = 'public/noticias/' . $request->Ruta;
            if (Storage::exists($fotoExistente)) {
                Storage::delete($fotoExistente);
            }

            $fileName = 'NOTICIA.' . $request->Ruta->extension();
            $request->Ruta->storeAs('public/noticias', $fileName);

            $noticia->Ruta = $fileName;
        }

        $noticia->push();

        return back();

    }
}