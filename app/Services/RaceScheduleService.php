<?php

namespace App\Services;

use App\Contracts\HttpClientInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Contracts\Routing\ResponseFactory;

class RaceScheduleService
{
    private string $apiUrl;
    private HttpClientInterface $httpClient;
    private ResponseFactory $responseFactory;

    public function __construct(HttpClientInterface $httpClient, ResponseFactory $responseFactory)
    {
        $this->apiUrl = env('ERGAST_API_URL');
        $this->httpClient = $httpClient;
        $this->responseFactory = $responseFactory; // Inyecta la fábrica de respuestas
    }

    public function getRaceSchedule(): JsonResponse
    {
        try {
            $response = $this->httpClient->get(env('ERGAST_API_URL'));

            // Verifica que la respuesta no sea nula
            if ($response && $response->getStatusCode() === 200) {
                return $response; // Asegúrate de que esto también sea un JsonResponse
            }

            // Manejar el caso de respuesta nula
            $error = ['error' => 'Error inesperado: HTTP request returned null response.'];
            return $this->responseFactory->json($error, 500);
        } catch (\Exception $e) {
            $error = ['error' => 'Excepción capturada: ' . $e->getMessage()];
            return $this->responseFactory->json($error, 500); // Asegúrate de que aquí también se devuelve un JsonResponse
        }
    }
}
