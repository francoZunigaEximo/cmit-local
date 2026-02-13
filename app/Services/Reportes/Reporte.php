<?php

namespace App\Services\Reportes;

use FPDF;

abstract class Reporte
{
    abstract public function render(FPDF $pdf, $params = []): void;
}
