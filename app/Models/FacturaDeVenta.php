<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FacturaDeVenta extends Model
{
    use HasFactory;

    protected $table = 'facturasventa';

    protected $primaryKey = 'Id';

    protected $fillable = [
        'Id',
        'Tipo',
        'Sucursal',
        'NroFactura',
        'Fecha',
        'Anulada',
        'FechaAnulada',
        'IdEmpresa',
        'TipoCliente',
        'ObsAnulado',
        'EnvioFacturaF',
        'Obs',
        'IdPrestacion'
    ];

    public $timestamps = false;

    public function itemPrestacion()
    {
        return $this->hasOne(ItemPrestacion::class, 'NumeroFacturaVta', 'Id');
    }
}
