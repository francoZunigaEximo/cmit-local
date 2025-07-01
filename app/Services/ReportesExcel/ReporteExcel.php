<?php

namespace App\Services\ReportesExcel;

use App\Services\ReportesExcel\modelos\Paciente;
use App\Services\ReportesExcel\modelos\Cliente;
use App\Services\ReportesExcel\modelos\DetalladaPrestacion;
use App\Services\ReportesExcel\modelos\Mapa;
use App\Services\ReportesExcel\modelos\Especialidad;
use App\Services\ReportesExcel\modelos\Remito;
use App\Services\ReportesExcel\modelos\LlamadorExportar;
use App\Services\ReportesExcel\modelos\LlamadorDetallado;
use App\Services\ReportesExcel\modelos\ResumenTotal;
use App\Services\ReportesExcel\modelos\SimplePrestacion;
use Exception;

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
            case 'llamadorExportar':
                return new LlamadorExportar();
            case 'llamadorDetalle':
                return new LlamadorDetallado();
            case 'resumenTotal':
                return new ResumenTotal();
            case 'simplePrestacion':
                return new SimplePrestacion();
            case 'detalladaPrestacion':
                return new DetalladaPrestacion();
            default:
                throw new Exception("Tipo de reporte no soportado.");
        }
    }
}
