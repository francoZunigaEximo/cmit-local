<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'profesional_id',
        'datos_id',
        'inactivo',
        'Anulado'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function profesional()
    {
        return $this->belongsTo(Profesional::class, 'Id', 'profesional_id');
    }

    public function personal()
    {
        return $this->hasOne(Personal::class, 'Id', 'datos_id');
    }

    public function auditor()
    {
        return $this->hasOne(Auditor::class, 'IdUsuario', 'name');
    }

    public function role()
    {
        return $this->belongsToMany(Rol::class, 'user_rol', 'user_id', 'rol_id');
    }
}
