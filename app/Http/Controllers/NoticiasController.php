<?php

namespace App\Http\Controllers;

use App\Helpers\FileHelper;
use App\Models\Noticia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Traits\CheckPermission;


class NoticiasController extends Controller 
{
    use CheckPermission;

	public function index(): mixed
    {
        if(!$this->hasPermission("noticias_show")) {
            abort(403);
        }

    	$noticia = Noticia::find(1);
        return view('layouts.noticias.index', with([
        	'noticia' 			=> $noticia,
        	'estiloSubitutlo' 	=>  $noticia->Urgente ? "color:#f44336" : ""
        ]));
    }

    public function edit(Noticia $noticia): mixed
    {
        if(!$this->hasPermission("noticias_edit")) {
            abort(403);
        }

        return view('layouts.noticias.edit', with(['noticia' => $noticia]));
    }

    public function update(Request $request, Noticia $noticia)
    {
        if(!$this->hasPermission("noticias_edit")) {
            return response()->json(['msg' => 'No tiene permisos'], 403);
        }

        $noticia 			= Noticia::find(1);
        $noticia->Titulo 	= $request->Titulo;
        $noticia->Subtitulo = $request->Subtitulo;
        $noticia->Texto 	= $request->Texto;
        $noticia->Urgente 	= $request->Urgente ? 1 : 0;

        if ($request->hasFile('Ruta')) {

            $fotoExistente =  FileHelper::getFileUrl('lectura') . $request->Ruta;
            if (file_exists($fotoExistente)) {
                unlink($fotoExistente);
            }

            $fileName = 'NOTICIA_.' . $request->Ruta->extension();
            $path = FileHelper::getFileUrl('escritura')."/Noticias";

            if (!file_exists($path)) {
                mkdir($path, 0755, true);
            }

            $request->Ruta->move($path, $fileName);
            $noticia->Ruta = $fileName;
        }

        $noticia->push();

        return back();

    }
}