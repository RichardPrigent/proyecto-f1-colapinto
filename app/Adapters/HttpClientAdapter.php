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
    /**
     * Envía una solicitud GET a la URL especificada.
     *
     * Este método valida la URL proporcionada y registra el proceso de solicitud.
     * Reintenta la solicitud hasta 3 veces con un retraso de 100ms entre intentos.
     * Si la solicitud es exitosa, devuelve la respuesta HTTP.
     * Si la solicitud falla, registra el error y lanza una excepción apropiada.
     *
     * @param string $url La URL a la que se enviará la solicitud GET.
     * @throws \Illuminate\Validation\ValidationException Si la URL proporcionada no es válida.
     * @throws \Illuminate\Http\Exceptions\HttpResponseException Si la solicitud HTTP falla o la respuesta no es exitosa.
     * @return \Illuminate\Http\Client\Response La respuesta HTTP si la solicitud es exitosa.
     */
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
