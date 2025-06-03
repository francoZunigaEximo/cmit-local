<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RelacionGrupoCliente extends Model{
 use HasFactory;

    protected $table = 'clientesgrupos_it';

    protected $primaryKey = 'Id';

    protected $fillable = [
        'Id',
        'IdGrupo',
        'IdCliente',
        'Baja'
    ];

    public $timestamps = false;

}
?>