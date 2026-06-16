<?php

namespace Ahinest\LogViewer\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Redirect;
use Ahinest\LogViewer\Actions\ReadLogsAction;
use Ahinest\LogViewer\Actions\ClearLogAction;
use Ahinest\LogViewer\Support\RendererFactory;
use Ahinest\LogViewer\DTOs\LogFilters;
use Ahinest\LogViewer\Services\LogFileService;
use Ahinest\LogViewer\Http\Requests\FilterLogsRequest;
use Ahinest\LogViewer\Http\Requests\ClearLogRequest;

/**
 * Controlador principal del paquete Laravel Log Viewer.
 *
 * Responsable de:
 *
 * - Procesar filtros de consulta.
 * - Obtener registros de log.
 * - Obtener archivos disponibles.
 * - Delegar el renderizado según la UI configurada.
 * - Ejecutar la limpieza de logs.
 *
 * Este controlador actúa como capa de orquestación
 * entre Requests, Actions, Services y Renderers,
 * manteniendo la lógica de negocio fuera de la capa HTTP.
 */
class LogViewerController extends Controller
{
    /**
     * @param ReadLogsAction $readLogs
     * Acción encargada de leer y filtrar logs.
     *
     * @param RendererFactory $factory
     * Factory utilizada para resolver el renderer
     * configurado (Blade, Inertia o API).
     *
     * @param LogFileService $files
     * Servicio encargado de administrar los
     * archivos de log disponibles.
     */
    public function __construct(
        protected ReadLogsAction $readLogs,
        protected RendererFactory $factory,
        protected LogFileService $files,
    ) {}

    /**
     * Muestra el visor de logs.
     *
     * Flujo:
     *
     * 1. Valida los filtros recibidos.
     * 2. Construye el DTO LogFilters.
     * 3. Obtiene los registros filtrados.
     * 4. Obtiene la lista de archivos disponibles.
     * 5. Determina el log seleccionado.
     * 6. Delega el renderizado al renderer configurado.
     *
     * Filtros soportados:
     *
     * - log
     * - level
     * - channel
     * - has_exception
     * - date
     *
     * @param FilterLogsRequest $request
     *
     * @return mixed
     */
    public function index(FilterLogsRequest $request)
    {
        $filters = LogFilters::fromRequest(
            $request
        );

        $currentFilters = $filters->toArray();
        
        $logs = $this->readLogs->handle(
            $filters
        );

        $availableLogs = $this->files->all();

        $selectedLog = $filters->log
            ?? config('logviewer.default_log');

        return $this->factory
            ->make()
            ->render(
                $logs,
                $availableLogs,
                $selectedLog,
                $currentFilters
            );
    }

    /**
     * Limpia el contenido de un archivo de log.
     *
     * La validación del archivo se realiza mediante
     * ClearLogRequest.
     *
     * Las reglas de autorización y permisos son
     * aplicadas mediante:
     *
     * - Middleware configurados.
     * - Configuración del paquete.
     * - ClearLogAction.
     *
     * Tras una limpieza exitosa se redirige al visor
     * principal mostrando un mensaje de estado.
     *
     * @param ClearLogRequest $request
     * @param ClearLogAction $action
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function clear(
        ClearLogRequest $request,
        ClearLogAction $action
    ) {

        $action->handle(
            $request->validated('log')
        );

        return Redirect::route('log.index')->with(
            'status',
            'Log limpiado correctamente.'
        );
    }
}
