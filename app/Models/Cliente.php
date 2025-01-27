<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    use HasFactory;

    protected $table = 'clientes';

    protected $primaryKey = 'Id';

    protected $fillable = [
        'Id',
        'TipoIdentificacion',
        'TipoCliente',
        'Identificacion',
        'ParaEmpresa',
        'RazonSocial',
        'Nacionalidad',
        'CondicionIva',
        'TipoPersona',
        'Envio',
        'Entrega',
        'IdActividad',
        'NombreFantasia',
        'Logo',
        'Bloqueado',
        'Motivo',
        'Direccion',
        'IdLocalidad',
        'Provincia',
        'CP',
        'EMail',
        'ObsEMail',
        'EMailResultados',
        'Telefono',
        'LogoCertificado',
        'Oreste',
        'FPago',
        'ObsEval',
        'ObsCE',
        'ObsCO',
        'Generico',
        'SEMail',
        'IdAsignado',
        'EMailFactura',
        'EnvioFactura',
        'EMailInformes',
        'EnvioInforme',
        'Ajuste',
        'SinPF',
        'SinEval',
        'RF',
        'Observaciones',
        'Estado',
        'Anexo',
        'EMailAnexo',
        'Descuento'
    ];

    public $timestamps = false;

    public function prestacion()
    {
        return $this->hasOne(Prestacion::class, 'Id', ['IdART', 'IdEmpresa']);
    }

    public function mapa()
    {
        return $this->hasMany(Mapa::class, ['IdART', 'IdEmpresa'], 'Id');
    }

    public function localidad()
    {
        return $this->hasOne(Localidad::class, 'Id', 'IdLocalidad');
    }

    public function fichaLaboral()
    {
        return $this->hasMany(Fichalaboral::class, ['IdArt', 'IdEmpresa'], 'Id');
    }

    public function examenCuenta()
    {
        return $this->hasMany(ExamenCuenta::class, 'IdEmpresa', 'Id');
    }

    public function facturadeventa()
    {
        return $this->hasMany(FacturaDeVenta::class, 'IdEmpresa', 'Id');
    }

    public function historialPrestacion()
    {
        return $this->hasMany(HistorialPrestacion::class, ['IdART', 'IdEmpresa'], 'Id');
    }
}

