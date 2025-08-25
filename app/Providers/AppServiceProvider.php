<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
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

        $permisos = [
            'prestaciones' => ['show', 'add', 'report', 'eEnviar'],
            'etapas' => ['show', 'efector', 'informador', 'eenviar'],
            'mapas' => ['show', 'cerrar', 'finalizar', 'add', 'edit', 'eenviar'],
            'clientes' => ['show', 'add', 'edit', 'export'],
            'pacientes' => ['show', 'add', 'edit', 'delete', 'report'],
            'noticias' => ['show', 'edit'],
            'facturacion' => ['show'],
            'especialidades' => ['show', 'add', 'edit'],
            'mensajeria' => ['show', 'edit'],
            'notaCredito' => ['show'],
            'examenes' => ['show', 'edit', 'add'],
            'examenCta' => ['show', 'edit', 'add', 'edit', 'delete'],
            'grupos' => ["show"],
            'paquetes' => ["show"],
            'botones' => ['usuarios', 'todo']
        ];

        Blade::directive('fileUrl', function ($expression) {
            return "<?php echo \\App\\Helpers\\FileHelper::getFileUrl({$expression}); ?>";
        });
        
        $this->accesosHabilitados($permisos);
    }

    private function accesosHabilitados(array $datos) 
    {
        $habilitados = [];
        foreach($datos as $permiso => $acciones) {
            foreach($acciones as $accion) {
                
                if($permiso === 'botones'){
                    $habilitados[] = "boton_" . $accion;
               
                }else{
                    $habilitados[] = $permiso . "_" . $accion;
                }
            }
        }
        return $this->gateAccess($habilitados);
    }

}