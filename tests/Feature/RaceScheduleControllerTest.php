<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Services\RaceScheduleService;
use App\Http\Controllers\RaceScheduleController;
use Illuminate\Http\JsonResponse;
use Mockery;
use Exception;

class RaceScheduleControllerTest extends TestCase
{
    public function testIndexReturnsRaceSchedule()
    {
        /** @var RaceScheduleService|Mockery\MockInterface $raceScheduleServiceMock */
        $raceScheduleServiceMock = Mockery::mock(RaceScheduleService::class);
        $raceScheduleServiceMock->shouldReceive('getRaceSchedule')
            ->once()
            ->andReturn(new JsonResponse(['schedule' => 'race data'], 200));

        // Instantiate the controller with the mocked service
        $controller = new RaceScheduleController($raceScheduleServiceMock);

        // Call the index method
        $response = $controller->index();

        // Assert the response
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(['schedule' => 'race data'], $response->getData(true));
    }

    public function testIndexHandlesException()
    {
        /** @var RaceScheduleService|Mockery\MockInterface $raceScheduleServiceMock */
        $raceScheduleServiceMock = Mockery::mock(RaceScheduleService::class);
        $raceScheduleServiceMock->shouldReceive('getRaceSchedule')
            ->once()
            ->andThrow(new Exception('Some error'));

        // Instantiate the controller with the mocked service
        $controller = new RaceScheduleController($raceScheduleServiceMock);

        // Call the index method
        $response = $controller->index();

        // Assert the response
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(500, $response->getStatusCode());
        $this->assertEquals(['error' => 'Ocurrió un error al procesar la solicitud.'], $response->getData(true));
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
