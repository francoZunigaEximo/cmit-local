<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AliasExamen extends Model
{
    use HasFactory;

    protected $table = 'aliasExamenes';

    protected $primaryKey = 'Id';

    protected $fillable = [
        'Nombre',
        'Descripcion'
    ];

    public $timestamps = false;

    
}
