<?php
namespace ComprobadorEquivalencias\Domain;

use ComprobadorEquivalencias\Infrastructure\EquivalenciasDAOMysql;

class ComprobadorEstado
{
    private EquivalenciasDAOMysql $EquivalenciaDAO;
    private $datosCSV;

    /**
     * __construct
     *
     * @param  mixed $datosCSV
     * @param  mixed $EstadoDAO
     * @return void
     */
    public function __construct(array $datosCSV, $EstadoDAO)
    {
        $this->EquivalenciaDAO = $EstadoDAO;
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
            $dato = $this->EquivalenciaDAO->comprobarEstado($this->datosCSV[$c][0]);
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
            //var_dump($datosHotel);
            $datosJSON[] = $datosHotel->asArray();
        }
        //var_dump($datosJSON);
        return $datosJSON;
    }

}