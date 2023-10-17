<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotaCreditoIt extends Model
{
    use HasFactory;

    protected $table = 'notascredito_it';

    protected $primaryKey = 'Id';

    public $timestamps = false;

    public function itemPrestacion()
    {
        return $this->hasOne(ItemPrestacion::class, 'Id', 'IdIP');
    }

    public function notaCredito()
    {
        return $this->hasOne(NotaCredito::class, 'Id', 'IdNC');
    }
}
