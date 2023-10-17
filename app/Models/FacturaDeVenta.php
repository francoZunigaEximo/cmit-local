<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FacturaDeVenta extends Model
{
    use HasFactory;

    protected $table = 'itemsprestaciones';

    protected $primaryKey = 'Id';

    public $timestamps = false;

    public function itemPrestacion()
    {
        return $this->hasOne(ItemPrestacion::class, 'NumeroFacturaVta', 'Id');
    }
}
