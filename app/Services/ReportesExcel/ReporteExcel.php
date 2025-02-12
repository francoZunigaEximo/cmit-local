<?php

namespace App\Services\ReportesExcel;

use App\Services\ReportesExcel\modelos\Paciente;
use App\Services\ReportesExcel\modelos\Cliente;
use App\Services\ReportesExcel\modelos\Mapa;
use App\Services\ReportesExcel\modelos\Especialidad;
use App\Services\ReportesExcel\modelos\Remito;
use App\Services\ReportesExcel\modelos\EfectorExportar;
use App\Services\ReportesExcel\modelos\EfectorDetallado;
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
            case 'efectorExportar':
                return new EfectorExportar();
            case 'efectorDetalle':
                return new EfectorDetallado();
            default:
                throw new Exception("Tipo de reporte no soportado.");
        }
    }
}
