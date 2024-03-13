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
        int $pagina,
        int $registrosPorPagina,
        GestorEstablecimientos $gestorFicheroCSV,
        EquivalenciasDAO $equivalenciasDAO,
        array $filtros = array()
    ) {
        $this->gestorFicheroCSV = $gestorFicheroCSV;
        $this->equivalenciasDao = $equivalenciasDAO;
        $this->pagina = $pagina;
        $this->registrosPorPagina = $registrosPorPagina;
        $this->filtros = $filtros;
    }


    /**
     *
     * @return array
     */
    public function __invoke(): array
    {

        $respuesta = "";
        if (isset($this->filtros['nombre'])) {
            $datosCSVPaginados = $this->gestorFicheroCSV->getDatosByNombrePaginados($this->filtros['nombre'], $this->pagina, $this->registrosPorPagina);
            $Comprobador = new ComprobadorEstado($datosCSVPaginados["datos"], $this->equivalenciasDao);
            $respuesta = [
                "datos" => $Comprobador->getEstados(),
                "totalBusquedas" => $datosCSVPaginados["total"]
            ];
        } elseif (isset($this->filtros['codigo'])) {
            $datosCSVPaginados = $this->gestorFicheroCSV->getDatosByCodigo($this->filtros['codigo']);

            $Comprobador = new ComprobadorEstado($datosCSVPaginados, $this->equivalenciasDao);
            $respuesta = $Comprobador->getEstados();
        } else {
            $datosCSVPaginados = $this->gestorFicheroCSV->getDatosPaginados($this->pagina, $this->registrosPorPagina);
            $Comprobador = new ComprobadorEstado($datosCSVPaginados["datos"], $this->equivalenciasDao);
            $respuesta = $Comprobador->getEstados();
        }
        return $respuesta;
    }
}