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
        $datos=array();
        $estadisticas = [
            'total' => 0,
            'pendientes' => 0,
            'mapeados' => 0,
            'mapeadosBlock' => 0,
            'noDescargados' => 0,
            'activaTotal' => 0,
            'activaPendiente' => 0,
            'activaMapeado' => 0,
            'activaBlock' => 0,
            'noActivaTotal' => 0,
            'noActivaMapeado' => 0,
            'noActivaBlock' => 0,
            'noActivaPendiente' => 0
        ];
    
        $total = count($this->datosArchivo);
    
        for ($c = 0; $c < $total; $c++) {
            $datoActiva = $this->activaDao->comprobarActiva($this->datosArchivo[$c][0]);
            $codigo = $this->datosArchivo[$c][0];
            $nombre = $this->datosArchivo[$c][1];
            $estado = "";
            $estaDescargado = $datoActiva['total'] > 0;
            $estaActivo = $datoActiva['activo'] == 1;
    
            if ($datoActiva['total'] == 0) {
                $estadisticas['noDescargados']++;
                $estado = DatosHoteles::NO_DESCARGADA;
                $datosHotel = new DatosHoteles($codigo, $nombre, $estado, DatosHoteles::NO_ACTIVA);
            } else {
                $datoEstado = $this->equivalenciaDAO->comprobarEstado($codigo);
                $totalMapeos = $datoEstado['total'];
                $usuarioMapeo = "";
    
                if ($totalMapeos > 0) {
                    if ($datoEstado["codigo"] == $codigo) {
                        $usuarioMapeo = $datoEstado["usuario"];
                    }
                }
    
                $estadoEstablecimiento = new EstadoEstablecimiento($totalMapeos, $estaActivo, $usuarioMapeo);
                $estadoActivo = $estadoEstablecimiento->obtenerEstado();
    
                if ($estadoActivo == DatosHoteles::ESTADO_MAPEADO_NO_ACTIVO) {
                    $estadisticas['mapeados']++;
                    $estadisticas['noActivaMapeado']++;
                    $estadisticas['noActivaTotal']++;
                    $estado = DatosHoteles::ESTADO_MAPEADO;
                    $activa = DatosHoteles::ACTIVA;
                }
                if($estadoActivo==DatosHoteles::ESTADO_MAPEADO_ACTIVO){
                    $estadisticas['mapeados']++;
                    $estadisticas['activaMapeado']++;
                    $estadisticas['activaTotal']++;
                    $estado=DatosHoteles::ESTADO_MAPEADO;
                    $activa=DatosHoteles::NO_ACTIVA;
                }
                if($estadoActivo== DatosHoteles::ESTADO_MAPEADO_BLOCK_ACTIVO){
                    $estadisticas['mapeadosBlock']++;
                    $estadisticas['activaBlock']++;
                    $estadisticas['activaTotal']++;
                    $estado=DatosHoteles::ESTADO_BLOCK;
                    $activa=DatosHoteles::ACTIVA;
                }
                if($estadoActivo== DatosHoteles::ESTADO_MAPEADO_BLOCK_NO_ACTIVO){
                    $estadisticas['mapeadosBlock']++;
                    $estadisticas['noActivaBlock']++;
                    $estadisticas['noActivaTotal']++;
                    $estado=DatosHoteles::ESTADO_BLOCK;
                    $activa=DatosHoteles::NO_ACTIVA;
                }
                if($estadoActivo==DatosHoteles::ESTADO_PENDIENTE_ACTIVO){
                    $estadisticas['pendientes']++;
                    $estadisticas['activaPendiente']++;
                    $estadisticas['activaTotal']++;
                    $estado=DatosHoteles::ESTADO_PENDIENTE;
                    $activa=DatosHoteles::ACTIVA;
                }
                if($estadoActivo==DatosHoteles::ESTADO_PENDIENTE_NO_ACTIVO){
                    $estadisticas['pendientes']++;
                    $estadisticas['noActivaPendiente']++;
                    $estadisticas['noActivaTotal']++;
                    $estado=DatosHoteles::ESTADO_PENDIENTE;
                    $activa=DatosHoteles::NO_ACTIVA;
                }
    
                $datosHotel = new DatosHoteles($codigo, $nombre, $estado, $activa);
            }
    
            $datos[] = $datosHotel->asArray();
        }
    
        $estadisticas['total'] = $total;
        return [
            'estadisticas' => $estadisticas,
            'datos' => $datos
        ];
    }

}
