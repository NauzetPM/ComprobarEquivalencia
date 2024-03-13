<?php declare(strict_types=1);

namespace ComprobadorEquivalencias\Application;

use ComprobadorEquivalencias\Domain\ComprobadorEstado;
use ComprobadorEquivalencias\Domain\EquivalenciasDAO;
use ComprobadorEquivalencias\Domain\GestorEstablecimientos;

class ObtenerEstadoEquivalencias
{
    private GestorEstablecimientos $GestorFicheroCSV;
    private EquivalenciasDAO $EquivalenciasDao;
    private int $pagina;
    private int $registrosPorPagina;
    private array $filtros;

    /**
     * @param int $pagina
     * @param int $registrosPorPagina
     * @param GestorEstablecimientos $gestorFicheroCSV
     * @param EquivalenciasDAO $EquivalenciasDao
     * @param array $filtros
     */
    public function __construct(
        int $pagina,
        int $registrosPorPagina,
        GestorEstablecimientos $gestorFicheroCSV,
        EquivalenciasDAO $equivalenciasDAO,
        array $filtros = array()
    ) {
        $this->GestorFicheroCSV = $gestorFicheroCSV;
        $this->EquivalenciasDao = $equivalenciasDAO;
        $this->pagina = $pagina;
        $this->registrosPorPagina = $registrosPorPagina;
        $this->filtros = $filtros;
    }


    /**
     * __invoke
     *
     * @return array
     */
    public function __invoke(): array
    {

        $Respuesta = "";
        if (isset($this->filtros['nombre'])) {
            $datosCSVPaginados = $this->GestorFicheroCSV->getDatosByNombrePaginados($this->filtros['nombre'], $this->pagina, $this->registrosPorPagina);
            $Comprobador = new ComprobadorEstado($datosCSVPaginados["datos"], $this->EquivalenciasDao);
            $Respuesta = [
                "datos" => $Comprobador->getEstados(),
                "totalBusquedas" => $datosCSVPaginados["total"]
            ];
        } elseif (isset($this->filtros['codigo'])) {
            $datosCSVPaginados = $this->GestorFicheroCSV->getDatosByCodigo($this->filtros['codigo']);

            $Comprobador = new ComprobadorEstado($datosCSVPaginados, $this->EquivalenciasDao);
            $Respuesta = $Comprobador->getEstados();
        } else {
            $datosCSVPaginados = $this->GestorFicheroCSV->getDatosPaginados($this->pagina, $this->registrosPorPagina);
            $Comprobador = new ComprobadorEstado($datosCSVPaginados["datos"], $this->EquivalenciasDao);
            $Respuesta = $Comprobador->getEstados();
        }
        return $Respuesta;
    }
}