<?php

namespace App\Services\ReportesExcel;

interface ReporteInterface2 
{
    public function columnasYEncabezados($sheet);
    public function datos($sheet, $datos, ...$args);
    public function generar($datos);
}