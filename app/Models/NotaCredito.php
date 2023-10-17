<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotaCredito extends Model
{
    use HasFactory;

    protected $table = 'notascredito';

    protected $primaryKey = 'Id';

    public $timestamps = false;

    public function notaCreditoIt()
    {
        return $this->hasOne(NotaCreditoIt::class, 'IdNC', 'Id');
    }
}
