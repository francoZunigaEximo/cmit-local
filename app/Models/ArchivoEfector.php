<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ArchivoEfector extends Model
{
    use HasFactory;

    protected $table = 'archivosefector';

    protected $primaryKey = 'Id';

    protected $fillable = [];

    public $timestamps = false;
}
