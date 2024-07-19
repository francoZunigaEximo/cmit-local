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

        $this->gateAccess([
            "prestaciones_show",
            "etapas_show",
            "mapas_show",
            "clientes_show",
            "noticias_show",
            "examenCuenta_show",
            "pacientes_show",
            "facturacion_show",
            "especialidades_show",
            "profesionales_show",
            "notaCredito_show",
            "mensajeria_show",
            "examenes_show",
        ]);

        $this->gateAccess([
            "prestaciones_add",
            "clientes_add",
            "pacientes_add",
            "especialidades_add",
            "examenCuenta_add",
            "mapas_add",
            "examenes_add"
        ]);

        $this->gateAccess([
            "noticias_edit",
            "mensajeria_edit",
            "clientes_edit",
            "mapas_edit",
            "especialidades_edit",
            "examenCuenta_edit",
            "examenes_edit"
        ]);

        $this->gateAccess([
            "pacientes_delete",
            "examenCuenta_delete",
        ]);

        $this->gateAccess([
            "paciente_report",
            "prestaciones_report",
            "clientes_export",
        ]);

        $this->gateAccess([
            'boton_usuarios',
            'boton_todo'
        ]);

        $this->gateAccess([
            'etapas_eenviar',
            'prestaciones_eEnviar'
        ]);
    }

}