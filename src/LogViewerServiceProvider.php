<?php

namespace Ahinest\LogViewer;

use Illuminate\Support\ServiceProvider;
use Ahinest\LogViewer\Services\LogParserService;
use Ahinest\LogViewer\Support\RendererFactory;
use Ahinest\LogViewer\Services\LogFileService;

/**
 * Service Provider principal del paquete
 * Laravel Log Viewer.
 *
 * Responsabilidades:
 *
 * - Registrar servicios dentro del contenedor.
 * - Cargar las rutas del paquete.
 * - Registrar las vistas del paquete.
 * - Publicar archivos configurables.
 * - Configurar el sistema de renderizado.
 *
 * Recursos publicados:
 *
 * logviewer-config
 *     Configuración del paquete.
 *
 * logviewer-views
 *     Vistas Blade personalizables.
 *
 * logviewer-vue
 *     Componentes Vue/Inertia de ejemplo.
 *
 * Este provider es descubierto automáticamente
 * por Laravel mediante Package Discovery.
 */
class LogViewerServiceProvider extends ServiceProvider
{
    /**
     * Inicializa los recursos del paquete.
     *
     * Acciones realizadas:
     *
     * - Carga las rutas web.
     * - Registra el namespace de vistas.
     * - Define los recursos publicables.
     *
     * Tags disponibles:
     *
     * php artisan vendor:publish
     *     --tag=logviewer-config
     *
     * php artisan vendor:publish
     *     --tag=logviewer-views
     *
     * php artisan vendor:publish
     *     --tag=logviewer-vue
     *
     * @return void
     */
    public function boot()
    {
        $this->loadRoutesFrom(__DIR__ . '/routes/web.php');

        $this->loadViewsFrom(
            __DIR__ . '/../resources/views',
            'logviewer'
        );

        $this->publishes([
            __DIR__ . '/../config/logviewer.php' =>
            config_path('logviewer.php'),
        ], 'logviewer-config');

        $this->publishes([
            __DIR__ . '/../resources/views' =>
            resource_path('views/vendor/logviewer'),
        ], 'logviewer-views');

        $this->publishes([
            __DIR__ . '/../resources/js' =>
            resource_path('js'),
        ], 'logviewer-vue');
    }

    /**
     * Registra los servicios utilizados
     * por el paquete dentro del contenedor
     * de dependencias de Laravel.
     *
     * Servicios registrados:
     *
     * - LogParserService
     * - RendererFactory
     * - LogFileService
     *
     * También fusiona la configuración
     * predeterminada del paquete con la
     * configuración de la aplicación.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/logviewer.php',
            'logviewer'
        );

        $this->app->singleton(LogParserService::class);

        $this->app->singleton(RendererFactory::class);

        $this->app->singleton(LogFileService::class);
    }
}
