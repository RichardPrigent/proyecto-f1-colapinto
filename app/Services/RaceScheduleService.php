<?php

namespace App\Services;

use App\Contracts\HttpClientInterface;

class RaceScheduleService
{
    private string $apiUrl;
    private HttpClientInterface $httpClient;

    // Constructor de la clase
    public function __construct(HttpClientInterface $httpClient)
    {
        // Asigna la URL de la API desde el archivo de configuración .env
        $this->apiUrl = env('ERGAST_API_URL');
        $this->httpClient = $httpClient; // Inyección del adaptador
    }

    // Método para obtener el calendario de carreras
    public function getRaceSchedule(): array
    {
        // Llamada al método del adaptador que realiza la petición HTTP
        return $this->httpClient->get($this->apiUrl);
    }
}
