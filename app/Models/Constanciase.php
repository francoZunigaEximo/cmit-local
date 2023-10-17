<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Constanciase extends Model
{
    use HasFactory;

    protected $table = 'constanciase';

    protected $primaryKey = 'Id';

    protected $fillable = [
        'Id',
        'NroC',
        'Fecha',
        'Obs'
    ];

    public $timestamps = false;
}
