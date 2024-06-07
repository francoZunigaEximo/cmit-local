<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuditoriaMail extends Model
{
    use HasFactory;

    protected $table = 'auditoriamailsmasivos';

    protected $primaryKey = 'Id';

    protected $fillable = [
        'Id',
        'Fecha',
        'Asunto',
        'Detalle',
        'Destinatarios'
    ];

    public $timestamps = false;
}
