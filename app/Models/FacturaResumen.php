<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FacturaResumen extends Model
{
    use HasFactory;

    protected $table = "facturasresumen";
    protected $primaryKey = "Id";

    protected $fillable = [
        'Id',
        'IdFactura',
        'Total',
        'Detalle',
        'Cod'
    ];

    public $timestamps = false;

    public function facturadeventa()
    {
        return $this->hasOne(FacturaDeVenta::class, 'Id', 'IdFactura');
    }

}
