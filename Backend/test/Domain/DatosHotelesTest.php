<?php

declare(strict_types=1);

namespace ComprobadorEquivalenciasTest\Domain;

require(__DIR__ . '/../../vendor/autoload.php');

use ComprobadorEquivalencias\Domain\DatosHoteles;
use PHPUnit\Framework\TestCase;

class DatosHotelesTest extends TestCase
{
    /**
     * @test
     * @return void
     */
    public function testasArray(): void
    {
        $hotel = new DatosHoteles("98901", "Nombre Hotel", "Estado Del Hotel", true);
        $array = $hotel->asArray();
        $this->assertSame('98901', $array["Codigo"]);
        $this->assertSame('Nombre Hotel', $array["Nombre"]);
        $this->assertSame('Estado Del Hotel', $array["Estado"]);
        $this->assertSame(true, $array["Activa"]);
    }
}
