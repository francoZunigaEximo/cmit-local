<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;

class ParametroReporte extends Model
{
    use HasFactory;

    protected $table = "descripcion_parametro";

    protected $primaryKey = "id";

    protected $fillable = [
        'titulo',
        'descripcion',
        'modulo_id',
        'IdEntidad',
        ];

    public $timestamps = false;

    public static function getListado(int $id, string $modulo)
    {
        $idModulo = self::getIdParametro($modulo);
        return ParametroReporte::where('IdEntidad', $id)->where('modulo_id', $idModulo->id)->get();
    }

    public static function guardar(int $idEntidad, string $modulo, string $titulo, string $descripcion): JsonResponse
    {
        $modulo_id = self::getIdParametro($modulo);

        ParametroReporte::create([
            'titulo' => $titulo,
            'descripcion' => $descripcion,
            'modulo_id' => $modulo_id->id,
            'IdEntidad' => $idEntidad
        ]);
        
        return response()->json(['message' => 'El parametro fue eliminado correctamente'], 201);
    }

    public static function eliminar(int $id) 
    {
        $query = ParametroReporte::where('id', $id)->first();

        if(empty($query))  {
            return response()->json(['message' => 'El parametro que desea eliminar no existe'], 500);
        }

        $query->delete();

        return response()->json(['message' => 'El parametro fue eliminado correctamente'], 204);
    }

    public static function actualizar(string $titulo, string $descripcion, int $id): JsonResponse
    {
        if(empty($titulo) && empty($descripcion)) {
            return response()->json(['message' => 'La descripción y el titulo no pueden estar vacíos'], 500);
        }

        if(empty($id)) {
            return response()->json(['message' => 'el identificador se encuentra vacío'], 404);
        }

        ParametroReporte::where('id', $id)->update([
            'titulo' => $titulo,
            'descripcion' => $descripcion
        ]);

        return response()->json(['message' => 'Se ha actualizado el parametro correctamente'], 200);
    }


    private static function getIdParametro(string $tipo)
    {
        return ModuloParametro::where('nombre', $tipo)->first();
    }
}
