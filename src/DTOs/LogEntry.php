<?php

namespace Ahinest\LogViewer\DTOs;

/**
 * DTO que representa una entrada individual
 * de un archivo de log.
 *
 * Cada instancia contiene la información
 * extraída y normalizada por el parser,
 * independientemente del formato original
 * del archivo.
 *
 * Ejemplo:
 *
 * [2026-06-12 10:15:20] local.ERROR:
 * Usuario no encontrado
 * {"user_id":1}
 *
 * Se transforma en:
 *
 * new LogEntry(
 *     date: '2026-06-12 10:15:20',
 *     channel: 'local',
 *     level: 'error',
 *     message: 'Usuario no encontrado',
 *     context: [
 *         'user_id' => 1,
 *     ],
 *     hasException: false,
 *     fullText: '...'
 * );
 *
 * Este DTO es inmutable mediante el uso de
 * propiedades readonly.
 */
class LogEntry
{
    /**
     * @param string $date
     * Fecha y hora del registro.
     *
     * Formato habitual:
     * Y-m-d H:i:s
     *
     * @param string $channel
     * Canal desde el que se generó el log.
     *
     * Ejemplos:
     * local
     * stack
     * daily
     *
     * @param string $level
     * Nivel de severidad del registro.
     *
     * Valores habituales:
     * debug
     * info
     * notice
     * warning
     * error
     * critical
     * alert
     * emergency
     *
     * @param string $message
     * Mensaje principal del registro.
     *
     * @param array|null $context
     * Contexto asociado al log.
     *
     * Normalmente corresponde al contenido
     * JSON o array registrado por Laravel.
     *
     * @param bool $hasException
     * Indica si el registro contiene una
     * excepción o stack trace.
     *
     * @param string $fullText
     * Bloque completo del log tal como fue
     * encontrado en el archivo.
     */
    public function __construct(
        public readonly string $date,
        public readonly string $channel,
        public readonly string $level,
        public readonly string $message,
        public readonly ?array $context,
        public readonly bool $hasException,
        public readonly string $fullText,
    ) {
    }

    /**
     * Convierte la entrada de log a un array.
     *
     * Resulta útil para serialización,
     * respuestas JSON y renderizado de vistas.
     *
     * @return array{
     *     date:string,
     *     channel:string,
     *     level:string,
     *     message:string,
     *     context:?array,
     *     hasException:bool,
     *     fullText:string
     * }
     */
    public function toArray(): array
    {
        return [
            'date' => $this->date,
            'channel' => $this->channel,
            'level' => $this->level,
            'message' => $this->message,
            'context' => $this->context,
            'hasException' => $this->hasException,
            'fullText' => $this->fullText,
        ];
    }
}