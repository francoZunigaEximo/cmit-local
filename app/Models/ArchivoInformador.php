<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ArchivoInformador extends Model
{
    use HasFactory;

    protected $table = 'archivosinformador';

    protected $primaryKey = 'Id';

    protected $fillable = [];

    public $timestamps = false;
}
