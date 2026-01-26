<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Parametro extends Model
{
    use HasFactory;

    protected $table = "parametros";

    protected $primaryKey = "Id";

    public $timestamps = false;

    public static function getMiEmpresa()
    {
        return Parametro::first();
    }
}
