<?php

declare(strict_types=1);

namespace ComprobadorEquivalenciasTest\Domain;

use ComprobadorEquivalencias\Domain\DatosHoteles;
use ComprobadorEquivalencias\Domain\ActivaDao;
use ComprobadorEquivalencias\Domain\ActivaDaoDatos;
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
            "total" => 1, // mapeado
            "usuario" => 'tema'
        );
        $equivalenciasDao->method('comprobarEstado')
            ->willReturn($comprobarEstado);


        $resultadoComprobarEquivalencia = [
            'total' => 1, // descargado
            'activo' => 1
        ];
        $resultadoComprobarEquivalenciaClase = ActivaDaoDatos::fromArray($resultadoComprobarEquivalencia);
        $descargadoActivoDao->method('comprobarDescargadaActiva')
            ->willReturn($resultadoComprobarEquivalenciaClase);

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
            "total" => 1, // mapeado
            "usuario" => 'tema'
        );
        $equivalenciasDao->method('comprobarEstado')
            ->willReturn($comprobarEstado);


        $resultadoComprobarEquivalencia = [
            'total' => 1, // descargado
            'activo' => 0
        ];
        $resultadoComprobarEquivalenciaClase = ActivaDaoDatos::fromArray($resultadoComprobarEquivalencia);
        $descargadoActivoDao->method('comprobarDescargadaActiva')
            ->willReturn($resultadoComprobarEquivalenciaClase);

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
            "total" => 0, // mapeado
            "usuario" => 'asdasd'
        );
        $equivalenciasDao->method('comprobarEstado')
            ->willReturn($comprobarEstado);


        $resultadoComprobarEquivalencia = [
            'total' => 0, // descargado
            'activo' => 0
        ];
        $resultadoComprobarEquivalenciaClase = ActivaDaoDatos::fromArray($resultadoComprobarEquivalencia);
        $descargadoActivoDao->method('comprobarDescargadaActiva')
            ->willReturn($resultadoComprobarEquivalenciaClase);

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
            "total" => 0, // mapeado
            "usuario" => 'asdasd'
        );
        $equivalenciasDao->method('comprobarEstado')
            ->willReturn($comprobarEstado);


        $resultadoComprobarEquivalencia = [
            'total' => 1, // descargado
            'activo' => 1
        ];
        $resultadoComprobarEquivalenciaClase = ActivaDaoDatos::fromArray($resultadoComprobarEquivalencia);
        $descargadoActivoDao->method('comprobarDescargadaActiva')
            ->willReturn($resultadoComprobarEquivalenciaClase);

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
            "total" => 0, // mapeado
            "usuario" => 'asdasd'
        );
        $equivalenciasDao->method('comprobarEstado')
            ->willReturn($comprobarEstado);


        $resultadoComprobarEquivalencia = [
            'total' => 1, // descargado
            'activo' => 0
        ];
        $resultadoComprobarEquivalenciaClase = ActivaDaoDatos::fromArray($resultadoComprobarEquivalencia);
        $descargadoActivoDao->method('comprobarDescargadaActiva')
            ->willReturn($resultadoComprobarEquivalenciaClase);

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
            "total" => 1, // mapeado
            "usuario" => DatosHoteles::USUARIO_MAPEADO_BLOCK,
        );
        $equivalenciasDao->method('comprobarEstado')
            ->willReturn($comprobarEstado);


        $resultadoComprobarEquivalencia = [
            'total' => 1, // descargado
            'activo' => 0
        ];
        $resultadoComprobarEquivalenciaClase = ActivaDaoDatos::fromArray($resultadoComprobarEquivalencia);
        $descargadoActivoDao->method('comprobarDescargadaActiva')
            ->willReturn($resultadoComprobarEquivalenciaClase);

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
            "total" => 1, // mapeado
            "usuario" => DatosHoteles::USUARIO_MAPEADO_BLOCK,
        );
        $equivalenciasDao->method('comprobarEstado')
            ->willReturn($comprobarEstado);


        $resultadoComprobarEquivalencia = [
            'total' => 1, // descargado
            'activo' => 1
        ];
        $resultadoComprobarEquivalenciaClase = ActivaDaoDatos::fromArray($resultadoComprobarEquivalencia);
        $descargadoActivoDao->method('comprobarDescargadaActiva')
            ->willReturn($resultadoComprobarEquivalenciaClase);

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
            "total" => 1, // mapeado
            "usuario" => DatosHoteles::USUARIO_MAPEADO_BLOCK,
        );
        $equivalenciasDao->method('comprobarEstado')
            ->willReturn($comprobarEstado);


        $resultadoComprobarEquivalencia1 = [
            'total' => 1, // descargado
            'activo' => 1
        ];
        $resultadoComprobarEquivalencia2 = [
            'total' => 1, // descargado
            'activo' => 0
        ];
        $resultadoComprobarEquivalenciaClase1 = ActivaDaoDatos::fromArray($resultadoComprobarEquivalencia1);
        $resultadoComprobarEquivalenciaClase2 = ActivaDaoDatos::fromArray($resultadoComprobarEquivalencia2);
        $descargadoActivoDao->method('comprobarDescargadaActiva')
            ->willReturnOnConsecutiveCalls($resultadoComprobarEquivalenciaClase1, $resultadoComprobarEquivalenciaClase2);

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
            'activaTotal' => 1,
            'activaPendiente' => 0,
            'activaMapeado' => 0,
            'activaBlock' => 1,
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
            "Activa" => true,
        ];
        $esperado['datos'][] = [
            "Codigo" => '2',
            "Nombre" => 'Nauzet2',
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
    public function estadistica_con_mapeado(): void
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
            "total" => 1, // mapeado
            "usuario" => "Usuario",
        );
        $equivalenciasDao->method('comprobarEstado')
            ->willReturn($comprobarEstado);


        $resultadoComprobarEquivalencia1 = [
            'total' => 1, // descargado
            'activo' => 1
        ];
        $resultadoComprobarEquivalencia2 = [
            'total' => 1, // descargado
            'activo' => 0
        ];
        $resultadoComprobarEquivalenciaClase1 = ActivaDaoDatos::fromArray($resultadoComprobarEquivalencia1);
        $resultadoComprobarEquivalenciaClase2 = ActivaDaoDatos::fromArray($resultadoComprobarEquivalencia2);
        $descargadoActivoDao->method('comprobarDescargadaActiva')
            ->willReturnOnConsecutiveCalls($resultadoComprobarEquivalenciaClase1, $resultadoComprobarEquivalenciaClase2);

        $instancia = new ComprobadorEstado(
            $datosEntrada,
            $equivalenciasDao,
            $descargadoActivoDao,
            2
        );

        $estadisticas = [
            'total' => 2,
            'pendientes' => 0,
            'mapeados' => 2,
            'mapeadosBlock' => 0,
            'noDescargados' => 0,
            'activaTotal' => 1,
            'activaPendiente' => 0,
            'activaMapeado' => 1,
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
            "Activa" => true,
        ];
        $esperado['datos'][] = [
            "Codigo" => '2',
            "Nombre" => 'Nauzet2',
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
    public function estadistica_con_pendiente(): void
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
            "total" => 0, // mapeado
            "usuario" => "Usuario",
        );
        $equivalenciasDao->method('comprobarEstado')
            ->willReturn($comprobarEstado);


        $resultadoComprobarEquivalencia1 = [
            'total' => 1, // descargado
            'activo' => 1
        ];
        $resultadoComprobarEquivalencia2 = [
            'total' => 1, // descargado
            'activo' => 0
        ];
        $resultadoComprobarEquivalenciaClase1 = ActivaDaoDatos::fromArray($resultadoComprobarEquivalencia1);
        $resultadoComprobarEquivalenciaClase2 = ActivaDaoDatos::fromArray($resultadoComprobarEquivalencia2);
        $descargadoActivoDao->method('comprobarDescargadaActiva')
            ->willReturnOnConsecutiveCalls($resultadoComprobarEquivalenciaClase1, $resultadoComprobarEquivalenciaClase2);

        $instancia = new ComprobadorEstado(
            $datosEntrada,
            $equivalenciasDao,
            $descargadoActivoDao,
            2
        );

        $estadisticas = [
            'total' => 2,
            'pendientes' => 2,
            'mapeados' => 0,
            'mapeadosBlock' => 0,
            'noDescargados' => 0,
            'activaTotal' => 1,
            'activaPendiente' => 1,
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
            "Activa" => true,
        ];
        $esperado['datos'][] = [
            "Codigo" => '2',
            "Nombre" => 'Nauzet2',
            "Estado" => DatosHoteles::ESTADO_PENDIENTE,
            "Activa" => false,
        ];
        $resultado = $instancia->getEstados();
        $this->assertEquals($esperado, $resultado);
    }
    /**
     * @test
     * @return void
     */
    public function estadistica_con_todo(): void
    {
        /*
        1º Pendiente Activo
        2º Pendiente No Activo
        3º Mapeado Activo
        4º Mapeado No activo
        5º Block Activo
        6º Block No Activo
        7º No descargado
        */

        // Dependencias
        $datosEntrada = [
            new EstablecimientoMayorista('1', 'Nauzet'),
            new EstablecimientoMayorista('2', 'Nauzet2'),
            new EstablecimientoMayorista('3', 'Nauzet3'),
            new EstablecimientoMayorista('4', 'Nauzet4'),
            new EstablecimientoMayorista('5', 'Nauzet5'),
            new EstablecimientoMayorista('6', 'Nauzet6'),
            new EstablecimientoMayorista('7', 'Nauzet7'),
        ];
        /** @var EquivalenciasDAO|\PHPUnit\Framework\MockObject\MockObject $equivalenciasDao */
        $equivalenciasDao = $this->getMockBuilder(EquivalenciasDAO::class)
            ->disableOriginalConstructor()
            ->getMock();
        /** @var ActivaDao|\PHPUnit\Framework\MockObject\MockObject $descargadoActivoDao */
        $descargadoActivoDao = $this->getMockBuilder(ActivaDao::class)
            ->disableOriginalConstructor()
            ->getMock();

        $comprobarEstado1 = array(
            "total" => 0, // mapeado
            "usuario" => "Usuario",
        );
        $comprobarEstado2 = array(
            "total" => 0, // mapeado
            "usuario" => "Usuario",
        );
        $comprobarEstado3 = array(
            "total" => 1, // mapeado
            "usuario" => "Usuario",
        );
        $comprobarEstado4 = array(
            "total" => 1, // mapeado
            "usuario" => "Usuario",
        );
        $comprobarEstado5 = array(
            "total" => 1, // mapeado
            "usuario" => DatosHoteles::USUARIO_MAPEADO_BLOCK,
        );
        $comprobarEstado6 = array(
            "total" => 1, // mapeado
            "usuario" => DatosHoteles::USUARIO_MAPEADO_BLOCK,
        );
        $comprobarEstado7 = array(
            "total" => 0, // mapeado
            "usuario" => "Usuario",
        );


        $equivalenciasDao->method('comprobarEstado')
            ->willReturnOnConsecutiveCalls(
                $comprobarEstado1,
                $comprobarEstado2,
                $comprobarEstado3,
                $comprobarEstado4,
                $comprobarEstado5,
                $comprobarEstado6,
                $comprobarEstado7,
            );


        $resultadoComprobarEquivalencia1 = [
            'total' => 1, // descargado
            'activo' => 1
        ];
        $resultadoComprobarEquivalencia2 = [
            'total' => 1, // descargado
            'activo' => 0
        ];
        $resultadoComprobarEquivalencia3 = [
            'total' => 1, // descargado
            'activo' => 1
        ];
        $resultadoComprobarEquivalencia4 = [
            'total' => 1, // descargado
            'activo' => 0
        ];
        $resultadoComprobarEquivalencia5 = [
            'total' => 1, // descargado
            'activo' => 1
        ];
        $resultadoComprobarEquivalencia6 = [
            'total' => 1, // descargado
            'activo' => 0
        ];
        $resultadoComprobarEquivalencia7 = [
            'total' => 0, // descargado
            'activo' => 0
        ];
        $resultadoComprobarEquivalenciaClase1 = ActivaDaoDatos::fromArray($resultadoComprobarEquivalencia1);
        $resultadoComprobarEquivalenciaClase2 = ActivaDaoDatos::fromArray($resultadoComprobarEquivalencia2);
        $resultadoComprobarEquivalenciaClase3 = ActivaDaoDatos::fromArray($resultadoComprobarEquivalencia3);
        $resultadoComprobarEquivalenciaClase4 = ActivaDaoDatos::fromArray($resultadoComprobarEquivalencia4);
        $resultadoComprobarEquivalenciaClase5 = ActivaDaoDatos::fromArray($resultadoComprobarEquivalencia5);
        $resultadoComprobarEquivalenciaClase6 = ActivaDaoDatos::fromArray($resultadoComprobarEquivalencia6);
        $resultadoComprobarEquivalenciaClase7 = ActivaDaoDatos::fromArray($resultadoComprobarEquivalencia7);

        $descargadoActivoDao->method('comprobarDescargadaActiva')
            ->willReturnOnConsecutiveCalls(
                $resultadoComprobarEquivalenciaClase1,
                $resultadoComprobarEquivalenciaClase2,
                $resultadoComprobarEquivalenciaClase3,
                $resultadoComprobarEquivalenciaClase4,
                $resultadoComprobarEquivalenciaClase5,
                $resultadoComprobarEquivalenciaClase6,
                $resultadoComprobarEquivalenciaClase7,
            );

        $instancia = new ComprobadorEstado(
            $datosEntrada,
            $equivalenciasDao,
            $descargadoActivoDao,
            7
        );

        $estadisticas = [
            'total' => 7,
            'pendientes' => 2,
            'mapeados' => 2,
            'mapeadosBlock' => 2,
            'noDescargados' => 1,
            'activaTotal' => 3,
            'activaPendiente' => 1,
            'activaMapeado' => 1,
            'activaBlock' => 1,
            'noActivaTotal' => 3,
            'noActivaMapeado' => 1,
            'noActivaBlock' => 1,
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
            "Activa" => true,
        ];
        $esperado['datos'][] = [
            "Codigo" => '2',
            "Nombre" => 'Nauzet2',
            "Estado" => DatosHoteles::ESTADO_PENDIENTE,
            "Activa" => false,
        ];
        $esperado['datos'][] = [
            "Codigo" => '3',
            "Nombre" => 'Nauzet3',
            "Estado" => DatosHoteles::ESTADO_MAPEADO,
            "Activa" => true,
        ];
        $esperado['datos'][] = [
            "Codigo" => '4',
            "Nombre" => 'Nauzet4',
            "Estado" => DatosHoteles::ESTADO_MAPEADO,
            "Activa" => false,
        ];
        $esperado['datos'][] = [
            "Codigo" => '5',
            "Nombre" => 'Nauzet5',
            "Estado" => DatosHoteles::ESTADO_BLOCK,
            "Activa" => true,
        ];
        $esperado['datos'][] = [
            "Codigo" => '6',
            "Nombre" => 'Nauzet6',
            "Estado" => DatosHoteles::ESTADO_BLOCK,
            "Activa" => false,
        ];
        $esperado['datos'][] = [
            "Codigo" => '7',
            "Nombre" => 'Nauzet7',
            "Estado" => DatosHoteles::NO_DESCARGADA,
            "Activa" => false,
        ];
        $resultado = $instancia->getEstados();
        $this->assertEquals($esperado, $resultado);
    }
}
