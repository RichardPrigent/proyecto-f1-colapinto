<?php

namespace App\Adapters;

use App\Contracts\HttpClientInterface;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class HttpClientAdapter implements HttpClientInterface
{
    public function get(string $url)
    {
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            Log::warning('URL no válida proporcionada: ' . $url);
            throw ValidationException::withMessages(['url' => 'URL no válida: ' . $url]);
        }

        try {
            Log::info('Realizando solicitud GET a la URL: ' . $url);
            $httpResponse = Http::retry(3, 100)->get($url);

            if ($httpResponse->successful()) {
                return $httpResponse;
            } else {
                Log::warning('Respuesta no exitosa del servidor: ' . $httpResponse->body());
                throw new HttpResponseException(response('Respuesta no exitosa: ' . $httpResponse->status(), $httpResponse->status()));
            }
        } catch (RequestException $e) {
            Log::error('Error en la petición HTTP: ' . $e->getMessage());
            throw new HttpResponseException(response('Error en la petición HTTP: ' . $e->getMessage(), JsonResponse::HTTP_INTERNAL_SERVER_ERROR));
        }
    }
}
