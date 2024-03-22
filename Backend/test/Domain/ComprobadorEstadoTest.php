<?php

namespace ComprobadorEquivalenciasTest\Domain;

use ComprobadorEquivalencias\Domain\DatosHoteles;
use ComprobadorEquivalencias\Domain\ActivaDao;
use ComprobadorEquivalencias\Domain\ComprobadorEstado;
use ComprobadorEquivalencias\Domain\EquivalenciasDAO;
use ComprobadorEquivalencias\Domain\EstablecimientoMayorista;
use PHPUnit\Framework\TestCase;

class ComprobadorEstadoTest extends TestCase
{


    /**
     * @test
     * @return void
     */
    public function estadistica_vacia(): void
    {
        // Dependencias
        $datosEntrada = [];
        /** @var EquivalenciasDAO|\PHPUnit\Framework\MockObject\MockObject $equivalenciasDao */
        $equivalenciasDao = $this->getMockBuilder(EquivalenciasDAO::class)
            ->disableOriginalConstructor()
            ->getMock();
        /** @var ActivaDao|\PHPUnit\Framework\MockObject\MockObject $descargadoActivoDao */
        $descargadoActivoDao = $this->getMockBuilder(ActivaDao::class)
            ->disableOriginalConstructor()
            ->getMock();

        $devuelto = array(
            "total" => 0,
            "usuario" => 'tema'
        );
        $equivalenciasDao->method('comprobarEstado')
            ->willReturn($devuelto);

        $instancia = new ComprobadorEstado(
            $datosEntrada,
            $equivalenciasDao,
            $descargadoActivoDao,
            0
        );

        $estadisticas = [
            'total' => 0,
            'pendientes' => 0,
            'mapeados' => 0,
            'mapeadosBlock' => 0,
            'noDescargados' => 0,
            'activaTotal' => 0,
            'activaPendiente' => 0,
            'activaMapeado' => 0,
            'activaBlock' => 0,
            'noActivaTotal' => 0,
            'noActivaMapeado' => 0,
            'noActivaBlock' => 0,
            'noActivaPendiente' => 0
        ];
        $esperado = [
            'estadisticas' => $estadisticas,
            'datos' => []
        ];
        $resultado = $instancia->getEstados();
        $this->assertEquals($esperado, $resultado);
    }

    /**
     * @test
     * @return void
     */
    public function estadistica_con_mapeados_activa(): void
    {
        // Dependencias
        $datosEntrada = [
            new EstablecimientoMayorista('1', 'Nauzet')
        ];
        /** @var EquivalenciasDAO|\PHPUnit\Framework\MockObject\MockObject $equivalenciasDao */
        $equivalenciasDao = $this->getMockBuilder(EquivalenciasDAO::class)
            ->disableOriginalConstructor()
            ->getMock();
        /** @var ActivaDao|\PHPUnit\Framework\MockObject\MockObject $descargadoActivoDao */
        $descargadoActivoDao = $this->getMockBuilder(ActivaDao::class)
            ->disableOriginalConstructor()
            ->getMock();

        $comprobarEstado = array(
            "total" => 1, // mapeoado
            "usuario" => 'tema'
        );
        $equivalenciasDao->method('comprobarEstado')
            ->willReturn($comprobarEstado);


        $resultadoComprobarEquivalencia = [
            'total' => 1, // descargado
            'activo' => 1
        ];
        $descargadoActivoDao->method('comprobarDescargadaActiva')
            ->willReturn($resultadoComprobarEquivalencia);

        $instancia = new ComprobadorEstado(
            $datosEntrada,
            $equivalenciasDao,
            $descargadoActivoDao,
            1
        );

        $estadisticas = [
            'total' => 1,
            'pendientes' => 0,
            'mapeados' => 1,
            'mapeadosBlock' => 0,
            'noDescargados' => 0,
            'activaTotal' => 1,
            'activaPendiente' => 0,
            'activaMapeado' => 1,
            'activaBlock' => 0,
            'noActivaTotal' => 0,
            'noActivaMapeado' => 0,
            'noActivaBlock' => 0,
            'noActivaPendiente' => 0
        ];
        $esperado = [
            'estadisticas' => $estadisticas,
            'datos' => []
        ];
        $esperado['datos'][] = [
            "Codigo" => '1',
            "Nombre" => 'Nauzet',
            "Estado" => DatosHoteles::ESTADO_MAPEADO,
            "Activa" => true,
        ];
        $resultado = $instancia->getEstados();
        $this->assertEquals($esperado, $resultado);
    }

