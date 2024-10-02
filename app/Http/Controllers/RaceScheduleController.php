<?php

namespace App\Http\Controllers;

use App\Services\RaceScheduleService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Exception;

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
        try {
            // Llama al servicio para obtener el calendario de carreras
            $raceSchedule = $this->raceScheduleService->getRaceSchedule();

            // Verifica si hay un error en la respuesta
            if (isset($raceSchedule['error'])) {
                return response()->json(['error' => $raceSchedule['error']], Response::HTTP_INTERNAL_SERVER_ERROR);
            }

            // Retorna la respuesta en formato JSON al cliente
            return response()->json($raceSchedule, Response::HTTP_OK);
        } catch (Exception $e) {
            // Manejo de excepciones generales
            return response()->json(['error' => 'Ocurrió un error al procesar la solicitud.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
