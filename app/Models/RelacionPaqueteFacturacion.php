<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RelacionPaqueteFacturacion extends Model
{
    use HasFactory;

    protected $table = 'relpaqfact';

    protected $primaryKey = 'Id';

    protected $fillable = [
        'Id',
        'IdPaquete',
        'IdEstudio',
        'IdExamen',
        'Baja'
    ];

    public $timestamps = false;
}
?>