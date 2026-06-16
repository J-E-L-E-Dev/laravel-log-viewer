<?php

/**
 * |--------------------------------------------------------------------------
 * | Laravel Log Viewer Configuration
 * |--------------------------------------------------------------------------
 * |
 * | Archivo de configuración principal del paquete.
 * |
 * | Desde aquí es posible:
 * |
 * | - Configurar middleware de acceso.
 * | - Definir el log por defecto.
 * | - Habilitar o deshabilitar limpieza de logs.
 * | - Configurar permisos de limpieza.
 * | - Seleccionar el tipo de interfaz.
 * | - Definir filtros permitidos.
 * |
 */
$defaultMiddleware = [
    'web',
    'auth',
];


return [
    /**
     * Log mostrado por defecto al ingresar
     * al visor.
     *
     * Debe corresponder a un archivo existente
     * dentro de storage/logs.
     */
    'default_log' => 'laravel.log',

    /**
     * Middleware utilizados para acceder
     * al visor de logs.
     *
     * Ejemplos:
     *
     * - auth
     * - can:view logs
     * - permission:view logs
     * - role:super-admin
     *
     * Compatible con:
     *
     * - Laravel Authorization
     * - Spatie Laravel Permission
     */
    'middleware' => [
        ...$defaultMiddleware
    ],

    /**
     * Middleware utilizados para la ruta
     * de limpieza de logs.
     *
     * Permite utilizar reglas de acceso
     * distintas a las de visualización.
     *
     * Ejemplo:
     *
     * 'middleware_clear' => [
     *     ...$defaultMiddleware,
     *     'permission:clear logs',
     * ]
     */
    'middleware_clear' => [
        ...$defaultMiddleware
    ],

    /**
     * Habilita o deshabilita la funcionalidad
     * de limpieza de archivos de log.
     *
     * false:
     *     La ruta responderá con 403.
     *
     * true:
     *     Permitirá la ejecución de la acción
     *     siempre que el archivo sea válido
     *     y esté autorizado.
     */
    'allow_clear' => false,

    /**
     * Archivos autorizados para limpieza.
     *
     * Aunque un archivo exista dentro de
     * storage/logs, únicamente podrá ser
     * vaciado si aparece en esta lista.
     */
    'clearable_logs' => [
        'laravel.log',
    ],

    /**
     * Prefijo de las rutas del paquete.
     *
     * Ejemplo:
     *
     * logs/log-viewer
     * logs/clear
     */
    'route' => 'logs',

    /**
     * Tipo de interfaz utilizada por el visor.
     *
     * Valores soportados:
     *
     * - blade
     * - inertia
     * - api
     */
    'ui' => env('LOG_VIEWER_UI', 'blade'),

    /**
     * Componente Inertia utilizado cuando
     * ui = inertia.
     */
    'inertia_page' => 'LogViewer/Index',

    /**
     * Vista Blade utilizada cuando
     * ui = blade.
     */
    'blade_view' => 'logviewer::index',

    /**
     * Canales permitidos para filtrado.
     *
     * Nota:
     * Laravel permite canales personalizados.
     * Si tu aplicación utiliza canales
     * adicionales deberán agregarse aquí.
     */
    'allowed_channels' => [
        'local',
        'stack',
        'daily',
    ],

    /**
     * Niveles de log permitidos para filtrado.
     *
     * Basados en los niveles estándar
     * definidos por Monolog.
     */
    'allowed_levels' => [

        'debug',

        'info',

        'notice',

        'warning',

        'error',

        'critical',

        'alert',

        'emergency',

    ],
];
