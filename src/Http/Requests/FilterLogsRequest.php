<?php

namespace Ahinest\LogViewer\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Ahinest\LogViewer\Services\LogFileService;

/**
 * Request encargado de validar los filtros
 * utilizados para consultar registros de log.
 *
 * El objetivo de esta clase es garantizar que
 * únicamente se procesen valores permitidos
 * por la configuración del paquete.
 *
 * Filtros soportados:
 *
 * - log
 * - level
 * - channel
 * - has_exception
 * - date
 *
 * Configuración relacionada:
 *
 * - logviewer.allowed_levels
 * - logviewer.allowed_channels
 *
 * Los archivos de log válidos son obtenidos
 * dinámicamente mediante LogFileService.
 */
class FilterLogsRequest extends FormRequest
{
    /**
     * Determina si el usuario está autorizado
     * para realizar esta petición.
     *
     * La autorización se delega a los middleware
     * configurados en las rutas del paquete.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Reglas de validación para los filtros
     * de búsqueda de logs.
     *
     * Validaciones:
     *
     * log:
     *     Debe existir dentro de los archivos
     *     detectados por LogFileService.
     *
     * level:
     *     Debe pertenecer a los niveles
     *     permitidos en la configuración.
     *
     * channel:
     *     Debe pertenecer a los canales
     *     permitidos en la configuración.
     *
     * has_exception:
     *     Debe ser un valor booleano.
     *
     * date:
     *     Debe tener formato Y-m-d.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'log' => [
                'nullable',
                Rule::in(
                    app(LogFileService::class)
                        ->allowed()
                ),
            ],

            'level' => [
                'nullable',
                Rule::in(
                    config('logviewer.allowed_levels')
                ),
            ],

            'channel' => [
                'nullable',
                Rule::in(
                    config('logviewer.allowed_channels')
                ),
            ],

            'has_exception' => [
                'nullable',
                'boolean',
            ],

            'date' => [
                'nullable',
                'date_format:Y-m-d',
            ],

        ];
    }
}
