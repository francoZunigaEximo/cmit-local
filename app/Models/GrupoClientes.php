<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GrupoClientes extends Model{
 use HasFactory;

    protected $table = 'clientesgrupos';

    protected $primaryKey = 'Id';

    protected $fillable = [
        'Id',
        'Nombre',
        'Baja'
    ];

    public $timestamps = false;

}