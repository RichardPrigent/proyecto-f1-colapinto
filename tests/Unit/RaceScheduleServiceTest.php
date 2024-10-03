<?php

use Mockery;
use PHPUnit\Framework\TestCase;
use App\Contracts\HttpClientInterface;
use App\Services\RaceScheduleService;
use Illuminate\Http\JsonResponse;
use Illuminate\Contracts\Routing\ResponseFactory;

class RaceScheduleServiceTest extends TestCase
{
    // Método que se ejecuta después de cada test
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    // Test para verificar que getRaceSchedule devuelve una respuesta JSON en caso de éxito
    public function testGetRaceScheduleReturnsJsonResponseOnSuccess()
    {
        /** @var HttpClientInterface|Mockery\MockInterface $mockHttpClient */
        $mockHttpClient = Mockery::mock(HttpClientInterface::class);
        /** @var ResponseFactory|Mockery\MockInterface $mockResponseFactory */
        $mockResponseFactory = Mockery::mock(ResponseFactory::class);
        $mockJsonResponse = Mockery::mock(JsonResponse::class);

        // Configuramos el mock para que devuelva una respuesta JSON al hacer una petición GET
        $mockHttpClient->shouldReceive('get')
            ->once()
            ->with(env('ERGAST_API_URL'))
            ->andReturn($mockJsonResponse);

        // Configuramos el mock para que la respuesta tenga un código de estado 200
        $mockJsonResponse->shouldReceive('getStatusCode')
            ->once()
            ->andReturn(200);

        // Creamos una instancia del servicio con los mocks
        $service = new RaceScheduleService($mockHttpClient, $mockResponseFactory);
        $response = $service->getRaceSchedule();

        // Verificamos que la respuesta sea una instancia de JsonResponse
        $this->assertInstanceOf(JsonResponse::class, $response);
        // Verificamos que la respuesta sea igual a la respuesta mockeada
        $this->assertEquals($mockJsonResponse, $response);
    }

    // Test para verificar que getRaceSchedule devuelve una respuesta de error JSON en caso de respuesta nula
    public function testGetRaceScheduleReturnsErrorJsonResponseOnNullResponse()
    {
        /** @var HttpClientInterface|Mockery\MockInterface $mockHttpClient */
        $mockHttpClient = Mockery::mock(HttpClientInterface::class);
        /** @var ResponseFactory|Mockery\MockInterface $mockResponseFactory */
        $mockResponseFactory = Mockery::mock(ResponseFactory::class);
        $mockErrorResponse = Mockery::mock(JsonResponse::class);

        // Configuramos el mock para que devuelva null al hacer una petición GET
        $mockHttpClient->shouldReceive('get')
            ->once()
            ->with(env('ERGAST_API_URL'))
            ->andReturn(null);

        // Configuramos el mock para que devuelva una respuesta de error JSON
        $mockResponseFactory->shouldReceive('json')
            ->once()
            ->with(['error' => 'Error inesperado: HTTP request returned null response.'], 500)
            ->andReturn($mockErrorResponse);

        // Creamos una instancia del servicio con los mocks
        $service = new RaceScheduleService($mockHttpClient, $mockResponseFactory);
        $response = $service->getRaceSchedule();

        // Verificamos que la respuesta sea una instancia de JsonResponse
        $this->assertInstanceOf(JsonResponse::class, $response);
        // Verificamos que la respuesta sea igual a la respuesta de error mockeada
        $this->assertEquals($mockErrorResponse, $response);
    }
}