    /**
     * @test
     * @return void
     */
    public function estadistica_con_mapeados_no_activa(): void
    {
        // Dependencias
        $datosEntrada = [
            new EstablecimientoMayorista('1', 'Nauzet')
        ];
        /** @var EquivalenciasDAO|\PHPUnit\Framework\MockObject\MockObject $equivalenciasDao */
        $equivalenciasDao = $this->getMockBuilder(EquivalenciasDAO::class)
            ->disableOriginalConstructor()
            ->getMock();
        /** @var ActivaDao|\PHPUnit\Framework\MockObject\MockObject $descargadoActivoDao */
        $descargadoActivoDao = $this->getMockBuilder(ActivaDao::class)
            ->disableOriginalConstructor()
            ->getMock();

        $comprobarEstado = array(
            "total" => 1, // mapeoado
            "usuario" => 'tema'
        );
        $equivalenciasDao->method('comprobarEstado')
            ->willReturn($comprobarEstado);


        $resultadoComprobarEquivalencia = [
            'total' => 1, // descargado
            'activo' => 0
        ];
        $descargadoActivoDao->method('comprobarDescargadaActiva')
            ->willReturn($resultadoComprobarEquivalencia);

        $instancia = new ComprobadorEstado(
            $datosEntrada,
            $equivalenciasDao,
            $descargadoActivoDao,
            1
        );

        $estadisticas = [
            'total' => 1,
            'pendientes' => 0,
            'mapeados' => 1,
            'mapeadosBlock' => 0,
            'noDescargados' => 0,
            'activaTotal' => 0,
            'activaPendiente' => 0,
            'activaMapeado' => 0,
            'activaBlock' => 0,
            'noActivaTotal' => 1,
            'noActivaMapeado' => 1,
            'noActivaBlock' => 0,
            'noActivaPendiente' => 0
        ];
        $esperado = [
            'estadisticas' => $estadisticas,
            'datos' => []
        ];
        $esperado['datos'][] = [
            "Codigo" => '1',
            "Nombre" => 'Nauzet',
            "Estado" => DatosHoteles::ESTADO_MAPEADO,
            "Activa" => false,
        ];
        $resultado = $instancia->getEstados();
        $this->assertEquals($esperado, $resultado);
    }

