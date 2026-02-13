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


}
