<?php

namespace Ahinest\LogViewer\Actions;

use Ahinest\LogViewer\Services\LogFileService;

/**
 * Acción encargada de vaciar el contenido
 * de un archivo de log.
 *
 * Antes de realizar la operación se validan:
 *
 * - Que la limpieza de logs esté habilitada.
 * - Que el archivo pertenezca a la lista de
 *   logs autorizados para limpieza.
 * - Que el archivo tenga permisos de escritura.
 *
 * La acción conserva el archivo físico y
 * únicamente elimina su contenido.
 *
 * Ejemplo:
 *
 * $action->handle('laravel.log');
 */
class ClearLogAction
{
    /**
     * @param LogFileService $files
     * Servicio encargado de la gestión de
     * archivos de log.
     */
    public function __construct(
        protected LogFileService $files
    ) {}

    /**
     * Vacía el contenido de un archivo de log.
     *
     * El archivo debe encontrarse dentro de la
     * configuración:
     *
     * config('logviewer.clearable_logs')
     *
     * Además, la opción:
     *
     * config('logviewer.allow_clear')
     *
     * debe estar habilitada.
     *
     * Ejemplo:
     *
     * $action->handle('laravel.log');
     *
     * @param string $log
     * Nombre del archivo de log.
     *
     * @throws \RuntimeException
     * Cuando el archivo no tiene permisos
     * de escritura.
     *
     * @return bool
     * true si la operación fue exitosa.
     */
    public function handle(string $log): bool
    {
        if (! config('logviewer.allow_clear')) {

            abort(
                403,
                'La limpieza de logs está deshabilitada.'
            );
        }

        if (! in_array(
            $log,
            config('logviewer.clearable_logs', []),
            true
        )) {
            abort(403);
        }

        $path = $this->files->path($log);

        if (! is_writable($path)) {

            throw new \RuntimeException(
                "El archivo no tiene permisos de escritura."
            );
        }

        

        return file_put_contents(
            $path,
            ''
        ) !== false;
    }
}
