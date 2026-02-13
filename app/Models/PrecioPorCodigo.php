<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PrecioPorCodigo extends Model
{
    use HasFactory;

    protected $table = 'preciosxcod';

    protected $primaryKey = 'Id';

    protected $fillable = [
        'Id',
        'Cod',
        'Precio',
    ];

    public $timestamps = false;
}
