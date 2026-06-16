<?php

namespace Ahinest\LogViewer\Renderers;

/**
 * Renderer encargado de mostrar los logs
 * mediante una página Inertia.
 *
 * Requiere que el paquete Inertia esté
 * instalado y configurado dentro de la
 * aplicación consumidora.
 *
 * La página utilizada puede configurarse
 * desde logviewer.inertia_page.
 */
class InertiaRenderer
{
    public function render(
        array $logs,
        array $availableLogs,
        string $selectedLog,
        array $currentFilters
    ) {
        if (! class_exists(\Inertia\Inertia::class)) {
            abort(500, 'Inertia no está instalado');
        }

        return \Inertia\Inertia::render(
            config('logviewer.inertia_page'),
            compact(
                'logs',
                'availableLogs',
                'selectedLog',
                'currentFilters'
            )
        );
    }
}
