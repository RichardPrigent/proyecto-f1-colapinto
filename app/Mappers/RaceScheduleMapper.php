<?php

namespace App\Mappers;

/**
 * Clase RaceScheduleMapper
 *
 * Esta clase es responsable de mapear los datos del calendario de carreras desde un array de entrada a un array de respuesta estructurado.
 *
 * Métodos:
 * - map(array $data): array
 *   Mapea los datos de entrada a un array de respuesta estructurado.
 *
 * - hasRaces(array $data): bool
 *   Verifica si los datos de entrada contienen información de carreras.
 *
 * - getSeasonYear(array $data): ?string
 *   Obtiene el año de la temporada de los datos de entrada.
 *
 * - getTotalRaces(array $data): int
 *   Obtiene el número total de carreras de los datos de entrada.
 *
 * - getLimit(array $data): string
 *   Obtiene el valor del límite de los datos de entrada.
 *
 * - getOffset(array $data): string
 *   Obtiene el valor del offset de los datos de entrada.
 *
 * - mapRaces(array $races): array
 *   Mapea los datos de las carreras a un array estructurado.
 *
 * - mapRace(array $race): ?array
 *   Mapea los datos de una sola carrera a un array estructurado.
 *
 * - isValidRace(array $race): bool
 *   Valida si los datos de la carrera contienen los campos requeridos.
 *
 * - mapCircuit(array $circuit): array
 *   Mapea los datos del circuito a un array estructurado.
 *
 * - mapLocation(array $location): array
 *   Mapea los datos de la ubicación a un array estructurado.
 *
 * - formatPractice(array $practice): ?array
 *   Formatea los datos de la sesión de práctica.
 *
 * - errorResponse(string $message): array
 *   Genera un array de respuesta de error con el mensaje dado.
 *
 * - buildResponse(string $series, string $limit, string $offset, int $totalRaces, string $year, array $races): array
 *   Construye el array de respuesta final con los datos mapeados.
 */
class RaceScheduleMapper
{
    /**
     * Mapea los datos proporcionados del calendario de carreras a un array estructurado.
     *
     * @param array $data Los datos del calendario de carreras a mapear.
     * @return array Los datos mapeados del calendario de carreras o una respuesta de error si los datos son inválidos.
     * La función realiza los siguientes pasos:
     * 1. Verifica si los datos proporcionados contienen carreras. Si no, devuelve una respuesta de error.
     * 2. Recupera el año de la temporada de los datos. Si el año no está definido, devuelve una respuesta de error.
     * 3. Recupera el número total de carreras de los datos.
     * 4. Establece la serie a 'f1'.
     * 5. Recupera el límite y el offset de los datos.
     * 6. Mapea las carreras de los datos proporcionados.
     * 7. Construye y devuelve la respuesta con la serie, el límite, el offset, el total de carreras, el año y las carreras mapeadas.
     */
    public function map(array $data): array
    {
        if (!$this->hasRaces($data)) {
            return $this->errorResponse('No se encontraron carreras en los datos proporcionados.');
        }

        $year = $this->getSeasonYear($data);
        if (is_null($year)) {
            return $this->errorResponse('El año de la temporada no está definido.');
        }

        $totalRaces = $this->getTotalRaces($data);
        $series = 'f1';
        $limit = $this->getLimit($data);
        $offset = $this->getOffset($data);

        $races = $this->mapRaces($data['MRData']['RaceTable']['Races']);

        return $this->buildResponse($series, $limit, $offset, $totalRaces, $year, $races);
    }

    /**
     * Verifica si los datos proporcionados contienen información de carreras.
     *
     * Este método verifica la presencia de la clave 'Races' dentro de las claves anidadas
     * 'MRData' y 'RaceTable' del array de datos dado.
     *
     * @param array $data El array de datos a verificar para información de carreras.
     * @return bool Devuelve true si la clave 'Races' está presente, de lo contrario false.
     */
    private function hasRaces(array $data): bool
    {
        return isset($data['MRData']['RaceTable']['Races']);
    }

    /**
     * Obtiene el año de la temporada de los datos proporcionados.
     *
     * @param array $data El array de datos que contiene información de carreras.
     * @return string|null El año de la temporada si está disponible, de lo contrario null.
     */
    private function getSeasonYear(array $data): ?string
    {
        return $data['MRData']['RaceTable']['season'] ?? null;
    }

    /**
     * Obtiene el número total de carreras de los datos proporcionados.
     *
     * Este método verifica si la clave 'total' existe dentro del array 'MRData' y si es un valor numérico.
     * Si ambas condiciones se cumplen, devuelve el número total de carreras como un entero.
     * De lo contrario, devuelve 0.
     *
     * @param array $data El array de datos que contiene información de carreras.
     * @return int El número total de carreras, o 0 si la clave 'total' no está presente o no es numérica.
     */
    private function getTotalRaces(array $data): int
    {
        return isset($data['MRData']['total']) && is_numeric($data['MRData']['total'])
            ? (int)$data['MRData']['total']
            : 0;
    }

    /**
     * Obtiene el valor del límite de los datos proporcionados.
     *
     * Este método accede a la clave 'MRData' dentro del array dado y devuelve el valor de 'limit'.
     * Si la clave 'limit' no está presente, el valor predeterminado es '30'.
     *
     * @param array $data El array de datos que contiene la clave 'MRData'.
     * @return string El valor del límite del array de datos, o '30' si no está presente.
     */
    private function getLimit(array $data): string
    {
        return $data['MRData']['limit'] ?? '30';
    }

