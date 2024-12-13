<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HistorialPrestacion extends Model
{
    use HasFactory;

    protected $table = "hist_prestaciones";

    protected $primaryKey = "Id";

    public $timestamps = false;


    public function paciente()
    {
        return $this->hasOne(Paciente::class, 'Id', 'IdPaciente');
    }

    public function empresa()
    {
        return $this->hasOne(Cliente::class, 'Id', 'IdEmpresa');
    }

    public function art()
    {
        return $this->hasOne(Cliente::class, 'Id', 'IdART');
    }

}
