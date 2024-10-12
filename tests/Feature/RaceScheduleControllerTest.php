<?php

namespace Tests\Feature;

use App\Http\Controllers\RaceScheduleController;
use App\Services\RaceScheduleService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Exceptions\HttpResponseException;
use Tests\TestCase;
use Mockery;

class RaceScheduleControllerTest extends TestCase
{
    // Test para verificar que el método index devuelve el cronograma de carreras correctamente
    public function testIndexReturnsRaceScheduleSuccessfully()
    {
        // Crear un mock del servicio RaceScheduleService
        $mockRaceScheduleService = Mockery::mock(RaceScheduleService::class);
        $mockRaceScheduleService->shouldReceive('getRaceSchedule')
            ->once()
            ->andReturn(['race1', 'race2']);

        // Crear una instancia del controlador con el servicio mockeado
        $controller = new RaceScheduleController($mockRaceScheduleService instanceof RaceScheduleService ? $mockRaceScheduleService : null);

        // Llamar al método index del controlador
        $response = $controller->index();

        // Verificar que la respuesta sea una instancia de JsonResponse
        $this->assertInstanceOf(JsonResponse::class, $response);
        // Verificar que el código de estado sea HTTP_OK
        $this->assertEquals(JsonResponse::HTTP_OK, $response->getStatusCode());
        // Verificar que los datos de la respuesta sean los esperados
        $this->assertEquals(['race1', 'race2'], $response->getData(true));
    }

    // Test para verificar que el método index maneja correctamente una HttpResponseException
    public function testIndexHandlesHttpResponseException()
    {
        // Crear un mock del servicio RaceScheduleService
        $mockService = Mockery::mock(RaceScheduleService::class);
        $mockService->shouldReceive('getRaceSchedule')
            ->once()
            ->andThrow(new HttpResponseException(response()->json(['error' => 'Ocurrió un error HTTP'], JsonResponse::HTTP_BAD_REQUEST)));

        // Crear una instancia del controlador con el servicio mockeado
        $controller = new RaceScheduleController($mockService instanceof RaceScheduleService ? $mockService : null);

        // Llamar al método index del controlador
        $response = $controller->index();

        // Verificar que la respuesta sea una instancia de JsonResponse
        $this->assertInstanceOf(JsonResponse::class, $response);
        // Verificar que el código de estado sea HTTP_BAD_REQUEST
        $this->assertEquals(JsonResponse::HTTP_BAD_REQUEST, $response->getStatusCode());
        // Verificar que los datos de la respuesta sean los esperados
        $this->assertEquals(['error' => 'Ocurrió un error HTTP'], $response->getData(true));
    }

    // Test para verificar que el método index maneja correctamente una excepción general
    public function testIndexHandlesGeneralException()
    {
        // Crear un mock del servicio RaceScheduleService
        $mockRaceScheduleService = Mockery::mock(RaceScheduleService::class);
        $mockRaceScheduleService->shouldReceive('getRaceSchedule')
            ->once()
            ->andThrow(new \Exception('General error'));

        // Crear una instancia del controlador con el servicio mockeado
        $controller = new RaceScheduleController($mockRaceScheduleService instanceof RaceScheduleService ? $mockRaceScheduleService : null);

        // Llamar al método index del controlador
        $response = $controller->index();

        // Verificar que la respuesta sea una instancia de JsonResponse
        $this->assertInstanceOf(JsonResponse::class, $response);
        // Verificar que el código de estado sea HTTP_INTERNAL_SERVER_ERROR
        $this->assertEquals(JsonResponse::HTTP_INTERNAL_SERVER_ERROR, $response->getStatusCode());
        // Verificar que los datos de la respuesta sean los esperados
        $this->assertEquals(['error' => 'Ocurrió un error al procesar la solicitud: General error'], $response->getData(true));
    }

    // Método para cerrar Mockery después de cada test
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