    /**
     * Obtiene el valor del offset de los datos proporcionados.
     *
     * Este método accede a la clave 'MRData' dentro del array dado y devuelve
     * el valor de 'offset' si existe. Si la clave 'offset' no está presente, el
     * valor predeterminado es '0'.
     *
     * @param array $data El array de datos que contiene la clave 'MRData'.
     * @return string El valor del offset o '0' si la clave no está presente.
     */
    private function getOffset(array $data): string
    {
        return $data['MRData']['offset'] ?? '0';
    }

    /**
     * Mapea un array de carreras usando el método mapRace y filtra cualquier valor nulo.
     *
     * Este método toma un array de carreras, aplica el método mapRace a cada carrera,
     * filtra cualquier valor nulo y devuelve el array resultante con claves reindexadas.
     *
     * @param array $races Un array de carreras a mapear.
     * @return array El array de carreras mapeadas y filtradas.
     */
    private function mapRaces(array $races): array
    {
        return array_values(array_filter(array_map([$this, 'mapRace'], $races)));
    }

    /**
     * Mapea un array de carrera a un array de carrera estructurado.
     *
     * Esta función toma un array que representa una carrera y lo mapea a un array estructurado
     * con claves específicas. Si el array de carrera no es válido, devuelve null.
     *
     * @param array $race El array de carrera a mapear.
     * @return array|null El array de carrera estructurado o null si la carrera no es válida.
     */
    private function mapRace(array $race): ?array
    {
        if (!$this->isValidRace($race)) {
            return null;
        }

        return [
            'season' => $race['season'],
            'round' => $race['round'],
            'url' => $race['url'],
            'raceName' => $race['raceName'],
            'Circuit' => $this->mapCircuit($race['Circuit']),
            'date' => $race['date'] ?? null,
            'time' => $race['time'] ?? null,
            'FirstPractice' => $this->formatPractice($race['FirstPractice'] ?? []),
            'SecondPractice' => $this->formatPractice($race['SecondPractice'] ?? []),
            'ThirdPractice' => $this->formatPractice($race['ThirdPractice'] ?? []),
            'Qualifying' => $this->formatPractice($race['Qualifying'] ?? []),
        ];
    }

    /**
     * Valida si el array de carrera dado contiene todas las claves requeridas.
     *
     * @param array $race El array de carrera a validar.
     * @return bool Devuelve true si el array de carrera contiene las claves 'season', 'round', 'url', 'raceName' y 'Circuit'; de lo contrario, false.
     */
    private function isValidRace(array $race): bool
    {
        return isset($race['season'], $race['round'], $race['url'], $race['raceName'], $race['Circuit']) &&
            !empty($race['Circuit']);
    }


    /**
     * Mapea el array de circuito dado a un array estructurado.
     *
     * @param array $circuit Los datos del circuito a mapear.
     * @return array Los datos del circuito mapeados con las claves:
     *               - 'circuitId': El ID del circuito (o null si no está proporcionado).
     *               - 'url': La URL del circuito (o null si no está proporcionado).
     *               - 'circuitName': El nombre del circuito (o null si no está proporcionado).
     *               - 'Location': Los datos de ubicación mapeados (o un array vacío si no está proporcionado).
     */
    private function mapCircuit(array $circuit): array
    {
        return [
            'circuitId' => $circuit['circuitId'] ?? null,
            'url' => $circuit['url'] ?? null,
            'circuitName' => $circuit['circuitName'] ?? null,
            'Location' => $this->mapLocation($circuit['Location'] ?? []),
        ];
    }

    /**
     * Mapea los datos de ubicación del array dado a un formato estandarizado.
     *
     * @param array $location Los datos de ubicación a mapear.
     *
     * @return array Los datos de ubicación mapeados con las claves 'lat', 'long', 'locality' y 'country'.
     *               Si una clave no está presente en el array de entrada, su valor será null.
     */
    private function mapLocation(array $location): array
    {
        return [
            'lat' => $location['lat'] ?? null,
            'long' => $location['long'] ?? null,
            'locality' => $location['locality'] ?? null,
            'country' => $location['country'] ?? null,
        ];
    }

    /**
     * Formatea los datos de la sesión de práctica.
     *
     * Este método toma un array que contiene detalles de la sesión de práctica y devuelve
     * un array formateado con las claves 'date' y 'time' si ambas están presentes. Si el
     * array de entrada está vacío o no contiene las claves 'date' y 'time', devuelve null.
     *
     * @param array $practice Los datos de la sesión de práctica.
     *
     * @return array|null Los datos de la sesión de práctica formateados o null si la entrada es inválida.
     */
    private function formatPractice(array $practice): ?array
    {
        if (empty($practice) || !isset($practice['date'], $practice['time'])) {
            return null;
        }

        return [
            'date' => $practice['date'],
            'time' => $practice['time'],
        ];
    }

    /**
     * Genera un array de respuesta de error con un mensaje dado.
     *
     * @param string $message El mensaje de error a incluir en la respuesta.
     * @return array Un array asociativo que contiene el mensaje de error.
     */
    private function errorResponse(string $message): array
    {
        return ['error' => $message];
    }

    /**
     * Construye el array de respuesta para la API del calendario de carreras.
     *
     * @param string $series El nombre de la serie.
     * @param string $limit El límite de carreras a devolver.
     * @param string $offset El offset para las carreras.
     * @param int $totalRaces El número total de carreras.
     * @param string $year El año de la temporada.
     * @param array $races El array de carreras.
     * @return array El array de respuesta estructurado.
     */
    private function buildResponse(string $series, string $limit, string $offset, int $totalRaces, string $year, array $races): array
    {
        return [
            'ApiColapinto' => [
                'series' => $series,
                'limit' => $limit,
                'offset' => $offset,
                'total' => (string)$totalRaces,
                'RaceTable' => [
                    'season' => $year,
                    'Races' => $races,
                ],
            ],
        ];
    }
}
