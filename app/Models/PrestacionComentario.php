<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PrestacionComentario extends Model
{
    use HasFactory;

    protected $table = 'prestaciones_comentarios';

    protected $primaryKey = 'Id';

    protected $fillable = [
        'Id',
        'IdP',
        'Obs',
    ];

    public $timestamps = false;

    public function prestacion()
    {
        return $this->hasOne(Prestacion::class, 'Id', 'IdP');
    }
}
