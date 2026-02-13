<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HistorialDatoPaciente extends Model
{
    use HasFactory;

    protected $table = 'hist_datospacientes';

    protected $primaryKey = 'Id';

    protected $fillable = [
        'Id',
        'IdPaciente',
        'IdPrestacion',
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
        'FechaIngreso',
        'FechaEgreso',
        'AntigPuesto',
        'AntigEmpresa',
        'TipoJornada',
        'Jornada',
        'ObsJornada',
        'CCosto'
    ];

    public $timestamps = false;
}
