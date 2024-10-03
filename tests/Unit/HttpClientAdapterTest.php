<?php

namespace Tests\Unit;

use App\Adapters\HttpClientAdapter;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;
use Illuminate\Http\JsonResponse;

class HttpClientAdapterTest extends TestCase
{
    private const EXAMPLE_URL = 'https://example.com';

    public function testGetReturnsValidResponse()
    {
        // Falsificar una respuesta HTTP exitosa
        Http::fake([
            self::EXAMPLE_URL => Http::response(['data' => 'success'], 200)
        ]);

        $client = new HttpClientAdapter();
        $response = $client->get(self::EXAMPLE_URL);

        // Asegurarse de que la respuesta es un JsonResponse y tiene el contenido esperado
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(200, $response->status());
        $this->assertEquals(['data' => 'success'], $response->getData(true));
    }

    public function testGetHandlesInvalidUrl()
    {
        $client = new HttpClientAdapter();
        $response = $client->get('invalid-url');

        // Asegurarse de que maneja URLs no válidas
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(400, $response->status());
        $this->assertEquals(['error' => 'URL no válida'], $response->getData(true));
    }

    public function testGetHandlesHttpRequestError()
    {
        // Falsificar una respuesta con error
        Http::fake([
            self::EXAMPLE_URL => Http::response(null, 500)
        ]);

        $client = new HttpClientAdapter();
        $response = $client->get(self::EXAMPLE_URL);

        // Verificar que se maneja correctamente la respuesta con error
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(500, $response->status());
        $this->assertEquals(['error' => 'Error inesperado: HTTP request returned status code 500'], $response->getData(true));
    }
}
