<?php
namespace ComprobadorEquivalencias\Domain;
use Seld\JsonLint\Undefined;

class ComprobadorEstado
{
    private EquivalenciasDAO $equivalenciaDAO;
    private array $datosArchivo;

    private ActivaDao $activaDao;

    /**
     * __construct
     *
     * @param  array  $datosArchivo
     * @param  EquivalenciasDAO $estadoDAO
     * @param  ActivaDao $activa
     */
    public function __construct(
        array $datosArchivo,
        EquivalenciasDAO $estadoDAO,
        ActivaDao $activa
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
            $Estado="";
            $estaDescargado = $datoActiva['total'] > 0;
            $estaActivo = $datoActiva['activo'] == 1;


            if ($datoActiva['total'] == 0) {
                $noDescargados++;
                $Activa = DatosHoteles::NO_DESCARGADA;
                $datosHotel = new DatosHoteles($Codigo, $Nombre, $Estado, $Activa);
            } else {
                $datoEstado = $this->equivalenciaDAO->comprobarEstado($Codigo);
                $totalMapeos = $datoEstado['total'];
                $usuarioMapeo="";
                //primer if por que si es 0 el $datoEstado["codigo"] no existe es pendiente 
                //y el segundo if por que si saco el usuario del caso pendiente que no esta peta
                if($totalMapeos>0){
                if ($datoEstado["codigo"] == $this->datosArchivo[$c][0]) {
                    $usuarioMapeo = $datoEstado["usuario"];
                }
                }
                $estadoEstablecimiento=new EstadoEstablecimiento($totalMapeos,$estaActivo,$usuarioMapeo);
                $estadoActivo=$estadoEstablecimiento->obtenerEstado();
                if($estadoActivo==DatosHoteles::ESTADO_MAPEADO_NO_ACTIVO){
                    $mapeados++;
                    $noActivaMapeado++;
                    $noActivaTotal++;
                    $Estado=DatosHoteles::ESTADO_MAPEADO;
                    $Activa=DatosHoteles::ACTIVA;
                }
                if($estadoActivo==DatosHoteles::ESTADO_MAPEADO_ACTIVO){
                    $mapeados++;
                    $activaMapeado++;
                    $activaTotal++;
                    $Estado=DatosHoteles::ESTADO_MAPEADO;
                    $Activa=DatosHoteles::NO_ACTIVA;
                }
                if($estadoActivo== DatosHoteles::ESTADO_MAPEADO_BLOCK_ACTIVO){
                    $mapeadosBlock++;
                    $activaBlock++;
                    $activaTotal++;
                    $Estado=DatosHoteles::ESTADO_BLOCK;
                    $Activa=DatosHoteles::ACTIVA;
                }
                if($estadoActivo== DatosHoteles::ESTADO_MAPEADO_BLOCK_NO_ACTIVO){
                    $mapeadosBlock++;
                    $noActivaBlock++;
                    $noActivaTotal++;
                    $Estado=DatosHoteles::ESTADO_BLOCK;
                    $Activa=DatosHoteles::NO_ACTIVA;
                }
                if($estadoActivo==DatosHoteles::ESTADO_PENDIENTE_ACTIVO){
                    $pendientes++;
                    $activaPendiente++;
                    $activaTotal++;
                    $Estado=DatosHoteles::ESTADO_PENDIENTE;
                    $Activa=DatosHoteles::ACTIVA;
                }
                if($estadoActivo==DatosHoteles::ESTADO_PENDIENTE_NO_ACTIVO){
                    $pendientes++;
                    $noActivaPendiente++;
                    $noActivaTotal++;
                    $Estado=DatosHoteles::ESTADO_PENDIENTE;
                    $Activa=DatosHoteles::NO_ACTIVA;
                }
                $datosHotel = new DatosHoteles($Codigo, $Nombre, $Estado, $Activa);
            }
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
