<?php

namespace Ahinest\LogViewer\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Ahinest\LogViewer\Services\LogFileService;

/**
 * Request encargado de validar las peticiones
 * de limpieza de archivos de log.
 *
 * Su objetivo es garantizar que únicamente se
 * solicite la limpieza de archivos de log
 * existentes y reconocidos por el paquete.
 *
 * La validación de permisos y autorización para
 * limpiar logs se realiza mediante:
 *
 * - Middleware de la ruta.
 * - Configuración del paquete.
 * - ClearLogAction.
 *
 * Esta clase únicamente valida la estructura
 * y validez de los datos recibidos.
 */
class ClearLogRequest extends FormRequest
{
    /**
     * Determina si el usuario está autorizado
     * para realizar la petición.
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
     * Reglas de validación para la limpieza
     * de archivos de log.
     *
     * Validaciones:
     *
     * log:
     *     Campo obligatorio.
     *
     *     Debe existir dentro de los archivos
     *     detectados por LogFileService.
     *
     * Nota:
     *
     * Que un archivo sea válido no implica que
     * pueda ser limpiado. La validación de los
     * logs autorizados para limpieza se realiza
     * posteriormente en ClearLogAction mediante:
     *
     * config('logviewer.clearable_logs')
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [

            'log' => [

                'required',

                Rule::in(
                    app(LogFileService::class)
                        ->allowed()
                ),

            ],

        ];
    }
}