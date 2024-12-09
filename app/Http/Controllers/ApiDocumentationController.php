<?php

namespace App\Http\Controllers;

use OpenApi\Annotations as OA;

/**
 * @OA\Info(
 *   title="Franco Colapinto API",
 *   version="1.0.0",
 *   description="API para gestionar información sobre la Fórmula 1, incluyendo cronogramas de carreras, pilotos y equipos, con un enfoque especial en Franco Colapinto."
 * )
 */
class ApiDocumentationController extends Controller
{
    /**
     * @OA\Get(
     *   path="/api/race-schedule",
     *   summary="Obtiene el cronograma de carreras",
     *   tags={"Race Schedule"},
     *   @OA\Response(
     *       response=200,
     *       description="Lista del cronograma de carreras",
     *       @OA\JsonContent(
     *           type="object",
     *           @OA\Property(property="series", type="string", example="f1"),
     *           @OA\Property(property="limit", type="string", example="30"),
     *           @OA\Property(property="offset", type="string", example="0"),
     *           @OA\Property(property="total", type="string", example="24"),
     *           @OA\Property(property="RaceTable", type="object",
     *               @OA\Property(property="season", type="string", example="2024"),
     *               @OA\Property(property="Races", type="array",
     *                   @OA\Items(
     *                       @OA\Property(property="season", type="string", example="2024"),
     *                       @OA\Property(property="round", type="string", example="1"),
     *                       @OA\Property(property="raceName", type="string", example="Bahrain Grand Prix"),
     *                       @OA\Property(property="Circuit", type="object",
     *                           @OA\Property(property="circuitId", type="string", example="bahrain"),
     *                           @OA\Property(property="circuitName", type="string", example="Bahrain International Circuit")
     *                       ),
     *                       @OA\Property(property="date", type="string", example="2024-03-02"),
     *                       @OA\Property(property="time", type="string", example="15:00:00Z")
     *                   )
     *               )
     *           )
     *       )
     *   ),
     *   @OA\Response(
     *       response=400,
     *       description="Ocurrió un error HTTP",
     *       @OA\JsonContent(
     *           @OA\Property(property="error", type="string", example="Ocurrió un error HTTP")
     *       )
     *   ),
     *   @OA\Response(
     *       response=500,
     *       description="Ocurrió un error al procesar la solicitud",
     *       @OA\JsonContent(
     *           @OA\Property(property="error", type="string", example="Ocurrió un error al procesar la solicitud: Detalles del error")
     *       )
     *   )
     * )
     */
    public function raceScheduleDocumentation()
    {
        // Este método no necesita implementación.
    }
}
