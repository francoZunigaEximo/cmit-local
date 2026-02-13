<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rol extends Model
{
    use HasFactory;

    protected $table = "roles";

    protected $primaryKey = "Id";

    protected $fillable = [
        'nombre',
        'descripcion',
    ];

    public $timestamps = false;

    public function user()
    {
        return $this->belongsToMany(User::class, 'user_rol', 'rol_id', 'user_id');
    }

    public function permiso()
    {
        return $this->belongsToMany(Permiso::class, 'rol_permisos', 'rol_id', 'permiso_id');
    }
}
