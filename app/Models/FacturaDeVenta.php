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

    public function empresa()
    {
        return $this->hasOne(Cliente::class, 'Id', 'IdEmpresa');
    }

    public function notaCredito()
    {
        return $this->hasMany(NotaCredito::class, 'IdFactura', 'Id');
    }

    public function facturaresumen()
    {
        return $this->hasOne(FacturaResumen::class, 'IdFactura', 'Id');
    }

    public function prestacion()
    {
        return $this->hasOne(Prestacion::class, 'Id', 'IdPrestacion');
    }

}
