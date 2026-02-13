<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PrestacionObsFase extends Model
{
    use HasFactory;

    protected $table = 'prestaciones_obsfases';

    protected $primaryKey = 'Id';

    protected $fillable = [
        'Id',
        'IdEntidad',
        'Comentario',
        'IdExamen',
        'IdUsuario',
        'Fecha',
        'Rol',
        'obsfases_id'
    ];

    public $timestamps = false;
}
