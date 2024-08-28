<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use App\Traits\AccessPolicy;
use App\Helpers\FileHelper;

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

        Blade::directive('fileUrl', function ($expression) {
            return "<?php echo \\App\\Helpers\\FileHelper::getFileUrl({$expression}); ?>";
        });

        $this->gateAccess([
            "prestaciones_show",
            "etapas_show",
            "mapas_show",
            "clientes_show",
            "noticias_show",
            "pacientes_show",
            "facturacion_show",
            "especialidades_show",
            "notaCredito_show",
            "mensajeria_show",
            "examenes_show",
            "examenCta_show",
        ]);

        $this->gateAccess([
            "etapas_efector",
            "etapas_informador",
            "mapas_cerrar",
            "mapas_finalizar",
            "examenCta_edit",
        ]);

        $this->gateAccess([
            "prestaciones_add",
            "clientes_add",
            "pacientes_add",
            "especialidades_add",
            "examenCta_add",
            "mapas_add",
            "examenes_add"
        ]);

        $this->gateAccess([
            "noticias_edit",
            "mensajeria_edit",
            "clientes_edit",
            "mapas_edit",
            "especialidades_edit",
            "examenCta_edit",
            "examenes_edit",
            "pacientes_edit"
        ]);

        $this->gateAccess([
            "pacientes_delete",
            "examenCta_delete",
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
            'prestaciones_eEnviar',
            'mapas_eenviar',
        ]);
    }

}