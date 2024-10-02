<?php

namespace App\Adapters;

use App\Contracts\HttpClientInterface;
use Illuminate\Support\Facades\Http;
use GuzzleHttp\Exception\RequestException;
use Exception;

class HttpClientAdapter implements HttpClientInterface
{
    public function get(string $url): array
    {
        $result = ['error' => 'No se pudo obtener la respuesta'];

        try {
            $response = Http::get($url);

            if ($response->successful()) {
                $result = $response->json();
            }
        } catch (RequestException $e) {
            $result = ['error' => 'Error en la petición HTTP: ' . $e->getMessage()];
        } catch (Exception $e) {
            $result = ['error' => 'Error inesperado: ' . $e->getMessage()];
        }

        return $result;
    }
}
