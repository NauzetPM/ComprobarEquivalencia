<?php
namespace ComprobadorEquivalencias\Domain;

use ComprobadorEquivalencias\Infrastructure\EquivalenciasDAOMysql;

class ComprobadorEstado
{
    private EquivalenciasDAO $equivalenciaDAO;
    private $datosCSV;

    private ActivaDao $activaDao;

    /**
     * __construct
     *
     * @param  array  $datosCSV
     * @param  EquivalenciasDAO $estadoDAO
     * @param ActivaDao $activa
     * @return void
     */
    public function __construct(array $datosCSV, $estadoDAO, $activa)
    {
        $this->equivalenciaDAO = $estadoDAO;
        $this->datosCSV = $datosCSV;
        $this->activaDao = $activa;
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
        $pendientes = 0;
        $mapeados = 0;
        $mapeadosBlock = 0;
        $noDescargados = 0;
        for ($c = 0; $c < $total; $c++) {
            $datoActiva = $this->activaDao->comprobarActiva($this->datosCSV[$c][0]);
            $Codigo = $this->datosCSV[$c][0];
            $Nombre = $this->datosCSV[$c][1];
            if ($datoActiva['total'] == 0) {
                $noDescargados++;
                //$Codigo = $this->datosCSV[$c][0];
                //$Nombre = $this->datosCSV[$c][1];
                $Activa = DatosHoteles::No_Descargada;
                $Estado="";
            } else {
                $datoEstado = $this->equivalenciaDAO->comprobarEstado($this->datosCSV[$c][0]);
                if($datoActiva['activo']==1){
                    $Activa=DatosHoteles::Activa;
                }else{
                    $Activa=DatosHoteles::No_Aciva;
                }
                if ($datoEstado['total'] == 0) {
                    $pendientes++;
                    $Estado = DatosHoteles::ESTADO_PENDIENTE;
                    //$Codigo = $this->datosCSV[$c][0];
                    //$Nombre = $this->datosCSV[$c][1];
                } elseif ($datoEstado["codigo"] == $this->datosCSV[$c][0]) {
                    //$Codigo = $this->datosCSV[$c][0];
                    //$Nombre = $this->datosCSV[$c][1];
                    if ($datoEstado["usuario"] == "casamientoBlock") {
                        $Estado = DatosHoteles::ESTADO_BLOCK;
                        $mapeadosBlock++;
                    } else {
                        $Estado = DatosHoteles::ESTADO_MAPEADO;
                        $mapeados++;
                    }
                }
            }
            $datosHotel = new DatosHoteles($Codigo, $Nombre, $Estado,$Activa);
            $datosJSON[] = $datosHotel->asArray();
        }
        return [
            'total' => $total,
            'mapeado' => $mapeados,
            'block' => $mapeadosBlock,
            'pendiente' => $pendientes,
            'no descargado' => $noDescargados,
            'datos' => $datosJSON
        ];
    }

}