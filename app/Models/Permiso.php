<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Permiso extends Model
{
    use HasFactory;

    protected $table = "permisos";

    protected $primaryKey = "Id";

    protected $fillable = [
        'slug',
        'descripcion',
    ];

    public $timestamps = false;

    public function role()
    {
        return $this->belongsToMany(Rol::class, 'rol_permisos', 'permiso_id', 'rol_id');
    }
}
