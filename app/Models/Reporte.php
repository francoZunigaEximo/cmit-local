<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reporte extends Model
{
    use HasFactory;

    protected $table = "reportes";

    protected $primaryKey = "Id";

    protected $fillable = [
        'Id',
        'Nombre',
        'IdReporte',
        'Inactivo',
        'VistaPrevia'
    ];

    public $timestamps = false;

    public function examen()
    {
        return $this->hasOne(Examen::class, 'IdReporte', 'Id');
    }
}