    /**
     * @test
     * @return void
     */
    public function estadistica_con_no_descargada(): void
    {
        // Dependencias
        $datosEntrada = [
            new EstablecimientoMayorista('1', 'Nauzet')
        ];
        /** @var EquivalenciasDAO|\PHPUnit\Framework\MockObject\MockObject $equivalenciasDao */
        $equivalenciasDao = $this->getMockBuilder(EquivalenciasDAO::class)
            ->disableOriginalConstructor()
            ->getMock();
        /** @var ActivaDao|\PHPUnit\Framework\MockObject\MockObject $descargadoActivoDao */
        $descargadoActivoDao = $this->getMockBuilder(ActivaDao::class)
            ->disableOriginalConstructor()
            ->getMock();

        $comprobarEstado = array(
            "total" => 0, // mapeoado
            "usuario" => 'asdasd'
        );
        $equivalenciasDao->method('comprobarEstado')
            ->willReturn($comprobarEstado);


        $resultadoComprobarEquivalencia = [
            'total' => 0, // descargado
            'activo' => 0
        ];
        $descargadoActivoDao->method('comprobarDescargadaActiva')
            ->willReturn($resultadoComprobarEquivalencia);

        $instancia = new ComprobadorEstado(
            $datosEntrada,
            $equivalenciasDao,
            $descargadoActivoDao,
            1
        );

        $estadisticas = [
            'total' => 1,
            'pendientes' => 0,
            'mapeados' => 0,
            'mapeadosBlock' => 0,
            'noDescargados' => 1,
            'activaTotal' => 0,
            'activaPendiente' => 0,
            'activaMapeado' => 0,
            'activaBlock' => 0,
            'noActivaTotal' => 0,
            'noActivaMapeado' => 0,
            'noActivaBlock' => 0,
            'noActivaPendiente' => 0
        ];
        $esperado = [
            'estadisticas' => $estadisticas,
            'datos' => []
        ];
        $esperado['datos'][] = [
            "Codigo" => '1',
            "Nombre" => 'Nauzet',
            "Estado" => DatosHoteles::NO_DESCARGADA,
            "Activa" => false,
        ];
        $resultado = $instancia->getEstados();
        $this->assertEquals($esperado, $resultado);
    }
    /**
     * @test
     * @return void
     */
    public function estadistica_con_pendiente_activa(): void
    {
        // Dependencias
        $datosEntrada = [
            new EstablecimientoMayorista('1', 'Nauzet')
        ];
        /** @var EquivalenciasDAO|\PHPUnit\Framework\MockObject\MockObject $equivalenciasDao */
        $equivalenciasDao = $this->getMockBuilder(EquivalenciasDAO::class)
            ->disableOriginalConstructor()
            ->getMock();
        /** @var ActivaDao|\PHPUnit\Framework\MockObject\MockObject $descargadoActivoDao */
        $descargadoActivoDao = $this->getMockBuilder(ActivaDao::class)
            ->disableOriginalConstructor()
            ->getMock();

        $comprobarEstado = array(
            "total" => 0, // mapeoado
            "usuario" => 'asdasd'
        );
        $equivalenciasDao->method('comprobarEstado')
            ->willReturn($comprobarEstado);


        $resultadoComprobarEquivalencia = [
            'total' => 1, // descargado
            'activo' => 1
        ];
        $descargadoActivoDao->method('comprobarDescargadaActiva')
            ->willReturn($resultadoComprobarEquivalencia);

        $instancia = new ComprobadorEstado(
            $datosEntrada,
            $equivalenciasDao,
            $descargadoActivoDao,
            1
        );

        $estadisticas = [
            'total' => 1,
            'pendientes' => 1,
            'mapeados' => 0,
            'mapeadosBlock' => 0,
            'noDescargados' => 0,
            'activaTotal' => 1,
            'activaPendiente' => 1,
            'activaMapeado' => 0,
            'activaBlock' => 0,
            'noActivaTotal' => 0,
            'noActivaMapeado' => 0,
            'noActivaBlock' => 0,
            'noActivaPendiente' => 0
        ];
        $esperado = [
            'estadisticas' => $estadisticas,
            'datos' => []
        ];
        $esperado['datos'][] = [
            "Codigo" => '1',
            "Nombre" => 'Nauzet',
            "Estado" => DatosHoteles::ESTADO_PENDIENTE,
            "Activa" => true,
        ];
        $resultado = $instancia->getEstados();
        $this->assertEquals($esperado, $resultado);
    }
    /**
     * @test
     * @return void
     */
    public function estadistica_con_pendiente_no_activa(): void
    {
        // Dependencias
        $datosEntrada = [
            new EstablecimientoMayorista('1', 'Nauzet')
        ];
        /** @var EquivalenciasDAO|\PHPUnit\Framework\MockObject\MockObject $equivalenciasDao */
        $equivalenciasDao = $this->getMockBuilder(EquivalenciasDAO::class)
            ->disableOriginalConstructor()
            ->getMock();
        /** @var ActivaDao|\PHPUnit\Framework\MockObject\MockObject $descargadoActivoDao */
        $descargadoActivoDao = $this->getMockBuilder(ActivaDao::class)
            ->disableOriginalConstructor()
            ->getMock();

        $comprobarEstado = array(
            "total" => 0, // mapeoado
            "usuario" => 'asdasd'
        );
        $equivalenciasDao->method('comprobarEstado')
            ->willReturn($comprobarEstado);


        $resultadoComprobarEquivalencia = [
            'total' => 1, // descargado
            'activo' => 0
        ];
        $descargadoActivoDao->method('comprobarDescargadaActiva')
            ->willReturn($resultadoComprobarEquivalencia);

        $instancia = new ComprobadorEstado(
            $datosEntrada,
            $equivalenciasDao,
            $descargadoActivoDao,
            1
        );

        $estadisticas = [
            'total' => 1,
            'pendientes' => 1,
            'mapeados' => 0,
            'mapeadosBlock' => 0,
            'noDescargados' => 0,
            'activaTotal' => 0,
            'activaPendiente' => 0,
            'activaMapeado' => 0,
            'activaBlock' => 0,
            'noActivaTotal' => 1,
            'noActivaMapeado' => 0,
            'noActivaBlock' => 0,
            'noActivaPendiente' => 1
        ];
        $esperado = [
            'estadisticas' => $estadisticas,
            'datos' => []
        ];
        $esperado['datos'][] = [
            "Codigo" => '1',
            "Nombre" => 'Nauzet',
            "Estado" => DatosHoteles::ESTADO_PENDIENTE,
            "Activa" => false,
        ];
        $resultado = $instancia->getEstados();
        $this->assertEquals($esperado, $resultado);
    }
    /**
     * @test
     * @return void
     * 
     */
    public function estadistica_con_block_no_activa(): void
    {
        // Dependencias
        $datosEntrada = [
            new EstablecimientoMayorista('1', 'Nauzet')
        ];

        /** @var EquivalenciasDAO|\PHPUnit\Framework\MockObject\MockObject $equivalenciasDao */
        $equivalenciasDao = $this->getMockBuilder(EquivalenciasDAO::class)
            ->disableOriginalConstructor()
            ->getMock();
        /** @var ActivaDao|\PHPUnit\Framework\MockObject\MockObject $descargadoActivoDao */
        $descargadoActivoDao = $this->getMockBuilder(ActivaDao::class)
            ->disableOriginalConstructor()
            ->getMock();

        $comprobarEstado = array(
            "total" => 1, // mapeoado
            "usuario" => DatosHoteles::USUARIO_MAPEADO_BLOCK,
        );
        $equivalenciasDao->method('comprobarEstado')
            ->willReturn($comprobarEstado);


        $resultadoComprobarEquivalencia = [
            'total' => 1, // descargado
            'activo' => 0
        ];
        $descargadoActivoDao->method('comprobarDescargadaActiva')
            ->willReturn($resultadoComprobarEquivalencia);

        $instancia = new ComprobadorEstado(
            $datosEntrada,
            $equivalenciasDao,
            $descargadoActivoDao,
            1
        );

        $estadisticas = [
            'total' => 1,
            'pendientes' => 0,
            'mapeados' => 0,
            'mapeadosBlock' => 1,
            'noDescargados' => 0,
            'activaTotal' => 0,
            'activaPendiente' => 0,
            'activaMapeado' => 0,
            'activaBlock' => 0,
            'noActivaTotal' => 1,
            'noActivaMapeado' => 0,
            'noActivaBlock' => 1,
            'noActivaPendiente' => 0
        ];
        $esperado = [
            'estadisticas' => $estadisticas,
            'datos' => []
        ];
        $esperado['datos'][] = [
            "Codigo" => '1',
            "Nombre" => 'Nauzet',
            "Estado" => DatosHoteles::ESTADO_BLOCK,
            "Activa" => false,
        ];
        $resultado = $instancia->getEstados();
        $this->assertEquals($esperado, $resultado);
    }

