<?php

namespace App\Services\ReportesExcel;

interface ReporteInterface 
{
    public function columnasYEncabezados($sheet);
    public function datos($sheet, $datos);
    public function generar($datos);
}