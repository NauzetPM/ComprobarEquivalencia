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
    private array $filtros;

    

    /**
     * __construct
     *
     * @param  mixed $gestorEstablecimientos
     * @param  mixed $equivalenciasDAO
     * @param  mixed $activaDao
     * @param  mixed $filtros
     * @return void
     */    
    public function __construct(GestorEstablecimientos $gestorEstablecimientos,
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