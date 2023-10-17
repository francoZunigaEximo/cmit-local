<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PrestacionPaqFact extends Model
{
    use HasFactory;

    protected $table = 'prestacionespaqfact';

    protected $primaryKey = 'Id';

    protected $fillable = [
        'Id',
        'IdPrestacion',
        'IdItem',
        'IdExamen',
        'IdPaqFact',
    ];

    public $timestamps = false;
}
