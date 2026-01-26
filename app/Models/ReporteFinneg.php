<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReporteFinneg extends Model
{
    use HasFactory;

    protected $table = "reportes_finneg";

    protected $primaryKey = "Id";

    protected $fillable = [
        'IdFinneg',
        'IdFactura',
        'cuit_cliente'
    ];

    
}
