<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FacturaCompra extends Model
{
    use HasFactory;

    protected $table = "facturascompra";

    protected $primaryKey = 'Id';

    protected $fillable = [
        'Id',
        'Tipo',
        'Sucursal',
        'NroFactura',
        'Fecha',
        'Anulada',
        'FechaAnulada',
        'IdProfesional',
        'ObsAnulado',
        'Obs',
        'Baja'
    ];

    public $timestamps = false;
}
