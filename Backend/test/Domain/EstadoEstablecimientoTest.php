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
    public function es_establecimiento_mapeado_activo(): void
    {

        $totalMapeos = 1;
        $estaActivo = true;
        $usuarioMapeo = 'inceit';

        $instancia = new EstadoEstablecimiento(
            $totalMapeos,
            $estaActivo,
            $usuarioMapeo
        );

        $datoEsperado = DatosHoteles::ESTADO_MAPEADO_ACTIVO;

        $dato = $instancia->obtenerEstado();
        $this->assertEquals($datoEsperado, $dato);
        $estado = $instancia->obtenerTipoEquivalencia();
        $estadoEsperado = DatosHoteles::ESTADO_MAPEADO;
        $this->assertEquals($estadoEsperado, $estado);
    }
    /**
     * @test
     * @return void
     */
    public function es_establecimiento_mapeado_no_activo(): void
    {
        $totalMapeos = 1;
        $estaActivo = false;
        $usuarioMapeo = 'inventado';

        $instancia = new EstadoEstablecimiento(
            $totalMapeos,
            $estaActivo,
            $usuarioMapeo
        );

        $datoEsperado = DatosHoteles::ESTADO_MAPEADO_NO_ACTIVO;

        $dato = $instancia->obtenerEstado();
        $this->assertEquals($datoEsperado, $dato);
        $estado = $instancia->obtenerTipoEquivalencia();
        $estadoEsperado = DatosHoteles::ESTADO_MAPEADO;
        $this->assertEquals($estadoEsperado, $estado);
    }
    /**
     * @test
     * @return void
     */
    public function es_establecimiento_pendiente_activo(): void
    {
        $totalMapeos = 0;
        $estaActivo = true;
        $usuarioMapeo = 'inceit';

        $instancia = new EstadoEstablecimiento(
            $totalMapeos,
            $estaActivo,
            $usuarioMapeo
        );

        $datoEsperado = DatosHoteles::ESTADO_PENDIENTE_ACTIVO;

        $dato = $instancia->obtenerEstado();
        $this->assertEquals($datoEsperado, $dato);
        $estado = $instancia->obtenerTipoEquivalencia();
        $estadoEsperado = DatosHoteles::ESTADO_PENDIENTE;
        $this->assertEquals($estadoEsperado, $estado);
    }
    /**
     * @test
     * @return void
     */
    public function es_establecimiento_pendiente_no_activo(): void
    {
        $totalMapeos = 0;
        $estaActivo = false;
        $usuarioMapeo = 'inceit';

        $instancia = new EstadoEstablecimiento(
            $totalMapeos,
            $estaActivo,
            $usuarioMapeo
        );

        $datoEsperado = DatosHoteles::ESTADO_PENDIENTE_NO_ACTIVO;

        $dato = $instancia->obtenerEstado();
        $this->assertEquals($datoEsperado, $dato);
        $estado = $instancia->obtenerTipoEquivalencia();
        $estadoEsperado = DatosHoteles::ESTADO_PENDIENTE;
        $this->assertEquals($estadoEsperado, $estado);
    }

    /**
     * @test
     * @return void
     */
    public function es_establecimiento_mapeado_block_activo(): void
    {
        $totalMapeos = 1;
        $estaActivo = true;
        $usuarioMapeo = DatosHoteles::USUARIO_MAPEADO_BLOCK;

        $instancia = new EstadoEstablecimiento(
            $totalMapeos,
            $estaActivo,
            $usuarioMapeo
        );

        $datoEsperado = DatosHoteles::ESTADO_MAPEADO_BLOCK_ACTIVO;

        $dato = $instancia->obtenerEstado();
        $this->assertEquals($datoEsperado, $dato);
        $estado = $instancia->obtenerTipoEquivalencia();
        $estadoEsperado = DatosHoteles::ESTADO_BLOCK;
        $this->assertEquals($estadoEsperado, $estado);
    }
    /**
     * @test
     * @return void
     */
    public function es_establecimiento_mapeado_block_no_activo(): void
    {
        $totalMapeos = 1;
        $estaActivo = false;
        $usuarioMapeo = DatosHoteles::USUARIO_MAPEADO_BLOCK;

        $instancia = new EstadoEstablecimiento(
            $totalMapeos,
            $estaActivo,
            $usuarioMapeo
        );

        $datoEsperado = DatosHoteles::ESTADO_MAPEADO_BLOCK_NO_ACTIVO;

        $dato = $instancia->obtenerEstado();
        $this->assertEquals($datoEsperado, $dato);
        $estado = $instancia->obtenerTipoEquivalencia();
        $estadoEsperado = DatosHoteles::ESTADO_BLOCK;
        $this->assertEquals($estadoEsperado, $estado);
    }
}
