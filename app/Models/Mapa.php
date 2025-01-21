<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mapa extends Model
{
    use HasFactory;

    protected $table = 'mapas';

    protected $primaryKey = 'Id';

    protected $fillable = [
        'Id',
        'Nro',
        'Fecha',
        'IdART',
        'IdEmpresa',
        'Obs',
        'Inactivo',
        'Cpacientes',
        'Cmapeados',
        'FechaE',
    ];

    public $timestamps = false;

    public function prestacion()
    {
        return $this->belongsTo(Prestacion::class, 'IdMapa', 'Id');
    }

    public function artMapa()
    {
        return $this->hasOne(Cliente::class, 'Id', 'IdART');
    }

    public function empresaMapa()
    {
        return $this->hasOne(Cliente::class, 'Id', 'IdEmpresa');
    }
}
