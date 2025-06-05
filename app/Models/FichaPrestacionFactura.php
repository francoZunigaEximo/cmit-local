<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FichaPrestacionFactura extends Model
{
    use HasFactory;
        
    protected $table = 'fichaprestacion_factura';

    protected $primaryKey = 'id';

    protected $fillable = [
        'prestacion_id',
        'fichalaboral_id',
        'Tipo',
        'Sucursal',
        'NroFactura',
        'NroFactProv'
    ];

    public $timestamps = false;

    public function prestacion()
    {
        return $this->hasOne(Prestacion::class, 'datos_facturacion_id', 'id');
    }
}
