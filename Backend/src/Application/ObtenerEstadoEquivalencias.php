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




    /**
     *
     * @param GestorEstablecimientos gestorEstablecimientos
     * @param EquivalenciasDAO equivalenciasDAO
     * @param ActivaDao activaDao
     *
     */
    public function __construct(
        GestorEstablecimientos $gestorEstablecimientos,
        EquivalenciasDAO $equivalenciasDAO,
        ActivaDao $activaDao
    ) {
        $this->gestorEstablecimientos = $gestorEstablecimientos;
        $this->equivalenciasDao = $equivalenciasDAO;
        $this->activaDao = $activaDao;
    }



    /**
     * @return array
     */
    public function __invoke(): array
    {

        $respuesta = "";
        $datos = $this->gestorEstablecimientos->getDatos();
        $Comprobador = new ComprobadorEstado(
            $datos["datos"],
            $this->equivalenciasDao,
            $this->activaDao,
            $datos['total'],
        );
        $respuesta = $Comprobador->getEstados();
        return $respuesta;
    }
}
