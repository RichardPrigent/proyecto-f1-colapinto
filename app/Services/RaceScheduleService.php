<?php

namespace App\Services;

use App\Contracts\HttpClientInterface;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use App\Mappers\RaceScheduleMapper;

/**
 * Clase RaceScheduleService
 *
 * Este servicio es responsable de obtener el calendario de carreras desde una API externa.
 * Utiliza caché para almacenar los datos del calendario de carreras durante una duración especificada.
 *
 * @package App\Services
 * @see https://ergast.com/mrd/ Documentación de la API Ergast
 */
class RaceScheduleService
{
    private string $apiUrl;
    private HttpClientInterface $httpClient;
    private RaceScheduleMapper $raceScheduleMapper; // Agregamos el mapper

    public function __construct(HttpClientInterface $httpClient, RaceScheduleMapper $raceScheduleMapper)
    {
        $this->apiUrl = env('ERGAST_API_URL');
        $this->httpClient = $httpClient;
        $this->raceScheduleMapper = $raceScheduleMapper;
    }

    /**
     * Obtiene el calendario de carreras y lo formatea con el mapper.
     *
     * Este método realiza una llamada a la API externa y devuelve el calendario de carreras formateado.
     * Utiliza caché para almacenar los datos durante 30 minutos.
     *
     * @return array Datos del calendario de carreras formateados.
     * @throws HttpResponseException Si la respuesta de la API no es exitosa.
     */
    public function getRaceSchedule(): array
    {
        $rawData = $this->getRawRaceSchedule(); // Obtiene los datos crudos
        return $this->raceScheduleMapper->map($rawData); // Aplica el mapper para formatear los datos
    }

    /**
     * Obtiene los datos crudos del calendario de carreras desde la API externa.
     *
     * @return array Datos crudos del calendario de carreras.
     * @throws HttpResponseException Si la respuesta de la API no es exitosa.
     */
    public function getRawRaceSchedule(): array
    {
        $cacheKey = 'race_schedule';
        return Cache::remember($cacheKey, 1800, function () {
            $response = $this->httpClient->get($this->apiUrl);

            // Verifica si la respuesta fue exitosa
            if (!$response->successful()) {
                throw new HttpResponseException(
                    new JsonResponse(
                        ['error' => 'Error al obtener datos de la API: ' . $response->body()],
                        Response::HTTP_BAD_REQUEST
                    )
                );
            }

            return $response->json();
        });
    }
}
