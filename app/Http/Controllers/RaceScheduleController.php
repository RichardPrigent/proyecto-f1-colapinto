<?php

namespace App\Http\Controllers;

use App\Services\RaceScheduleService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class RaceScheduleController extends Controller
{
    private RaceScheduleService $raceScheduleService;

    // Inyección del servicio en el constructor
    public function __construct(RaceScheduleService $raceScheduleService)
    {
        $this->raceScheduleService = $raceScheduleService;
    }

    // Método que responderá a las solicitudes GET en la ruta /race-schedule
    public function index(): JsonResponse
    {
        // Llama al servicio para obtener el calendario de carreras
        $raceSchedule = $this->raceScheduleService->getRaceSchedule();

        if (isset($raceSchedule['error'])) {
            // Retorna un mensaje de error en caso de que no se pueda obtener el calendario
            return response()->json(['error' => $raceSchedule['error']], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        // Retorna la respuesta en formato JSON al cliente
        return response()->json($raceSchedule, Response::HTTP_OK);
    }
}
