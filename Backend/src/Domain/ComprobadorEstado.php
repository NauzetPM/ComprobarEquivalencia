<?php
namespace ComprobadorEquivalencias\Domain;

class ComprobadorEstado
{
    private EquivalenciasDAO $equivalenciaDAO;
    private $datosArchivo;

    private ActivaDao $activaDao;

    /**
     * __construct
     *
     * @param  array  $datosArchivo
     * @param  EquivalenciasDAO $estadoDAO
     * @param ActivaDao $activa
     * @return void
     */
    public function __construct(
        array $datosArchivo,
        $estadoDAO,
        $activa
    ) {
        $this->equivalenciaDAO = $estadoDAO;
        $this->datosArchivo = $datosArchivo;
        $this->activaDao = $activa;
    }
    /**
     * getEstados
     *
     * @return array
     */
    public function getEstados(): array
    {
        $datos = array();
        $total = count($this->datosArchivo);
        $pendientes = 0;
        $mapeados = 0;
        $mapeadosBlock = 0;
        $noDescargados = 0;

        $activaTotal = 0;
        $activaPendiente = 0;
        $activaMapeado = 0;
        $activaBlock = 0;

        $noActivaTotal = 0;
        $noActivaMapeado = 0;
        $noActivaBlock = 0;
        $noActivaPendiente = 0;

        $Estado = "";
        for ($c = 0; $c < $total; $c++) {
            $datoActiva = $this->activaDao->comprobarActiva($this->datosArchivo[$c][0]);
            $Codigo = $this->datosArchivo[$c][0];
            $Nombre = $this->datosArchivo[$c][1];
            if ($datoActiva['total'] == 0) {
                $noDescargados++;
                $Activa = DatosHoteles::No_Descargada;
            } else {
                $datoEstado = $this->equivalenciaDAO->comprobarEstado($this->datosArchivo[$c][0]);
                if ($datoActiva['activo'] == 1) {
                    $Activa = DatosHoteles::Activa;
                    $activaTotal++;
                } else {
                    $Activa = DatosHoteles::No_Aciva;
                    $noActivaTotal++;
                }
                if ($datoEstado['total'] == 0) {
                    $pendientes++;
                    $Estado = DatosHoteles::ESTADO_PENDIENTE;
                    if ($datoActiva['activo'] == 1) {
                        $activaPendiente++;
                    } else {
                        $noActivaPendiente++;
                    }
                } elseif ($datoEstado["codigo"] == $this->datosArchivo[$c][0]) {
                    if ($datoEstado["usuario"] == "casamientoBlock") {
                        $Estado = DatosHoteles::ESTADO_BLOCK;
                        $mapeadosBlock++;
                        if ($datoActiva['activo'] == 1) {
                            $activaBlock++;
                        } else {
                            $noActivaBlock++;
                        }
                    } else {
                        if ($datoActiva['activo'] == 1) {
                            $activaMapeado++;
                        } else {
                            $noActivaMapeado++;
                        }
                        $Estado = DatosHoteles::ESTADO_MAPEADO;
                        $mapeados++;
                    }
                }
            }
            $datosHotel = new DatosHoteles($Codigo, $Nombre, $Estado, $Activa);
            $datos[] = $datosHotel->asArray();
        }
        return [
            'total' => $total,
            'mapeado' => $mapeados,
            'block' => $mapeadosBlock,
            'pendiente' => $pendientes,
            'no descargado' => $noDescargados,

            'activa total' => $activaTotal,
            'activa pendiente' => $activaPendiente,
            'activa mapeado' => $activaMapeado,
            'activa block' => $activaBlock,

            'no activa total' => $noActivaTotal,
            'no activa pendiente' => $noActivaPendiente,
            'no activa mapeado' => $noActivaMapeado,
            'no activa block' => $noActivaBlock,

            'datos' => $datos
        ];
    }

}
