<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PrestacionAtributo extends Model
{
    use HasFactory;

    protected $table = "prestaciones_atributos";

    public $fillable = [
        'Id',
        'IdPadre',
        'SinEval'
    ];

    public $timestamps = false;

    public $primaryKey = "Id";

    public function prestacion()
    {
        return $this->hasOne(Prestacion::class, 'Id', 'IdPadre');
    }
}
