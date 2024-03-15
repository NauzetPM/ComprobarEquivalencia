<?php declare(strict_types=1);

namespace ComprobadorEquivalencias\Application;

use ComprobadorEquivalencias\Domain\ComprobadorEstado;
use ComprobadorEquivalencias\Domain\EquivalenciasDAO;
use ComprobadorEquivalencias\Domain\GestorEstablecimientos;

class ObtenerEstadoEquivalencias
{
    private GestorEstablecimientos $gestorFicheroCSV;
    private EquivalenciasDAO $equivalenciasDao;
    private int $pagina;
    private int $registrosPorPagina;
    private array $filtros;

    /**
     * @param int $pagina
     * @param int $registrosPorPagina
     * @param GestorEstablecimientos $gestorFicheroCSV
     * @param EquivalenciasDAO $equivalenciasDao
     * @param array $filtros
     */
    public function __construct(
        GestorEstablecimientos $gestorFicheroCSV,
        EquivalenciasDAO $equivalenciasDAO,
        array $filtros = array()
    ) {
        $this->gestorFicheroCSV = $gestorFicheroCSV;
        $this->equivalenciasDao = $equivalenciasDAO;
        $this->filtros = $filtros;
    }


    /**
     *
     * @return array
     */
    public function __invoke(): array
    {

        $respuesta = "";
        $datosCSVPaginados = $this->gestorFicheroCSV->getDatos();
        $Comprobador = new ComprobadorEstado($datosCSVPaginados["datos"], $this->equivalenciasDao);
        $respuesta = $Comprobador->getEstados();
        return $respuesta;
    }
}