<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ArchivoPrestacion extends Model
{
    use HasFactory;

    protected $table = "archivosprestacion";

    protected $primaryKey = "Id";

    protected $fillable = [
        'Id',
        'IdEntidad',
        'Descripcion',
        'Ruta'
    ];

    public $timestamps = false;
}
