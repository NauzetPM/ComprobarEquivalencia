<?php

namespace ComprobadorEquivalencias\Application;

use ComprobadorEquivalencias\Domain\ComprobadorEstado;
use ComprobadorEquivalencias\Domain\EquivalenciasDAO;
use ComprobadorEquivalencias\Domain\GestorEstablecimientos;
use ComprobadorEquivalencias\Domain\ActivaDao;

class ObtenerEstadoEquivalencias
{
    private GestorEstablecimientos $gestorEstablecimientos;
    private EquivalenciasDAO $equivalenciasDao;

    private ActivaDao $activaDao;
    private int $pagina;
    private int $registrosPorPagina;
    private array $filtros;

    /**
     * @param int $pagina
     * @param int $registrosPorPagina
     * @param GestorEstablecimientos $gestorEstablecimientos
     * @param EquivalenciasDAO $equivalenciasDao
     * @param array $filtros
     */
    public function __construct(
        GestorEstablecimientos $gestorEstablecimientos,
        EquivalenciasDAO $equivalenciasDAO,
        ActivaDao $activaDao,
        array $filtros = array()
    ) {
        $this->gestorEstablecimientos = $gestorEstablecimientos;
        $this->equivalenciasDao = $equivalenciasDAO;
        $this->filtros = $filtros;
        $this->activaDao = $activaDao;
    }


    /**
     *
     * @return array
     */
    public function __invoke(): array
    {

        $respuesta = "";
        $datos = $this->gestorEstablecimientos->getDatos();
        $Comprobador = new ComprobadorEstado
        (
            $datos["datos"],
            $this->equivalenciasDao,
            $this->activaDao
        );
        $respuesta = $Comprobador->getEstados();
        return $respuesta;
    }
}