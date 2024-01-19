<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemPrestacion extends Model
{
    use HasFactory;

    protected $table = 'itemsprestaciones';

    protected $primaryKey = 'Id';

    protected $fillable = [
        'Id',
        'IdPrestacion',
        'IdExamen',
        'ObsExamen',
        'IdProveedor',
        'IdProfesional',
        'IdProfesional2',
        'FechaPagado',
        'FechaPagado2',
        'Anulado',
        'Fecha',
        'FechaAsignado',
        'Facturado',
        'NumeroFacturaVta',
        'VtoItem',
        'Honorarios',
        'NroFactCompra',
        'NroFactCompra2',
        'Incompleto',
        'HoraAsignado',
        'HoraFAsignado',
        'SinEsc',
        'Forma',
        'Ausente',
        'Devol',
        'CInfo',
        'CAdj',
    ];

    public $timestamps = false;

    public function prestaciones()
    {
        return $this->hasOne(Prestacion::class, 'Id', 'IdPrestacion');
    }

    public function examenes()
    {
        return $this->hasOne(Examen::class, 'Id', 'IdExamen');
    }

    public function profesionales1()
    {
        return $this->hasOne(Profesional::class, 'Id', 'IdProfesional');
    }

    public function profesionales2()
    {
        return $this->hasOne(Profesional::class, 'Id', 'IdProfesional2');
    }

    public function itemsInfo()
    {
        return $this->hasOne(ItemPrestacionInfo::class, 'IdIP', 'Id');
    }

    public function facturadeventa()
    {
        return $this->hasOne(FacturaDeVenta::class, 'Id', 'NumeroFacturaVta');
    }

    public function notaCreditoIt()
    {
        return $this->hasOne(NotaCreditoIt::class, 'IdIP', 'Id');
    }

    public function archivoEfector()
    {
        return $this->hasMany(ArchivoEfector::class, 'IdEntidad', 'Id');
    }
}
