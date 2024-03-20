<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Examen extends Model
{
    use HasFactory;

    protected $table = 'examenes';

    protected $primaryKey = 'Id';

    protected $fillable = [
        'Id',
        'IdEstudio',
        'Nombre',
        'Descripcion',
        'IdReporte',
        'IdProveedor',
        'IdProveedor2',
        'DiasVencimiento',
        'Inactivo',
        'Cod',
        'Cod2',
        'SinEsc',
        'Forma',
        'Ausente',
        'Devol',
        'Informe',
        'Adjunto',
        'NoImprime',
        'Cerrado',
        'Evaluador',
        'EvalCopia',
        'PI',
        'IdForm',
    ];

    public $timestamps = false;

    public function estudios()
    {
        return $this->hasOne(Estudio::class, 'Id', 'IdEstudio');
    }

    public function itemsprestacion()
    {
        return $this->hasOne(ItemPrestacion::class, 'IdExamen', 'Id');
    }

    public function proveedor1()
    {
        return $this->hasOne(Proveedor::class, 'Id', 'IdProveedor');
    }

    public function proveedor2()
    {
        return $this->hasOne(Proveedor::class, 'Id', 'IdProveedor2');
    }

    public function reportes()
    {
        return $this->hasOne(Reporte::class, 'Id', 'IdReporte');
    }

    public function examenCuenta()
    {
        return $this->belongToMany(ExamenCuenta::class, 'pagoacuenta_it', 'IdExamen', 'IdPago');
    }
}
