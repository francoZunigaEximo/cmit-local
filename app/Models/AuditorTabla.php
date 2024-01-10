<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuditorTabla extends Model
{
    use HasFactory;

    protected $table = 'auditoriatablas';

    protected $primaryKey = 'Id';

    protected $fillable = [
        'Id',
        'Nombre'
    ];

    public $timestamps = false;
}
