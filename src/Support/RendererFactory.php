<?php

namespace Ahinest\LogViewer\Support;

use Ahinest\LogViewer\Renderers\ApiRenderer;
use Ahinest\LogViewer\Renderers\BladeRenderer;
use Ahinest\LogViewer\Renderers\InertiaRenderer;

/**
 * Factory encargada de resolver el renderer
 * adecuado según la configuración del paquete.
 *
 * Permite desacoplar el controlador de la
 * implementación concreta utilizada para
 * renderizar los logs.
 *
 * Configuración soportada:
 *
 * - blade
 * - api
 * - inertia
 *
 * Ejemplo:
 *
 * config/logviewer.php
 *
 * 'ui' => 'blade'
 *
 * $renderer = app(RendererFactory::class)
 *     ->make();
 *
 * return $renderer->render($logs);
 */
class RendererFactory
{
    /**
     * Obtiene la implementación del renderer
     * configurada en logviewer.ui.
     *
     * @throws \Exception
     *
     * @return ApiRenderer|BladeRenderer|InertiaRenderer
     */
    public function make()
    {
        return match(config('logviewer.ui')) {

            'blade' => app(BladeRenderer::class),

            'api' => app(ApiRenderer::class),

            'inertia' => app(InertiaRenderer::class),

            default => throw new \Exception(
                'UI no soportada'
            ),
        };
    }
}