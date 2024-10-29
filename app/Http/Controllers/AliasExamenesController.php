<?php

namespace App\Http\Controllers;

use App\Models\AliasExamen;
use App\Models\Examen;
use Illuminate\Http\Request;
use App\Traits\CheckPermission;

class AliasExamenesController extends Controller
{
    use CheckPermission;

    public function getListadoExamenes(): mixed
    {
        if(!$this->hasPermission("examenes_show")) {
            abort(403);
        }

        return AliasExamen::whereNot('Id', 0)->orderBy('Id', 'DESC')->get();
    }

    public function saveAlias(Request $request): mixed
    {
        if(!$this->hasPermission("examenes_add")) {
            return response()->json(["msg" => "No tiene permisos"], 403);
        }

        if(empty($request->Nombre)) {
            return response()->json(['msg' => 'El nombre del alias no puede estar vacío'], 403);
        }

        AliasExamen::create([
            'Nombre' => $request->Nombre,
            'Descripcion' => $request->Descripcion
        ]);

        return response()->json(['msg' => 'El alias se ha agregado correctamente'], 201);
    }


    public function deleteAlias(Request $request): mixed
    {
        if(!$this->hasPermission("examenes_delete")) {
            return response()->json(["msg" => "No tiene permisos"], 403);
        }

        if(empty($request->Id)) {
            return response()->json(['msg' => 'El Id no puede encontrarse vacío'], 409);
        }
        
        $check = Examen::where('aliasexamen_id', $request->Id)->first();
        if($check) {
            return response()->json(['msg' => 'No se puede eliminar el alias porque se encuentra asociado a un exámen'], 409);
        }

        $delete = AliasExamen::find($request->Id);

        if($delete) {
            $delete->delete();
            return response()->json(['msg' => 'Se ha eliminado el alias correctamente']);
        }
    }

    public function getAliasSelect(Request $request)
    {
        return Examen::with('aliasExamen')->where('Id', $request->Id)->first();
    }



}
