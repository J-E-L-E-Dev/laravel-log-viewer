<?php

namespace Ahinest\LogViewer\Services;

use Ahinest\LogViewer\DTOs\LogEntry;

/**
 * Servicio encargado de interpretar archivos
 * de logs de Laravel y transformarlos en
 * objetos LogEntry.
 *
 * Funcionalidades:
 *
 * - Detecta entradas individuales de log.
 * - Extrae fecha, canal y nivel.
 * - Obtiene el mensaje principal.
 * - Detecta contextos JSON.
 * - Identifica registros con excepciones.
 * - Conserva el bloque completo original.
 *
 * Formatos soportados:
 *
 * Formato clásico:
 *
 * [2026-06-12 10:00:00] local.ERROR:
 * Undefined array key "name"
 *
 * Formato con contexto:
 *
 * [2026-06-12 10:00:00] local.ERROR:
 * Usuario no encontrado
 * {"user_id":1}
 *
 * Formato con excepción:
 *
 * [2026-06-12 10:00:00] local.ERROR:
 * Error procesando pedido
 * {"exception":"..."}
 *
 * El resultado siempre será una colección
 * de objetos LogEntry normalizados.
 */
class LogParserService
{
    /**
     * Expresión regular utilizada para detectar
     * el inicio de cada registro dentro del archivo.
     *
     * Capturas:
     *
     * 1. Fecha
     * 2. Canal
     * 3. Nivel
     *
     * Ejemplo:
     *
     * [2026-06-12 10:00:00] local.ERROR:
     *
     * fecha   => 2026-06-12 10:00:00
     * channel => local
     * level   => ERROR
     *
     * @var string
     */
    protected string $pattern =
        '/^\[(.*?)\]\s+(.*?)\.([A-Z]+):/m';

    
    /**
     * Procesa un archivo de log y devuelve
     * todas las entradas encontradas.
     *
     * Si el archivo no existe se retorna
     * un array vacío.
     *
     * Cada bloque detectado es convertido
     * en una instancia de LogEntry.
     *
     * @param string $path
     * Ruta absoluta del archivo de log.
     *
     * @return array<int, LogEntry>
     */
    public function parse(string $path): array
    {
        if (! file_exists($path)) {
            return [];
        }

        $content = file_get_contents($path);

        preg_match_all(
            $this->pattern,
            $content,
            $matches,
            PREG_OFFSET_CAPTURE
        );

        $logs = [];

        $totalMatches = count($matches[0]);

        for ($i = 0; $i < $totalMatches; $i++) {

            $start = $matches[0][$i][1];

            $end = $i < ($totalMatches - 1)
                ? $matches[0][$i + 1][1]
                : strlen($content);

            $block = trim(
                substr(
                    $content,
                    $start,
                    $end - $start
                )
            );

            $parsed = $this->extractData($block);

            $logs[] = new LogEntry(
                date: $matches[1][$i][0],
                channel: $matches[2][$i][0],
                level: strtolower($matches[3][$i][0]),
                message: $parsed['message'],
                context: $parsed['context'],
                hasException: $parsed['hasException'],
                fullText: $block,
            );
        }

        return $logs;
    }

    /**
     * Extrae la información relevante de un
     * bloque individual de log.
     *
     * Información obtenida:
     *
     * - message
     * - context
     * - hasException
     *
     * Cuando el mensaje no aparece en la
     * primera línea del registro, se intenta
     * obtener desde la siguiente línea útil.
     *
     * Ejemplo:
     *
     * [fecha] local.ERROR:
     * Undefined array key "id"
     *
     * Resultado:
     *
     * [
     *     'message' => 'Undefined array key "id"',
     *     'context' => null,
     *     'hasException' => false,
     * ]
     *
     * @param string $block
     *
     * @return array{
     *     message:string,
     *     context:?array,
     *     hasException:bool
     * }
     */
    protected function extractData(string $block): array
    {
        $lines = preg_split(
            '/\r\n|\r|\n/',
            trim($block)
        );

        if (empty($lines)) {
            return [
                'message' => '',
                'context' => null,
                'hasException' => false,
            ];
        }

        $firstLine = array_shift($lines);

        preg_match(
            '/^\[(.*?)\]\s+(.*?)\.([A-Z]+):\s*(.*)$/',
            $firstLine,
            $matches
        );

        $message = trim(
            $matches[4] ?? ''
        );

        $context = null;

        foreach ($lines as $line) {

            $line = trim($line);

            if ($line === '') {
                continue;
            }

            if (
                str_starts_with($line, '{')
                && $this->isJson($line)
            ) {
                $context = json_decode(
                    $line,
                    true
                );

                break;
            }
        }

        /**
         * Caso:
         *
         * [fecha] local.ERROR:
         * Undefined array key...
         */
        if (
            empty($message)
            && ! empty($lines)
        ) {
            $message = trim(
                collect($lines)
                    ->first(fn ($line) => trim($line) !== '')
            );
        }

        $hasException =
            isset($context['exception']);

        return [
            'message' => $message,
            'context' => $context,
            'hasException' => $hasException,
        ];
    }

    /**
     * Determina si una cadena contiene
     * un JSON válido.
     *
     * Utilizado para detectar contextos
     * serializados dentro de los registros.
     *
     * @param string $value
     *
     * @return bool
     */
    protected function isJson(string $value): bool
    {
        json_decode($value);

        return json_last_error() === JSON_ERROR_NONE;
    }
}