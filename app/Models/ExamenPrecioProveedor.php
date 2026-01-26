<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExamenPrecioProveedor extends Model
{
    use HasFactory;

    protected $table = "examenesprecioproveedor";

    protected $primaryKey = 'Id';

    protected $fillable = [
        'Id',
        'IdEstudio',
        'IdExamen',
        'IdProveedor',
        'Honorarios'
    ];

    public $timestamps = false;
}
