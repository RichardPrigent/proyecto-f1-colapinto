<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Exception;
use GuzzleHttp\Exception\RequestException;

class RaceScheduleService
{
    // URL de la API
    private string $apiUrl;

    // Constructor de la clase
    public function __construct()
    {
        // Asigna la URL de la API desde el archivo de configuración .env
        $this->apiUrl = env('ERGAST_API_URL');
    }

    // Método para obtener el calendario de carreras
    public function getRaceSchedule(): array
    {
        try {
            // Llamada al método que realiza la petición HTTP
            $response = $this->fetchRaceSchedule();

            // Verifica si la respuesta fue exitosa
            if ($response->successful()) {
                // Retorna la respuesta en formato JSON
                return $response->json();
            }

            // Mensaje de error si no se pudo obtener el calendario
            $error = 'No se pudo obtener el calendario de carreras';
        } catch (RequestException $e) {
            // Manejo de excepción en caso de error en la petición HTTP
            $error = 'Error en la petición al servicio externo: ' . $e->getMessage();
        } catch (Exception $e) {
            // Manejo de excepción en caso de error inesperado
            $error = 'Error inesperado: ' . $e->getMessage();
        }

        // Retorna un array con el mensaje de error
        return ['error' => $error];
    }

    // Método privado para realizar la petición HTTP
    private function fetchRaceSchedule()
    {
        // Realiza una petición GET a la URL de la API
        return Http::get($this->apiUrl);
    }
}
