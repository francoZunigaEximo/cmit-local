<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExamenCuentaIt extends Model
{
    use HasFactory;

    protected $table = 'pagosacuenta_it';

    protected $primaryKey = 'Id';

    protected $fillable = [
        'Id',
        'IdPago',
        'IdExamen',
        'IdPrestacion',
        'Obs',
        'Obs2',
        'Obs',
        'Precarga'
    ];

    public $timestamps = false;

    
}
