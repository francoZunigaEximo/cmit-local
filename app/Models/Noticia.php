<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Noticia extends Model
{
    use HasFactory;

    protected $table = 'noticias';

    protected $primaryKey = 'Id';

    protected $fillable = [
        'Id',
        'Titulo',
        'Subtitulo',
        'Texto',
        'Urgente',
        'Ruta'
    ];

    public $timestamps = false;

}
