<?php

declare(strict_types=1);

namespace ComprobadorEquivalenciasTest\Infrastructure;

use PHPUnit\Framework\TestCase;
use ComprobadorEquivalencias\Infrastructure\GestorSelectorMysql;
use ComprobadorEquivalencias\Infrastructure\Database;
use Dotenv\Dotenv;

class GestorSelectorMysqlTest extends TestCase
{
    protected $gestorSelector;


    /**
     *
     * @return void
     */
    protected function setUp(): void
    {
        $dotenv = Dotenv::createImmutable(__DIR__ . '/../../');
        $dotenv->load();
        $db = new Database($_ENV['BBDD_HOST'], $_ENV['BBDD_USER'], $_ENV['BBDD_PASS'], $_ENV['BBDD_DATABASE_CONFIG'], $_ENV['BBDD_PORT']);
        $this->gestorSelector = new GestorSelectorMysql($db);
    }

    /**
     * @test
     * @return void
     */
    public function testObtenerCorrespondencias(): void
    {
        $result = $this->gestorSelector->obtenerCorrespondencias('Expedia');
        $this->assertIsArray($result);

        $this->assertArrayHasKey('conexion', $result);
        $this->assertArrayHasKey('tabla1', $result);
        $this->assertArrayHasKey('tabla2', $result);

        $this->assertNotEmpty($result['conexion']);
        $this->assertNotEmpty($result['tabla1']);
        $this->assertNotEmpty($result['tabla2']);
    }

    /**
     * @test
     * @return void
     */
    public function testObtenerCorrespondenciasFailure(): void
    {
        $result = $this->gestorSelector->obtenerCorrespondencias('Inventado');
        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }
}
