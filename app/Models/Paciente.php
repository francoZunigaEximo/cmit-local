<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Paciente extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'pacientes';

    protected $primaryKey = 'Id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
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

    public function prestaciones()
    {
        return $this->hasMany(Prestacion::class, 'IdPaciente', 'Id');
    }

    public function localidad()
    {
        return $this->hasOne(Localidad::class, 'Id', 'IdLocalidad');
    }
}
