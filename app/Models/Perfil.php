<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Perfil extends Model
{
    use HasFactory;

    protected $table = 'perfiles';

    protected $primaryKey = 'Id';

    public $timestamps = false;

    public function user()
    {
        return $this->hasOne(User::class, 'IdPerfil', 'Id');
    }
}
