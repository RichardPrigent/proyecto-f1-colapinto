<?php

namespace Tests\Unit;

use App\Adapters\HttpClientAdapter;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response as GuzzleResponse;
use Illuminate\Http\Client\Response as HttpClientResponse;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class HttpClientAdapterTest extends TestCase
{
    protected HttpClientAdapter $httpClient;
    private const TEST_URL = 'https://api.example.com/data'; // Definición de la constante

    protected function setUp(): void
    {
        parent::setUp();
        $this->httpClient = new HttpClientAdapter(); // Inicializa el adaptador de cliente HTTP
    }

    public function testValidUrlReturnsResponse()
    {
        Http::shouldReceive('retry')
            ->once()
            ->andReturnSelf(); // Simula el método retry de la fachada Http
        Http::shouldReceive('get')
            ->once()
            ->with(self::TEST_URL)
            ->andReturn(new HttpClientResponse(
                new GuzzleResponse(JsonResponse::HTTP_OK, [], json_encode(['message' => 'Success'])) // Respuesta simulada con estado 200 y mensaje de éxito
            ));

        $response = $this->httpClient->get(self::TEST_URL); // Realiza la solicitud GET
        $this->assertEquals(JsonResponse::HTTP_OK, $response->status()); // Verifica que el estado de la respuesta sea 200
        $this->assertEquals('Success', json_decode($response->body())->message); // Verifica que el mensaje de la respuesta sea "Success"
    }

    public function testInvalidUrlThrowsValidationException()
    {
        $this->expectException(ValidationException::class); // Espera una excepción de validación

        $this->httpClient->get('invalid-url'); // Realiza la solicitud GET con una URL inválida
    }

    public function testRequestExceptionThrowsHttpResponseException()
    {
        Http::shouldReceive('retry')
            ->once()
            ->andReturnSelf(); // Simula el método retry de la fachada Http
        Http::shouldReceive('get')
            ->once()
            ->with(self::TEST_URL)
            ->andThrow(new RequestException("Error en la conexión", new Request('GET', self::TEST_URL))); // Simula una excepción de solicitud

        $this->expectException(HttpResponseException::class); // Espera una excepción de respuesta HTTP
        $this->httpClient->get(self::TEST_URL); // Realiza la solicitud GET
    }

    public function testUnsuccessfulResponseThrowsHttpResponseException()
    {
        Http::shouldReceive('retry')
            ->once()
            ->andReturnSelf(); // Simula el método retry de la fachada Http
        Http::shouldReceive('get')
            ->once()
            ->with(self::TEST_URL)
            ->andReturn(new HttpClientResponse(
                new GuzzleResponse(JsonResponse::HTTP_NOT_FOUND, [], json_encode(['error' => 'Not Found'])) // Respuesta simulada con estado 404 y mensaje de error
            ));

        $this->expectException(HttpResponseException::class); // Espera una excepción de respuesta HTTP
        $this->httpClient->get(self::TEST_URL); // Realiza la solicitud GET
    }
}
