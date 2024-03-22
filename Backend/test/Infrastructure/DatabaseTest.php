<?php

declare(strict_types=1);

namespace ComprobadorEquivalenciasTest\Infrastructure;

use PHPUnit\Framework\TestCase;
use ComprobadorEquivalencias\Infrastructure\Database;
use PDO;
use Dotenv\Dotenv;

class DatabaseTest extends TestCase
{
    protected $db;

    /**
     * @test
     * @return void
     */
    protected function setUp(): void
    {
        $dotenv = Dotenv::createImmutable(__DIR__ . '/../../');
        $dotenv->load();
    }

    /**
     * @test
     * @return void
     */
    public function testConnection(): void
    {
        $this->db = new Database($_ENV['BBDD_HOST'], $_ENV['BBDD_USER'], $_ENV['BBDD_PASS'], $_ENV['BBDD_DATABASE_CONFIG'], $_ENV['BBDD_PORT']);
        $this->assertInstanceOf(PDO::class, $this->db->connection);
    }

    /**
     * @test
     * @return void
     */
    public function testInvalidConnection(): void
    {
        $this->expectException(\Exception::class);
        $invalidDb = new Database('invalid_host', 'invalid_user', 'invalid_pass', 'invalid_db', '3306');
    }
}
