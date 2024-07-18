<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Provincia extends Model
{
    use HasFactory;

    protected $table = 'provincias';

    protected $primaryKey = 'Id';

    public function localidad()
    {
        return $this->hasMany(Localidad::class, 'IdProv', 'Id');
    }
}
