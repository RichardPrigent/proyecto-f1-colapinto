<?php

namespace App\Http\Controllers;

use App\Services\RaceScheduleService;
use Illuminate\Http\JsonResponse;
use Exception;

/**
 * Clase RaceScheduleController
 *
 * Este controlador maneja las operaciones relacionadas con el calendario de carreras.
 * Extiende la clase base Controller proporcionada por el framework.
 *
 * @package App\Http\Controllers
 */
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
            return $this->raceScheduleService->getRaceSchedule(); // Asumiendo que devuelve un JsonResponse
        } catch (Exception $e) {
            // Manejo de excepciones generales
            return response()->json(['error' => 'Ocurrió un error al procesar la solicitud.'], 500); // Usar el código 500 directamente
        }
    }
}
