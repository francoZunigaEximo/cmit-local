<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Auditor extends Model
{
    use HasFactory;

    protected $table = 'auditoria';

    protected $primaryKey = 'Id';

    protected $fillable = [
        'Id',
        'IdTabla',
        'IdAccion',
        'IdRegistro',
        'IdUsuario',
        'Fecha',
        'Observaciones'
    ];

    public $timestamps = false;

    public function auditarTabla()
    {
        return $this->hasOne(AuditorTabla::class, 'Id', 'IdTabla');
    }

    public function auditarAccion()
    {
        return $this->hasOne(AuditorAcciones::class, 'Id', 'IdAccion');
    }

    public function usuario()
    {
        return $this->hasOne(User::class, 'name', 'IdUsuario');
    }

    public static function setAuditoria(int $registro, int $tabla, int $accion, string $usuario, ?string $observaciones = null)
    {
        return Auditor::create([
            'Id' => Auditor::max('Id') + 1,
            'IdTabla' => $tabla,
            'IdAccion' => $accion,
            'IdRegistro' => $registro,
            'IdUsuario' => $usuario, 
            'Fecha' => now(),
            'Observaciones' => $observaciones
        ]);
    }
}
