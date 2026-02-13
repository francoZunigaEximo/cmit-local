<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemPrestacionInfo extends Model
{
    use HasFactory;

    protected $table = 'itemsprestaciones_info';

    protected $primaryKey = 'Id';

    public $timestamps = false;

    protected $fillable = [
        'Id',
        'IdIP',
        'IdP',
        'Obs',
        'C1',
        'C2'
    ];

    public function itemsprestacion()
    {
        return $this->hasOne(ItemPrestacion::class, 'Id', 'IdIP');
    }


}
