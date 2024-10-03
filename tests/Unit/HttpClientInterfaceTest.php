<?php

use PHPUnit\Framework\TestCase;
use App\Contracts\HttpClientInterface;

class HttpClientInterfaceTest extends TestCase
{
    // Test para verificar si el método 'get' existe en la interfaz HttpClientInterface
    public function testGetMethodExists()
    {
        $this->assertTrue(
            method_exists(HttpClientInterface::class, 'get'),
            'Interface HttpClientInterface does not have method get' // La interfaz HttpClientInterface no tiene el método get
        );
    }

    // Test para verificar la firma del método 'get'
    public function testGetMethodSignature()
    {
        $reflection = new ReflectionMethod(HttpClientInterface::class, 'get');
        $parameters = $reflection->getParameters();

        // Verifica que el método 'get' tenga exactamente un parámetro
        $this->assertCount(1, $parameters, 'Method get should have exactly one parameter');
        // Verifica que el nombre del parámetro sea 'url'
        $this->assertEquals('url', $parameters[0]->getName(), 'Parameter name should be "url"');
        // Verifica que el tipo del parámetro sea 'string'
        $this->assertEquals('string', $parameters[0]->getType()->getName(), 'Parameter type should be "string"');
    }
}
