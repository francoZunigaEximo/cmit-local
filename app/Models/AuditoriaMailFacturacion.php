<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuditoriaMailFacturacion extends Model
{
    use HasFactory;

    protected $table = 'auditoriamailsfact';

    protected $primaryKey = 'Id';

    protected $fillable = [
        'Id',
        'Fecha',
        'Destinatarios'
    ];

    public $timestamps = false;
    
}
