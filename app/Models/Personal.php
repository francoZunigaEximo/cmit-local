<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Personal extends Model
{
    use HasFactory;

    protected $table = 'datos';

    protected $primaryKey = 'Id';

    protected $fillable = [
        'Id',
        'TipoIdentificacion',
        'Apellido',
        'Nombre',
        'TipoDocumento',
        'Documento',
        'Identificacion',
        'Telefono',
        'FechaNacimiento',
        'Provincia',
        'IdLocalidad', 
        'CP',
        'Direccion'
    ];

    public $timestamps = false;

    public function user()
    {
        return $this->hasOne(User::class, 'datos_id', 'Id');
    }
}
