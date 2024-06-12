<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Traits\AccessPolicy;

class AppServiceProvider extends ServiceProvider
{
    use AccessPolicy;

    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        ini_set('max_execution_time', 300);
        ini_set('memory_limit', '256M');

        $this->gateAccess("prestaciones_show");
        $this->gateAccess("etapas_show");
        $this->gateAccess("mapas_show");
        $this->gateAccess("clientes_show");
        $this->gateAccess("noticias_show");
        $this->gateAccess("examenCuenta_show");
        $this->gateAccess("pacientes_show");
        $this->gateAccess("facturacion_show");
        $this->gateAccess("especialidades_show");

        $this->gateAccess("prestaciones_add");
        $this->gateAccess("clientes_add");
        $this->gateAccess("pacientes_add");

        $this->gateAccess("noticias_edit");
        $this->gateAccess("mensajeria_edit");
        $this->gateAccess("clientes_edit");

        $this->gateAccess("pacientes_delete");
        
        $this->gateAccess("clientes_send");

        $this->gateAccess("paciente_report");
        $this->gateAccess("prestaciones_report");

        $this->gateAccess('boton_usuarios');

    }
}
