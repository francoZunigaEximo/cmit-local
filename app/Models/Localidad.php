<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Localidad extends Model
{
    use HasFactory;

    protected $table = 'localidades';

    protected $primaryKey = 'Id';

    public function paciente()
    {
        return $this->hasOne(Paciente::class, 'IdLocalidad', 'Id');
    }

    public function profesional()
    {
        return $this->hasOne(Profesional::class, 'IdLocalidad', 'Id');
    }

    public function cliente()
    {
        return $this->hasOne(Cliente::class, 'IdLocalidad', 'Id');
    }

    public function proveedor()
    {
        return $this->hasOne(Proveedor::class, 'IdLocalidad', 'Id');
    }
}
