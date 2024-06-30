<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemsFacturaVenta extends Model
{
    use HasFactory;

    protected $table = 'itemsfacturaventa';
    protected $primaryKey = 'Id';

    protected $fillable = [
        'Id',
        'IdFactura',
        'IdPrestacion',
        'Detalle',
        'Anulado'
    ];

    public $timestamps = false;
    
    public function prestacion()
    {
        return $this->hasOne(Prestacion::class, 'Id', 'IdPrestacion');
    }
}
