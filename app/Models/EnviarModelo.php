<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EnviarModelo extends Model
{
    use HasFactory;

    protected $table = 'enviarmodelos';

    protected $primaryKey = 'Id';

    protected $fillable = [
        'Id',
        'Nombre',
        'Asunto',
        'Cuerpo'
    ];

    public $timestamps = false;
}
