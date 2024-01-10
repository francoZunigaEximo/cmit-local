<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuditorAcciones extends Model
{
    use HasFactory;

    protected $table = 'auditoriaacciones';

    protected $primaryKey = 'Id';

    protected $fillable = [
        'Id',
        'Nombre'
    ];

    public $timestamps = false;
}
