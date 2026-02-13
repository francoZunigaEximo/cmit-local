<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotaCredito extends Model
{
    use HasFactory;

    protected $table = 'notascredito';

    protected $primaryKey = 'Id';

    public $timestamps = false;

    protected $fillable = [
        'Id',
        'Tipo',
        'Sucursal',
        'Nro',
        'Fecha',
        'IdEmpresa',
        'TipoCliente',
        'IdFactura',
        'IdPrestacion',
        'TipoNC',
        'Obs'
    ];

    public function notaCreditoIt()
    {
        return $this->hasOne(NotaCreditoIt::class, 'IdNC', 'Id');
    }

    public function FacturaDeVenta()
    {
        return $this->hasOne(FacturaDeVenta::class, 'Id', 'IdFactura');
    }
}
