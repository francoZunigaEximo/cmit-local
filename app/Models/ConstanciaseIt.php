<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConstanciaseIt extends Model
{
    use HasFactory;
    
    protected $table = "constanciase_it";

    protected $primaryKey = "Id";

    protected $fillable = [
        'Id',
        'IdC',
        'IdP'
    ];

    public $timestamps = false;

    public static function addConstPrestacion($idPrestacion, $idConstancia)
    {
        ConstanciaseIt::create([
            'Id' => ConstanciaseIt::max('Id') + 1,
            'IdC' => $idConstancia,
            'IdP' => $idPrestacion
        ]);
    }
}
