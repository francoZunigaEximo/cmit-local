<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Personal extends Model
{
    use HasFactory;

    protected $table = 'datos';

    protected $primaryKey = 'Id';

    protected $fillable = [];

    public $timestamps = false;

    public function user()
    {
        return $this->hasOne(User::class, 'datos_id', 'Id');
    }
}
