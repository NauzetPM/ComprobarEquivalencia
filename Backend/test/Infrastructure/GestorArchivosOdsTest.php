<?php

namespace ComprobadorEquivalenciasTest\Infrastructure;

use PHPUnit\Framework\TestCase;
use ComprobadorEquivalencias\Infrastructure\GestorArchivosOds;

class GestorArchivosOdsTest extends TestCase
{
    private $filePath;
    private $nombreArchivo;

    /**
     * @test
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->filePath = __DIR__ . "/";
        echo $this->filePath;
        $this->nombreArchivo = 'archivo_prueba.ods';
    }

    /**
     * @test
     * @return void
     */
    public function testCrearArchivo(): void
    {
        $datos = [
            'estadisticas' => [
                'total' => 100,
                'activaTotal' => 80,
                'noActivaTotal' => 20,
                'noDescargados' => 10,
                'mapeados' => 50,
                'activaMapeado' => 40,
                'noActivaMapeado' => 10,
                'mapeadosBlock' => 30,
                'activaBlock' => 20,
                'noActivaBlock' => 10,
                'pendientes' => 20,
                'activaPendiente' => 20,
                'noActivaPendiente' => 0,
            ],
            'datos' => [
                [
                    'Codigo' => '001',
                    'Nombre' => 'Producto 1',
                    'Estado' => 'Pendiente',
                    'Activa' => 'Sí',
                ],
            ],
        ];

        $gestorArchivosOds = new GestorArchivosOds($this->filePath, $this->nombreArchivo);

        $gestorArchivosOds->crearArchivo($datos);

        $this->assertFileExists($this->filePath . $this->nombreArchivo);
    }

    /**
     * @test
     * @return void
     */
    protected function tearDown(): void
    {
        parent::tearDown();
        if (file_exists($this->filePath . $this->nombreArchivo)) {
            unlink($this->filePath . $this->nombreArchivo);
        }
    }
}
