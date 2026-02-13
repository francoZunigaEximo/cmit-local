<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RelacionPaqueteEstudio extends Model
{
    use HasFactory;

    protected $table = 'relpaqest';

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