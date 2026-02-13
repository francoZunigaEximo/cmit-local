<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExamenCuenta extends Model
{
    use HasFactory;

    protected $table = 'pagosacuenta';

    protected $primaryKey = 'Id';

    protected $fillable = [
        'Id',
        'IdEmpresa',
        'Fecha',
        'Tipo',
        'Suc',
        'Nro',
        'Obs',
        'Pagado',
        'FechaP'
    ];

    public $timestamps = false;

    public function empresa()
    {
        return $this->hasOne(Cliente::class, 'Id', 'IdEmpresa');
    }

    public function prestacion()
    {
        return $this->belongsToMany(Prestacion::class, 'pagosacuenta_it', 'IdPago', 'IdPrestacion');
    }

    public function examen()
    {
        return $this->belongsToMany(Examen::class, 'pagosacuenta_it', 'IdPago', 'IdExamen');
    }
}
