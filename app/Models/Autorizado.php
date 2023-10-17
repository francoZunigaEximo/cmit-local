<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Autorizado extends Model
{
    use HasFactory;

    protected $table = 'autorizados';

    protected $primaryKey = 'Id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'Id',
        'IdEntidad', //Cliente_id
        'Nombre',
        'Apellido',
        'DNI',
        'Derecho',
        'TipoEntidad',
    ];

    public $timestamps = false;
}
