<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Auditor extends Model
{
    use HasFactory;

    protected $table = 'auditoria';

    protected $primaryKey = 'Id';

    protected $fillable = [
        'Id',
        'IdTabla',
        'IdAccion',
        'IdRegistro',
        'IdUsuario',
        'Fecha'
    ];

    public $timestamps = false;
}
