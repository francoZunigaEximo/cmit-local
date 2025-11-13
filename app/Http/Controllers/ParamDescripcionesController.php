<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ParametroReporte;
use App\Models\ModuloParametro;
use Illuminate\Http\JsonResponse;

class ParamDescripcionesController extends Controller
{
    public function listado(Request $request)
    {
        $idModulo = $this->getModulo($request->modulo);
        $query = ParametroReporte::where('IdEntidad', $request->IdEntidad)->where('modulo_id', $idModulo->id)->get();

        return response()->json($query);
    }

    public function guardar(Request $request): JsonResponse
    {
        $modulo_id = $this->getModulo($request->modulo);

        ParametroReporte::create([
            'titulo' => $request->titulo,
            'descripcion' => $request->descripcion,
            'modulo_id' => $modulo_id->id,
            'IdEntidad' => $request->IdEntidad
        ]);
        
        return response()->json(['message' => 'El parametro fue guardado correctamente'], 201);
    }

    public function eliminar(Request $request): JsonResponse
    {
        $query = ParametroReporte::where('id', $request->id)->first();

        if(empty($query))  {
            return response()->json(['message' => 'El parametro que desea eliminar no existe'], 500);
        }

        $query->delete();
        return response()->json(['message' => 'El parametro fue eliminado correctamente'], 200);
    }

    public function actualizar(Request $request): JsonResponse
    {
        if(empty($request->titulo) && empty($request->descripcion)) {
            return response()->json(['message' => 'La descripción y el titulo no pueden estar vacíos'], 500);
        }

        if(empty($request->id)) {
            return response()->json(['message' => 'el identificador se encuentra vacío'], 404);
        }

        ParametroReporte::where('id', $request->id)->update([
            'titulo' => $request->titulo,
            'descripcion' => $request->descripcion
        ]);

        return response()->json(['message' => 'Se ha actualizado el parametro correctamente'], 200);
    }

    public function getParametroId(Request $request): JsonResponse
    {
        $query = ParametroReporte::find($request->id);

        if(empty($query)) {
            return response()->json(['message' => 'No existe el parametro que desea eliminar'], 404);
        }

        return response()->json($query);
    }


    private function getModulo(string $tipo)
    {
        return ModuloParametro::where('nombre', $tipo)->first();
    }

}
