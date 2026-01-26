<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ModuloParametro extends Model
{
    use HasFactory;

    protected $table = "modulo_parametros";

    protected $primaryKey = "id";

    protected $fillable = [
        'nombre',
        ];

    public $timestamps = false;
}
