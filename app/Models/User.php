<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, HasRoles, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'IdProfesinal',
        'IdPersonal',
        'IdPerfil',
        'SR',
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
        return $this->hasOne(Profesional::class, 'Id', 'IdProfesional');
    }

    public function personal()
    {
        return $this->hasOne(Personal::class, ' Id', 'IdPersonal');
    }

    public function perfil()
    {
        return $this->hasOne(Personal::class, 'Id', 'IdPerfil');
    }

    public function auditor()
    {
        return $this->hasOne(Auditor::class, 'IdUsuario', 'name');
    }
}
