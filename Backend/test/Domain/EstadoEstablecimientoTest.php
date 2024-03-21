<?php

namespace ComprobadorEquivalenciasTest\Domain;

use ComprobadorEquivalencias\Domain\DatosHoteles;
use ComprobadorEquivalencias\Domain\EstadoEstablecimiento;
use PHPUnit\Framework\TestCase;

class EstadoEstablecimientoTest extends TestCase
{
    /**
     * @test
     * @return void
     */
    public function es_establecimiento_mapeado_activo()
    {

        $totalMapeos = 1;
        $estaActivo = true;
        $usuarioMapeo = 'inceit';

        $instancia = new EstadoEstablecimiento(
            $totalMapeos,
            $estaActivo,
            $usuarioMapeo
        );

        $esperado = DatosHoteles::ESTADO_MAPEADO_ACTIVO;

        $resultado = $instancia->obtenerEstado();
        $this->assertEquals($esperado, $resultado);

    }
    /**
     * @test
     * @return void
     */
    public function es_establecimiento_mapeado_no_activo()
    {
        $totalMapeos = 1;
        $estaActivo = false;
        $usuarioMapeo = 'inventado';

        $instancia = new EstadoEstablecimiento(
            $totalMapeos,
            $estaActivo,
            $usuarioMapeo
        );

        $esperado = DatosHoteles::ESTADO_MAPEADO_NO_ACTIVO;

        $resultado = $instancia->obtenerEstado();
        $this->assertEquals($esperado, $resultado);

    }
    /**
     * @test
     * @return void
     */
    public function es_establecimiento_pendiente_activo()
    {
        $totalMapeos = 0;
        $estaActivo = true;
        $usuarioMapeo = 'inceit';

        $instancia = new EstadoEstablecimiento(
            $totalMapeos,
            $estaActivo,
            $usuarioMapeo
        );

        $esperado = DatosHoteles::ESTADO_PENDIENTE_ACTIVO;

        $resultado = $instancia->obtenerEstado();
        $this->assertEquals($esperado, $resultado);

    }
    /**
     * @test
     * @return void
     */
    public function es_establecimiento_pendiente_no_activo()
    {
        $totalMapeos = 0;
        $estaActivo = false;
        $usuarioMapeo = 'inceit';

        $instancia = new EstadoEstablecimiento(
            $totalMapeos,
            $estaActivo,
            $usuarioMapeo
        );

        $esperado = DatosHoteles::ESTADO_PENDIENTE_NO_ACTIVO;

        $resultado = $instancia->obtenerEstado();
        $this->assertEquals($esperado, $resultado);

    }

    /**
     * @test
     * @return void
     */
    public function es_establecimiento_mapeado_block_activo()
    {
        $totalMapeos = 1;
        $estaActivo = true;
        $usuarioMapeo = 'casamientoBlock';

        $instancia = new EstadoEstablecimiento(
            $totalMapeos,
            $estaActivo,
            $usuarioMapeo
        );

        $esperado = DatosHoteles::ESTADO_MAPEADO_BLOCK_ACTIVO;

        $resultado = $instancia->obtenerEstado();
        $this->assertEquals($esperado, $resultado);

    }
        /**
     * @test
     * @return void
     */
    public function es_establecimiento_mapeado_block_no_activo()
    {
        $totalMapeos = 1;
        $estaActivo = false;
        $usuarioMapeo = 'casamientoBlock';

        $instancia = new EstadoEstablecimiento(
            $totalMapeos,
            $estaActivo,
            $usuarioMapeo
        );

        $esperado = DatosHoteles::ESTADO_MAPEADO_BLOCK_NO_ACTIVO;

        $resultado = $instancia->obtenerEstado();
        $this->assertEquals($esperado, $resultado);

    }
}
