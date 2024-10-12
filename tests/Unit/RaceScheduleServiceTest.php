<?php

namespace Tests\Unit;

use App\Services\RaceScheduleService;
use App\Contracts\HttpClientInterface;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\Exceptions\HttpResponseException;
use Tests\TestCase;
use Mockery;

class RaceScheduleServiceTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        putenv('ERGAST_API_URL=https://ergast.com/api/f1/current.json'); // Establecer la URL de la API
        Cache::flush(); // Limpiar la caché antes de cada prueba
    }

    protected function tearDown(): void
    {
        Mockery::close(); // Cerrar Mockery después de cada prueba
        parent::tearDown();
    }

    public function testGetRaceScheduleReturnsDataFromCache()
    {
        $httpClientMock = Mockery::mock(HttpClientInterface::class);
        $cacheData = ['race' => 'data'];

        Cache::shouldReceive('remember')
            ->once()
            ->with('race_schedule', 1800, Mockery::type('Closure'))
            ->andReturn($cacheData); // Simular que los datos vienen de la caché

        $service = new RaceScheduleService($httpClientMock instanceof HttpClientInterface ? $httpClientMock : null);
        $result = $service->getRaceSchedule();

        $this->assertEquals($cacheData, $result); // Verificar que los datos de la caché son correctos
    }

    public function testGetRaceScheduleFetchesDataFromApi()
    {
        $httpClientMock = Mockery::mock(HttpClientInterface::class);
        $apiResponse = Mockery::mock();
        $apiData = ['race' => 'data'];

        $apiResponse->shouldReceive('successful')
            ->once()
            ->andReturn(true); // Simular que la respuesta de la API es exitosa
        $apiResponse->shouldReceive('json')
            ->once()
            ->andReturn($apiData); // Simular los datos de la API

        // Obtener la URL de la variable de entorno
        $apiUrl = getenv('ERGAST_API_URL');

        $httpClientMock->shouldReceive('get')
            ->once()
            ->with($apiUrl) // Usar la URL de la variable de entorno
            ->andReturn($apiResponse); // Simular la llamada a la API

        Cache::shouldReceive('remember')
            ->once()
            ->with('race_schedule', 1800, Mockery::type('Closure'))
            ->andReturnUsing(function ($key, $ttl, $callback) {
                return $callback(); // Ejecutar el callback para obtener los datos de la API
            });

        $service = new RaceScheduleService($httpClientMock instanceof HttpClientInterface ? $httpClientMock : null);
        $result = $service->getRaceSchedule();

        $this->assertEquals($apiData, $result); // Verificar que los datos de la API son correctos
    }

    public function testGetRaceScheduleThrowsExceptionOnApiError()
    {
        $this->expectException(HttpResponseException::class); // Esperar una excepción si la API falla

        $httpClientMock = Mockery::mock(HttpClientInterface::class);
        $apiResponse = Mockery::mock();

        $apiResponse->shouldReceive('successful')
            ->once()
            ->andReturn(false); // Simular que la respuesta de la API no es exitosa
        $apiResponse->shouldReceive('body')
            ->once()
            ->andReturn('Error message'); // Simular el mensaje de error de la API

        // Obtener la URL de la variable de entorno
        $apiUrl = getenv('ERGAST_API_URL');

        $httpClientMock->shouldReceive('get')
            ->once()
            ->with($apiUrl) // Usar la URL de la variable de entorno
            ->andReturn($apiResponse); // Simular la llamada a la API

        Cache::shouldReceive('remember')
            ->once()
            ->with('race_schedule', 1800, Mockery::type('Closure'))
            ->andReturnUsing(function ($key, $ttl, $callback) {
                return $callback(); // Ejecutar el callback para obtener los datos de la API
            });

        $service = new RaceScheduleService($httpClientMock instanceof HttpClientInterface ? $httpClientMock : null);
        $service->getRaceSchedule(); // Llamar al método que debería lanzar la excepción
    }
}
