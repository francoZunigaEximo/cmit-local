<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FacturaDeVenta extends Model
{
    use HasFactory;

    protected $table = 'itemsprestaciones';

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
        'Obs'
    ];

    public $timestamps = false;

    public function itemPrestacion()
    {
        return $this->hasOne(ItemPrestacion::class, 'NumeroFacturaVta', 'Id');
    }
}
