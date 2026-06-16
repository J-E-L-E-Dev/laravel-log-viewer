<?php

namespace Ahinest\LogViewer\DTOs;

use Ahinest\LogViewer\Http\Requests\FilterLogsRequest;

/**
 * DTO que representa los filtros aplicados
 * a la consulta de registros de log.
 *
 * Este objeto desacopla la capa HTTP de la
 * lógica de lectura de logs, permitiendo que
 * las acciones trabajen con una estructura
 * tipada e inmutable.
 */
class LogFilters
{
    /**
     * @param string|null $log
     * Nombre del archivo de log a consultar.
     *
     * Ejemplo:
     * laravel.log
     * payments.log
     *
     * @param string|null $level
     * Nivel de severidad del registro.
     *
     * Valores comunes:
     * debug
     * info
     * notice
     * warning
     * error
     * critical
     * alert
     * emergency
     *
     * @param string|null $channel
     * Canal del log.
     *
     * Ejemplo:
     * local
     * stack
     * daily
     *
     * @param bool|null $hasException
     * Indica si se deben mostrar únicamente
     * registros que contienen una excepción.
     *
     * true  => sólo registros con excepción.
     * false => sólo registros sin excepción.
     * null  => todos los registros.
     *
     * @param string|null $date
     * Fecha del registro en formato Y-m-d.
     *
     * Ejemplo:
     * 2026-06-12
     */
    public function __construct(
        public readonly ?string $log = null,
        public readonly ?string $level = null,
        public readonly ?string $channel = null,
        public readonly ?bool $hasException = null,
        public readonly ?string $date = null,
    ) {}

    /**
     * Crea una instancia del DTO a partir de una
     * petición validada.
     *
     * Este método centraliza la transformación de
     * los filtros HTTP en un objeto de dominio,
     * evitando que el controlador tenga que conocer
     * la estructura interna del DTO.
     *
     * Campos soportados:
     *
     * - log
     * - level
     * - channel
     * - has_exception
     * - date
     *
     * @param FilterLogsRequest $request
     *
     * @return self
     */
    public static function fromRequest(
        FilterLogsRequest $request
    ): self {

        return new self(
            log: $request->validated('log'),
            level: $request->validated('level'),
            channel: $request->validated('channel'),
            hasException: $request->has('has_exception')
                ? (bool) $request->validated('has_exception')
                : null,
            date: $request->validated('date'),
        );
    }

    /**
     * Convierte el DTO en un arreglo serializable.
     *
     * Útil para:
     *
     * - Enviar filtros a vistas Blade.
     * - Compartir filtros con Inertia.
     * - Retornar filtros desde una API.
     * - Persistir el estado actual de búsqueda.
     *
     * Las claves retornadas coinciden con los
     * nombres utilizados en la URL y en los
     * formularios de filtrado.
     *
     * @return array{
     *     log:?string,
     *     level:?string,
     *     channel:?string,
     *     has_exception:?bool,
     *     date:?string
     * }
     */
    public function toArray(): array
    {
        return [
            'log' => $this->log,
            'level' => $this->level,
            'channel' => $this->channel,
            'has_exception' => $this->hasException,
            'date' => $this->date,
        ];
    }
}
