<?php

namespace App\Http\Controllers;

use App\Services\RaceScheduleService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Exceptions\HttpResponseException;
use Exception;

/**
 * RaceScheduleController
 *
 * Este controlador maneja las solicitudes relacionadas con el cronograma de carreras.
 *
 * @package App\Http\Controllers
 */
class RaceScheduleController extends Controller
{
    private RaceScheduleService $raceScheduleService;

    public function __construct(RaceScheduleService $raceScheduleService)
    {
        $this->raceScheduleService = $raceScheduleService;
    }

    /**
     * Muestra una lista del cronograma de carreras.
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @throws \Illuminate\Http\Exceptions\HttpResponseException Si hay un error de respuesta HTTP.
     * @throws \Exception Si hay un error general.
     */
    public function index(): JsonResponse
    {
        try {
            $data = $this->raceScheduleService->getRaceSchedule();
            return response()->json($data, JsonResponse::HTTP_OK);
        } catch (HttpResponseException $e) {
            // Manejo de excepciones específicas de respuesta HTTP
            return response()->json(['error' => 'Ocurrió un error HTTP'], JsonResponse::HTTP_BAD_REQUEST);
        } catch (Exception $e) {
            // Manejo de otras excepciones
            return response()->json(['error' => 'Ocurrió un error al procesar la solicitud: ' . $e->getMessage()], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
