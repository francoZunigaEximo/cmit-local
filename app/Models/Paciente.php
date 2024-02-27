<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Paciente extends Model
{
    use HasFactory;

    protected $table = 'pacientes';

    protected $primaryKey = 'Id';

    protected $appends = ['NombreCompleto'];

    protected $fillable = [
        'TipoIdentificacion',
        'Identificacion',
        'TipoDocumento',
        'Documento',
        'Nacionalidad',
        'Sexo',
        'Nombre',
        'Apellido',
        'FechaNacimiento',
        'LugarNacimiento',
        'EstadoCivil',
        'ObsEstadoCivil',
        'Direccion',
        'CP',
        'EMail',
        'ObsEMail',
        'Foto',
        'Antecedentes',
        'Observaciones',
        'Estado',
        'Provincia',
        'IdLocalidad',
        'ObsEMail',
        'Id',
    ];

    public $timestamps = false;

    public function getNombreCompletoAttribute()
    {
        return $this->Apellido . ' ' . $this->Nombre;
    }

    public function prestaciones()
    {
        return $this->hasMany(Prestacion::class, 'IdPaciente', 'Id');
    }

    public function localidad()
    {
        return $this->hasOne(Localidad::class, 'Id', 'IdLocalidad');
    }

    public function fichaLaboral()
    {
        return $this->hasOne(Fichalaboral::class, 'IdPaciente', 'Id');
    }
}
