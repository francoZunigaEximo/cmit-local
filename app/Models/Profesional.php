<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Profesional extends Model
{
    use HasFactory;

    protected $table = 'profesionales';

    protected $primaryKey = 'Id';

    protected $appends = ['NombreProfesional'];

    protected $fillable = [
        'Id',
        'IdProveedor',
        'TipoIdentificacion',
        'Identificacion',
        'TipoDocumento',
        'Documento',
        'Nombre',
        'Apellido',
        'Direccion',
        'IdLocalidad',
        'Provincia',
        'CP',
        'Firma',
        'Foto',
        'T1',
        'T2',
        'T3',
        'T4',
        'TLP',
        'TMP',
        'Pago',
        'wImage',
        'hImage',
        'InfAdj',
        'RegHis',
        'T5'
    ];

    public $timestamps = false;

    public function getNombreProfesionalAttribute()
    {
        return $this->Apellido . ' ' . $this->Nombre;
    }

    public function prestacion()
    {
        return $this->hasMany(Prestacion::class, 'IdEvaluador', 'Id');
    }

    public function proveedor()
    {
        return $this->hasOne(Proveedor::class, 'Id', 'IdProveedor');
    }

    public function profesionalProv()
    {
        return $this->hasMany(ProfesionalProv::class, 'IdProf', 'Id');
    }

    public function localidad()
    {
        return $this->hasOne(Localidad::class, 'Id', 'IdLocalidad');
    }

    public function telefono()
    {
        return $this->hasOne(Telefono::class, 'IdProfesional', 'Id');
    }

    public function user()
    {
        return $this->hasOne(User::class, 'IdProfesional', 'Id');
    }

    public function itemsPrestacion()
    {
        return $this->hasOne(ItemPrestacion::class, ['IdProfesional', 'IdProfesional2'], 'Id');
    }

}
