<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProfesionalProv extends Model
{
    use HasFactory;

    protected $table = 'profesionales_prov';

    protected $primaryKey = 'Id';

    protected $fillable = [
        'Id',
        'IdProf',
        'IdProv',
        'Tipo'
    ];

    public $timestamps = false;

    public function profesionales()
    {
        return $this->hasMany(Profesional::class, 'Id', 'IdProf');
    }

    public function proveedores()
    {
        return $this->hasOne(Proveedor::class, 'Id', 'IdProv');
    }


}
