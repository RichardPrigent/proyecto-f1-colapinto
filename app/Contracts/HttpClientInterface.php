<?php

namespace App\Contracts;

/**
 * Interfaz HttpClientInterface
 *
 * Esta interfaz define el contrato para un cliente HTTP.
 *
 * @package App\Contracts
 */
interface HttpClientInterface
{
    // Método para realizar una solicitud GET a una URL específica
    public function get(string $url);
}
