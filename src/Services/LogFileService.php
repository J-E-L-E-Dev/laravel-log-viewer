<?php

namespace Ahinest\LogViewer\Services;

/**
 * Servicio encargado de administrar los
 * archivos de logs disponibles en el proyecto.
 *
 * Responsabilidades:
 *
 * - Descubrir archivos *.log dentro de storage/logs.
 * - Obtener información de cada archivo.
 * - Validar la existencia de un log.
 * - Resolver la ruta física de un archivo.
 * - Generar listados para filtros y validaciones.
 *
 * Este servicio no lee ni interpreta el contenido
 * de los logs. Su única responsabilidad es la
 * gestión de archivos.
 */
class LogFileService
{
    /**
     * Obtiene la lista de nombres de logs permitidos.
     *
     * El resultado se almacena temporalmente en caché
     * para evitar acceder continuamente al sistema
     * de archivos.
     *
     * Ejemplo:
     *
     * [
     *     'laravel.log',
     *     'payments.log',
     *     'queue.log',
     * ]
     *
     * @return array<int, string>
     */
    public function allowed(): array
    {
        return cache()->remember(
            'logviewer.allowed_logs',
            now()->addMinute(),
            fn() => collect($this->all())
                ->pluck('name')
                ->all()
        );
    }

    /**
     * Obtiene todos los archivos de log disponibles
     * junto con su información descriptiva.
     *
     * El log configurado como default_log se coloca
     * al inicio de la colección.
     *
     * Ejemplo:
     *
     * [
     *     [
     *         'name' => 'laravel.log',
     *         'size' => 1245532,
     *         'size_human' => '1.19 MB',
     *         'modified_at' => '2026-06-08 18:00:00',
     *     ],
     * ]
     *
     * @return array<int, array{
     *     name:string,
     *     size:int,
     *     size_human:string,
     *     modified_at:string
     * }>
     */
    public function all(): array
    {
        $default = config(
            'logviewer.default_log'
        );

        return collect(
            glob(
                storage_path('logs/*.log')
            )
        )
            ->map(function ($file) {
                $size = filesize($file);

                return [

                    'name' => basename($file),

                    'size' => $size,

                    'size_human' => $this->formatBytes(
                        $size
                    ),

                    'modified_at' => date(
                        'Y-m-d H:i:s',
                        filemtime($file)
                    ),

                ];
            })
            ->sortBy('name')
            ->sortByDesc(
                fn($file) =>
                $file['name'] === $default
            )
            ->values()
            ->all();
    }

    /**
     * Determina si un archivo de log existe dentro
     * de storage/logs y forma parte de los logs
     * detectados por el paquete.
     *
     * @param string $file
     *
     * @return bool
     */
    public function exists(string $file): bool
    {
        return collect($this->all())
            ->pluck('name')
            ->contains($file);
    }

    /**
     * Obtiene la ruta absoluta de un archivo de log.
     *
     * Ejemplo:
     *
     * storage/logs/laravel.log
     *
     * @param string $file
     *
     * @return string
     */
    public function path(string $file): string
    {
        return storage_path(
            'logs/' . $file
        );
    }

    /**
     * Convierte una cantidad de bytes a un formato
     * legible para humanos.
     *
     * Ejemplos:
     *
     * 1024 => 1 KB
     * 1048576 => 1 MB
     * 1073741824 => 1 GB
     *
     * @param int $bytes
     * @param int $precision
     *
     * @return string
     */
    protected function formatBytes(
        int $bytes,
        int $precision = 2
    ): string {

        $units = [
            'B',
            'KB',
            'MB',
            'GB',
            'TB',
        ];

        $bytes = max($bytes, 0);

        $pow = floor(
            ($bytes
                ? log($bytes)
                : 0
            ) / log(1024)
        );

        $pow = min(
            $pow,
            count($units) - 1
        );

        $bytes /= pow(
            1024,
            $pow
        );

        return round(
            $bytes,
            $precision
        ) . ' ' . $units[$pow];
    }
}
