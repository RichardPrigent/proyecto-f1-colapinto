<?php

namespace App\Services;

use App\Contracts\HttpClientInterface;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse; // Importamos JsonResponse
use Symfony\Component\HttpFoundation\Response;

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

    public function __construct(HttpClientInterface $httpClient)
    {
        $this->apiUrl = env('ERGAST_API_URL');
        $this->httpClient = $httpClient;
    }

    /**
     * Obtiene el calendario de carreras.
     *
     * Este método realiza una llamada a la API externa y devuelve el calendario de carreras.
     * Utiliza caché para almacenar los datos durante 30 minutos.
     *
     * @return array Datos del calendario de carreras.
     * @throws HttpResponseException Si la respuesta de la API no es exitosa.
     */
    public function getRaceSchedule(): array
    {
        $cacheKey = 'race_schedule';
        return Cache::remember($cacheKey, 1800, function () {
            $response = $this->httpClient->get($this->apiUrl);

            // Verifica si la respuesta fue exitosa
            if (!$response->successful()) {
                // Lanza una excepción utilizando la clase JsonResponse ya importada
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