    /**
     * @test
     * @return void
     */
    public function estadistica_con_block_activa(): void
    {
        // Dependencias
        $datosEntrada = [
            new EstablecimientoMayorista('1', 'Nauzet')
        ];
        /** @var EquivalenciasDAO|\PHPUnit\Framework\MockObject\MockObject $equivalenciasDao */
        $equivalenciasDao = $this->getMockBuilder(EquivalenciasDAO::class)
            ->disableOriginalConstructor()
            ->getMock();
        /** @var ActivaDao|\PHPUnit\Framework\MockObject\MockObject $descargadoActivoDao */
        $descargadoActivoDao = $this->getMockBuilder(ActivaDao::class)
            ->disableOriginalConstructor()
            ->getMock();

        $comprobarEstado = array(
            "total" => 1, // mapeoado
            "usuario" => DatosHoteles::USUARIO_MAPEADO_BLOCK,
        );
        $equivalenciasDao->method('comprobarEstado')
            ->willReturn($comprobarEstado);


        $resultadoComprobarEquivalencia = [
            'total' => 1, // descargado
            'activo' => 1
        ];
        $descargadoActivoDao->method('comprobarDescargadaActiva')
            ->willReturn($resultadoComprobarEquivalencia);

        $instancia = new ComprobadorEstado(
            $datosEntrada,
            $equivalenciasDao,
            $descargadoActivoDao,
            1
        );

        $estadisticas = [
            'total' => 1,
            'pendientes' => 0,
            'mapeados' => 0,
            'mapeadosBlock' => 1,
            'noDescargados' => 0,
            'activaTotal' => 1,
            'activaPendiente' => 0,
            'activaMapeado' => 0,
            'activaBlock' => 1,
            'noActivaTotal' => 0,
            'noActivaMapeado' => 0,
            'noActivaBlock' => 0,
            'noActivaPendiente' => 0
        ];
        $esperado = [
            'estadisticas' => $estadisticas,
            'datos' => []
        ];
        $esperado['datos'][] = [
            "Codigo" => '1',
            "Nombre" => 'Nauzet',
            "Estado" => DatosHoteles::ESTADO_BLOCK,
            "Activa" => true,
        ];
        $resultado = $instancia->getEstados();
        $this->assertEquals($esperado, $resultado);
    }
    /**
     * @test
     * @return void
     */
    public function estadistica_con_block(): void
    {
        // Dependencias
        $datosEntrada = [
            new EstablecimientoMayorista('1', 'Nauzet'),
            new EstablecimientoMayorista('2', 'Nauzet2')
        ];
        /** @var EquivalenciasDAO|\PHPUnit\Framework\MockObject\MockObject $equivalenciasDao */
        $equivalenciasDao = $this->getMockBuilder(EquivalenciasDAO::class)
            ->disableOriginalConstructor()
            ->getMock();
        /** @var ActivaDao|\PHPUnit\Framework\MockObject\MockObject $descargadoActivoDao */
        $descargadoActivoDao = $this->getMockBuilder(ActivaDao::class)
            ->disableOriginalConstructor()
            ->getMock();

        $comprobarEstado = array(
            "total" => 1, // mapeoado
            "usuario" => DatosHoteles::USUARIO_MAPEADO_BLOCK,
        );
        $equivalenciasDao->method('comprobarEstado')
            ->willReturn($comprobarEstado);


        $resultadoComprobarEquivalencia = [
            'total' => 1, // descargado
            'activo' => 1
        ];
        $descargadoActivoDao->method('comprobarDescargadaActiva')
            ->willReturn($resultadoComprobarEquivalencia);

        $instancia = new ComprobadorEstado(
            $datosEntrada,
            $equivalenciasDao,
            $descargadoActivoDao,
            2
        );

        $estadisticas = [
            'total' => 2,
            'pendientes' => 0,
            'mapeados' => 0,
            'mapeadosBlock' => 2,
            'noDescargados' => 0,
            'activaTotal' => 2,
            'activaPendiente' => 0,
            'activaMapeado' => 0,
            'activaBlock' => 2,
            'noActivaTotal' => 0,
            'noActivaMapeado' => 0,
            'noActivaBlock' => 0,
            'noActivaPendiente' => 0
        ];
        $esperado = [
            'estadisticas' => $estadisticas,
            'datos' => []
        ];
        $esperado['datos'][] = [
            "Codigo" => '1',
            "Nombre" => 'Nauzet',
            "Estado" => DatosHoteles::ESTADO_BLOCK,
            "Activa" => true,
        ];
        $esperado['datos'][] = [
            "Codigo" => '2',
            "Nombre" => 'Nauzet2',
            "Estado" => DatosHoteles::ESTADO_BLOCK,
            "Activa" => true,
        ];
        $resultado = $instancia->getEstados();
        $this->assertEquals($esperado, $resultado);
    }
}
