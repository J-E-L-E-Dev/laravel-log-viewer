<?php

use Illuminate\Support\Facades\Route;
use Ahinest\LogViewer\Http\Controllers\LogViewerController;

/*
|--------------------------------------------------------------------------
| Laravel Log Viewer Routes
|--------------------------------------------------------------------------
|
| Rutas expuestas por el paquete para visualizar y administrar
| archivos de logs.
|
| Middleware configurable:
|
| - logviewer.middleware
| - logviewer.middleware_clear
|
| Prefix configurable:
|
| - logviewer.route
|
*/

Route::prefix(config('logviewer.route'))
    ->group(function () {

        Route::middleware(
            config('logviewer.middleware')
        )->group(function () {

            /**
             * Rutas de visualización.
             *
             * GET /{prefix}/log-viewer
             *
             * Permite:
             * - Listar registros.
             * - Aplicar filtros.
             * - Seleccionar archivos de log.
             * - Consultar detalles de cada entrada.
             */
            Route::get(
                '/log-viewer',
                [LogViewerController::class, 'index']
            )->name('log.index');
        });

        Route::middleware(
            config('logviewer.middleware_clear')
        )->group(function () {

            /**
             * Rutas administrativas.
             *
             * DELETE /{prefix}/clear
             *
             * Permite vaciar el contenido de un archivo
             * de log autorizado mediante:
             *
             * config('logviewer.clearable_logs')
             *
             * Esta ruta puede protegerse con middleware
             * específicos independientes de los utilizados
             * para la visualización.
             */
            Route::delete(
                '/clear',
                [LogViewerController::class, 'clear']
            )->name('log.clear');
        });
    });
