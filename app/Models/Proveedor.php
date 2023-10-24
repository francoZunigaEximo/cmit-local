<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Proveedor extends Model
{
    use HasFactory;

    protected $table = 'proveedores';

    protected $primaryKey = 'Id';

    protected $fillable = [
        'Id',
        'Nombre',
        'Telefono',
        'Direccion',
        'IdLocalidad',
        'Inactivo',
        'Min',
        'PR',
        'Multi',
        'MultiE',
        'InfAdj',
        'Externo',
        'Obs'
    ];

    public $timestamps = false;

    public function profesional()
    {
        return $this->hasMany(Profesional::class, 'IdProveedor', 'Id');
    }

    public function profesionalProv()
    {
        return $this->hasMany(ProfesionalProv::class, 'IdProv', 'Id');
    }
    
    public function examenes()
    {
        return $this->hasOne(Examen::class, ['IdProveedor', 'IdProveedor2'], 'Id');
    }

    public function localidad()
    {
        return $this->hasOne(Localidad::class, 'Id', 'IdLocalidad');
    }
}