<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaqueteFacturacion extends Model
{
    use HasFactory;

    protected $table = 'paqfacturacion';

    protected $primaryKey = 'Id';

    protected $fillable = [
        'Id',
        'Nombre',
        'Descripcion',
        'CantExamenes',
        'IdGrupo',
        'IdEmpresa',
        'Cod'
    ];

    public function examenes()
    {
        return $this->belongsToMany(Examen::class, 'relpaqest', 'IdPaquete', 'IdExamen');
    }
}
