<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PrestacionesTipo extends Model
{
    use HasFactory;

    protected $table = 'prestaciones_tipo';

    protected $primaryKey = 'Id';

    protected $fillable = [
        'Id',
        'Nombre',
    ];

    public $timestamps = false;
}
