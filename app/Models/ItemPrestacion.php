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

    public static function InsertarVtoPrestacion(int $idPrestacion)
    {
        $maxVtoItem = ItemPrestacion::max('VtoItem');

        $query = ItemPrestacion::where('Anulado', 0)
            ->where('IdPrestacion', $idPrestacion)
            ->where('VtoItem', $maxVtoItem)
            ->first();

        $maxvto = $query && !is_null($query->VtoPrestacion) ? $query->VtoPrestacion : 0;

        if($maxvto !== 0)
        {
            $vto = $maxvto;
            $vbfecha = $query->prestaciones->Fecha;
            $diashabiles = 0;
            $diastotal = 0;

            while($diashabiles < $vto){ 
                $vbdia = date('w',strtotime($vbfecha));
                
                if(($vbdia!=6) And ($vbdia!=0))
                {
                    $diashabiles=$diashabiles + 1;
                }//6:s,0:do
                $diastotal=$diastotal + 1;$vbfecha=date ( 'Y-m-j' ,strtotime ( '+1 day' , strtotime ( $vbfecha ))) ; 
            }
            $fechavto = date ( 'Y-m-j' ,strtotime ( '+'.$diastotal.' day' , strtotime ( $query->prestaciones->Fecha ))) ; 
        }else{
            $fechavto="0000-00-00";
        }

        if ($query && $query->prestaciones) {
            $query->prestaciones->update(['FechaVto' => $fechavto, 'Vto' => $maxvto]);
        }
    }

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

    public function proveedores()
    {
        return $this->hasOne(Proveedor::class, 'Id', 'IdProveedor');
    }
}
