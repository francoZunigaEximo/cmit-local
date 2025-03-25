<?php

namespace App\Services\ReportesExcel;

use App\Services\ReportesExcel\modelos\Paciente;
use App\Services\ReportesExcel\modelos\Cliente;
use App\Services\ReportesExcel\modelos\DetalladaPrestacion;
use App\Services\ReportesExcel\modelos\DetalladaPrestacionFull;
use App\Services\ReportesExcel\modelos\Mapa;
use App\Services\ReportesExcel\modelos\Especialidad;
use App\Services\ReportesExcel\modelos\Remito;
use App\Services\ReportesExcel\modelos\EfectorExportar;
use App\Services\ReportesExcel\modelos\EfectorDetallado;
use App\Services\ReportesExcel\modelos\ResumenTotal;
use App\Services\ReportesExcel\modelos\SimplePrestacion;
use App\Services\ReportesExcel\modelos\SimplePrestacionFull;
use App\Services\ReportesExcel\modelos\CompletoPrestacionFull;

class ReporteExcel
{
    public static function crear($tipo)
    {
        switch ($tipo) {
            case 'pacientes':
                return new Paciente();
            case 'clientes':
                return new Cliente();
            case 'mapas':
                return new Mapa();
            case 'especialidades':
                return new Especialidad();
            case 'remitos':
                return new Remito();
            case 'efectorExportar':
                return new EfectorExportar();
            case 'efectorDetalle':
                return new EfectorDetallado();
            case 'resumenTotal':
                return new ResumenTotal();
            case 'simplePrestacion':
                return new SimplePrestacion();
            case 'simplePrestacionFull':
                return new SimplePrestacionFull();
            case 'detalladaPrestacion':
                return new DetalladaPrestacion();
            case 'detalladaPrestacionFull':
                return new DetalladaPrestacionFull();
            case 'completoPrestacionFull':
                return new CompletoPrestacionFull();
            default:
                return response()->json(['msg' => 'Tipo de reporte no válido'], 400);
        }
    }
}
