<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Telefono extends Model
{
    use HasFactory;

    protected $table = 'telefonos';

    protected $primaryKey = 'Id';

    protected $fillable = [

        'IdEntidad',
        'IdCliente',
        'CodigoArea',
        'NumeroTelefono',
        'Observaciones',
        'TipoEntidad',
        'Id',
        'IdProfesional'
    ];

    public $timestamps = false;

    public function profesional()
    {
        return $this->hasOne(Profesional::class, 'Id', 'IdProfesional');
    }
}
