<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Estudio extends Model
{
    use HasFactory;

    protected $table = 'estudios';

    protected $primaryKey = 'Id';

    protected $fillable = [
        'Id',
        'Nombre',
        'Descripcion'
    ];

    public $timestamps = false;

    public function examen()
    {
        return $this->hasOne(Examen::class, 'IdEstudio', 'Id');
    }
}
