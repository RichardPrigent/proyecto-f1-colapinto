<?php

namespace App\Adapters;

use App\Contracts\HttpClientInterface;
use Exception;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\JsonResponse; // Asegúrate de importar la clase JsonResponse

/**
 * Clase HttpClientAdapter
 *
 * Esta clase implementa la interfaz HttpClientInterface y proporciona
 * los métodos necesarios para manejar operaciones del cliente HTTP.
 *
 * @package App\Adapters
 */
class HttpClientAdapter implements HttpClientInterface
{
    /**
     * Realiza una solicitud GET a una URL y devuelve la respuesta como un objeto Response.
     *
     * @param string $url La URL a la que se hará la solicitud GET.
     * @return JsonResponse La respuesta JSON de la solicitud HTTP.
     */
    public function get(string $url): JsonResponse
    {

        // Validar la URL antes de realizar la solicitud
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            return response()->json(['error' => 'URL no válida'], JsonResponse::HTTP_BAD_REQUEST);
        }

        try {
            // Realiza la solicitud GET a la URL proporcionada con reintentos
            $httpResponse = Http::retry(3, 100)->get($url);

            // Verifica si la respuesta fue exitosa
            if ($httpResponse->successful()) {
                // Retorna el contenido JSON como objeto JsonResponse
                return response()->json($httpResponse->json(), JsonResponse::HTTP_OK);
            } else {
                // Retorna un mensaje de error con el código de estado correspondiente
                return response()->json(['error' => 'Respuesta no exitosa: ' . $httpResponse->status()], $httpResponse->status());
            }
        } catch (RequestException $e) {
            // Registrar y devolver errores específicos de la petición HTTP
            Log::error('Error en la petición HTTP: ' . $e->getMessage());
            return response()->json(['error' => 'Error en la petición HTTP: ' . $e->getMessage()], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        } catch (Exception $e) {
            // Registrar y devolver cualquier otro tipo de error inesperado
            Log::error('Error inesperado: ' . $e->getMessage());
            return response()->json(['error' => 'Error inesperado: ' . $e->getMessage()], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
