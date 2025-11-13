<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemsFacturaCompra2 extends Model
{
    use HasFactory;

    protected $table = "itemsfacturacompra2";

    protected $primaryKey = 'Id';

    protected $fillable = [
        'Id',
        'IdFactura',
        'IdItemPrestacion'  
    ];

    public $timestamps = false;
}
