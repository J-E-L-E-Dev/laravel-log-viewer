<?php

namespace Ahinest\LogViewer\Renderers;

/**
 * Renderer encargado de mostrar los logs
 * mediante una vista Blade tradicional.
 *
 * La vista utilizada puede configurarse
 * desde el archivo config/logviewer.php
 * mediante la opción blade_view.
 *
 * Esta implementación es adecuada para
 * aplicaciones Laravel sin Inertia.
 */ 
class BladeRenderer
{
    public function render(
        array $logs,
        array $availableLogs,
        string $selectedLog,
        array $currentFilters
    ) {
        return view(
            config('logviewer.blade_view'),
            compact(
                'logs',
                'availableLogs',
                'selectedLog',
                'currentFilters'
            )
        );
    }
}
