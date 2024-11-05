<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DatoPaciente extends Model
{
    use HasFactory;

    protected $table = 'datospacientes';
    
    protected $primaryKey = 'Id';

    protected $fillable = [
        'Id',
        'IdPaciente',
        'Prestacion',
        'Edad',
        'EstadoCivil',
        'ObsEC',
        'Direccion',
        'IdLocalidad',
        'TipoActividad',
        'Tareas',
        'TareasEmpAnterior',
        'Puesto',
        'Sector',
        'FechaIngreso','
        FechaEgreso',
        'AntigPuesto',
        'AntigEmpresa',
        'TipoJornada',
        'Jornada',
        'ObsJornada',
        'CCosto'
    ];

    public $timestamps = false;

    public function paciente()
    {
        return $this->hasOne(Paciente::class, 'Id', 'IdPaciente');
    }
}
