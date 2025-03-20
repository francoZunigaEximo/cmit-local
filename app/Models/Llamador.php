<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Llamador extends Model
{
    use HasFactory;

    protected $table = 'llamador';

    protected $primaryKey = 'Id';

    public $fillable = [
        'Id',
        'profesional_id',
	    'prestacion_id',
	    'itemprestacion_id',
    ];

    public $timestamps = false;

    public function profesional()
    {
        return $this->hasOne(Profesional::class, 'Id', 'profesional_id');
    }

    public function prestacion()
    {
        return $this->hasOne(Prestacion::class, 'Id', 'prestacion_id');
    }

    public function itemPrestacion()
    {
        return $this->hasOne(ItemPrestacion::class, 'Id', 'itemprestacion_id');
    }
}
