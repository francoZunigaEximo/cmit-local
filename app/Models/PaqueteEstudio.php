<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaqueteEstudio extends Model
{
    use HasFactory;

    protected $table = 'paqestudios';

    protected $primaryKey = 'Id';

    protected $fillable = [
        'Id',
        'Nombre',
        'Descripcion',
    ];

    public $timestamps = false;

    public function examenes()
    {
        return $this->hasOne(Examen::class, 'Id', 'IdEstudio');
    }
}
