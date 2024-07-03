<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Prestacion extends Model
{
    use HasFactory;

    protected $table = 'prestaciones';

    protected $primaryKey = 'Id';

    protected $fillable = [
        'Estado',
        'Id',
        'IdPaciente',
        'IdEmpresa',
        'IdART',
        'TipoPrestacion',
        'IdMapa',
        'Pago',
        'SPago',
        'Observaciones',
        'NumeroFacturaVta',
        'Fecha',
        'Financiador',
        'FechaCierre',
        'FechaFinalizado',
        'Finalizado',
        'Cerrado',
        'Entregar',
        'FechaEntrega',
        'eEnviado',
        'FechaEnviado',
        'FechaVto',
        'Vto',
        'IdEvaluador',
        'Devol',
        'Ausente',
        'Forma',
        'SinEsc',
        'RxPreliminar',
        'Incompleto',
        'FechaAnul',
        'FechaFact',
        'Evaluacion',
        'Anulado'
    ];

    public $timestamps = false;

    public function paciente()
    {
        return $this->hasOne(Paciente::class, 'Id', 'IdPaciente');
    }

    public function empresa()
    {
        return $this->hasOne(Cliente::class, 'Id', 'IdEmpresa');
    }

    public function art()
    {
        return $this->hasOne(Cliente::class, 'Id', 'IdART');
    }

    public function mapa()
    {
        return $this->hasOne(Mapa::class, 'Id', 'IdMapa');
    }

    public function profesional()
    {
        return $this->hasOne(Profesional::class, 'Id', 'IdEvaluador');
    }

    public function itemsPrestacion()
    {
        return $this->hasMany(ItemPrestacion::class, 'IdPrestacion', 'Id');
    }
    
    public function prestacionAtributo()
    {
        return $this->hasOne(PrestacionAtributo::class, 'IdPadre', 'Id');
    }

    public function prestacionComentario()
    {
        return $this->hasOne(PrestacionComentario::class, 'IdP', 'Id');
    }

    public function constanciases()
    {
        return $this->belongsToMany(Constanciase::class, 'constanciase_it', 'IdP', 'IdC');
    }

    public function examenCuenta()
    {
        return $this->belongToMany(ExamenCuenta::class, 'pagoacuenta_it', 'IdPrestacion', 'IdPago');
    }

    public function itemFacturaVenta()
    {
        return $this->hasOne(ItemsFacturaVenta::class, 'IdPrestacion', 'Id');
    }

    public function facturadeventa()
    {
        return $this->hasMany(FacturaDeVenta::class, 'IdPrestacion', 'Id');
    }
}
