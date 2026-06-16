<?php

namespace Ahinest\LogViewer\Renderers;

/**
 * Renderer encargado de exponer los logs
 * mediante una respuesta JSON.
 *
 * Esta implementación resulta útil para:
 *
 * - APIs REST.
 * - Integraciones externas.
 * - Frontends desacoplados.
 * - Consumo mediante AJAX.
 *
 * La respuesta contiene únicamente los
 * datos procesados por el paquete.
 */
class ApiRenderer
{
    /**
     * Genera una respuesta JSON con los logs.
     *
     * Estructura:
     *
     * {
     *     "logs": [...]
     * }
     *
     * @param array $logs
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function render(
        array $logs,
        array $availableLogs,
        string $selectedLog,
        array $currentFilters
    ) {
        return response()->json(
            compact(
                'logs',
                'availableLogs',
                'selectedLog',
                'currentFilters'
            )
        );
    }
}
