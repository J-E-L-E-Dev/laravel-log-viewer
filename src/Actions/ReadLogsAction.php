<?php

namespace Ahinest\LogViewer\Actions;

use Ahinest\LogViewer\Services\LogParserService;
use Ahinest\LogViewer\DTOs\LogFilters;
use Ahinest\LogViewer\Services\LogFileService;

/**
 * Acción encargada de obtener los registros
 * de un archivo de log y aplicar los filtros
 * solicitados por el usuario.
 *
 * Responsabilidades:
 *
 * - Resolver el archivo de log a consultar.
 * - Leer y parsear el contenido del log.
 * - Transformar los DTOs LogEntry a arrays.
 * - Aplicar filtros de búsqueda.
 * - Retornar únicamente los registros resultantes.
 *
 * Esta clase actúa como capa de orquestación
 * entre los servicios de archivos, parsing
 * y filtrado.
 */
class ReadLogsAction
{
    /**
     * @param LogParserService $parser
     * Servicio encargado de interpretar el
     * contenido de los archivos de log.
     *
     * @param LogFileService $files
     * Servicio encargado de gestionar los
     * archivos de log disponibles.
     */
    public function __construct(
        protected LogParserService $parser,
        protected LogFileService $files,
    ) {}

    /**
     * Obtiene los registros del archivo
     * seleccionado y aplica los filtros
     * definidos por el usuario.
     *
     * Si no se especifica un archivo de log,
     * se utilizará el configurado en:
     *
     * config('logviewer.default_log')
     *
     * Filtros soportados:
     *
     * - log
     * - level
     * - channel
     * - hasException
     * - date
     *
     * Ejemplo:
     *
     * $logs = $action->handle(
     *     new LogFilters(
     *         log: 'laravel.log',
     *         level: 'error',
     *         date: '2026-06-12'
     *     )
     * );
     *
     * @param LogFilters $filters
     *
     * @return array<int, array{
     *     date:string,
     *     channel:string,
     *     level:string,
     *     message:string,
     *     context:mixed,
     *     hasException:bool,
     *     fullText:string
     * }>
     */
    public function handle(LogFilters $filters): array
    {
        $logFile = $filters->log
            ?? config('logviewer.default_log');

        return collect(
            $this->parser->parse(
                $this->files->path($logFile)
            )
        )
            ->map(fn($log) => $log->toArray())
            ->when(
                $filters->level,
                fn($query) => $query->where(
                    'level',
                    $filters->level
                )
            )

            ->when(
                $filters->channel,
                fn($query) => $query->where(
                    'channel',
                    $filters->channel
                )
            )

            ->when(
                ! is_null($filters->hasException),
                fn($query) => $query->where(
                    'hasException',
                    $filters->hasException
                )
            )

            ->when(
                $filters->date,
                fn($query) => $query->filter(
                    fn($log) =>
                    str_starts_with(
                        $log['date'],
                        $filters->date
                    )
                )
            )

            ->values()
            ->all();
    }
}
