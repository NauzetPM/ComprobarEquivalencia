<?php
namespace ComprobadorEquivalencias\Domain;

use ComprobadorEquivalencias\Infrastructure\EquivalenciasDAOMysql;

class ComprobadorEstado
{
    private EquivalenciasDAOMysql $equivalenciaDAO;
    private $datosCSV;

    /**
     * __construct
     *
     * @param  array  $datosCSV
     * @param  EquivalenciasDAOMysql $estadoDAO
     * @return void
     */
    public function __construct(array $datosCSV, $estadoDAO)
    {
        $this->equivalenciaDAO = $estadoDAO;
        $this->datosCSV = $datosCSV;
    }
    /**
     * getEstados
     *
     * @return array
     */
    public function getEstados(): array
    {
        $datosJSON = array();
        $total = count($this->datosCSV);
        //Comprobar Mapeado y Mapeado Block
        for ($c = 0; $c < $total; $c++) {
            $dato = $this->equivalenciaDAO->comprobarEstado($this->datosCSV[$c][0]);
            if ($dato['total'] == 0) {
                $Estado = DatosHoteles::ESTADO_PENDIENTE;
                $Codigo = $this->datosCSV[$c][0];
                $Nombre = $this->datosCSV[$c][1];
            } elseif ($dato["codigo"] == $this->datosCSV[$c][0]) {
                $Codigo = $this->datosCSV[$c][0];
                $Nombre = $this->datosCSV[$c][1];
                if ($dato["usuario"] == "casamientoBlock") {
                    $Estado = DatosHoteles::ESTADO_BLOCK;
                } else {
                    $Estado = DatosHoteles::ESTADO_MAPEADO;
                }
            }
            $datosHotel = new DatosHoteles($Codigo, $Nombre, $Estado);
            $datosJSON[] = $datosHotel->asArray();
        }
        return $datosJSON;
    }

}