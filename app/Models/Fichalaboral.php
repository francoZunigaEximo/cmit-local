<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Fichalaboral extends Model
{
    use HasFactory;

    protected $table = 'fichaslaborales';

    protected $primaryKey = 'Id';

    protected $fillable = [
        'Id',
        'IdPaciente',
        'IdEmpresa',
        'IdART',
        'Tareas',
        'Puesto',
        'Sector',
        'FechaIngreso',
        'FechaEgreso',
        'AntigPuesto',
        'Tipojornada',
        'Jornada',
        'Observaciones',
        'TipoActividad',
        'CCosto',
        'Pago',
        'Estado',
        'TipoPrestacion'
    ];

    public $timestamps = false;

    public function empresa()
    {
        return $this->hasOne(Cliente::class, 'Id', 'IdEmpresa');
    }

    public function art()
    {
        return $this->hasOne(Cliente::class, 'Id', 'IdART');
    }

}
