<?php

declare(strict_types=1);

namespace ComprobadorEquivalencias\Domain;

class ComprobadorEstado
{
    private EquivalenciasDAO $equivalenciaDAO;
    private array $datosArchivo;

    private ActivaDao $activaDao;
    private int $total;

    /**
     *
     * @param EstablecimientoMayorista[] $datosArchivo
     * @param EquivalenciasDAO $estadoDAO
     * @param ActivaDao $activa
     * @param int $total
     */
    public function __construct(
        array $datosArchivo,
        EquivalenciasDAO $estadoDAO,
        ActivaDao $activa,
        int $total
    ) {
        $this->equivalenciaDAO = $estadoDAO;
        $this->datosArchivo = $datosArchivo;
        $this->activaDao = $activa;
        $this->total = $total;
    }

    /**
     *
     * @return array
     */
    public function getEstados(): array
    {
        $datos = array();
        $estadisticas = [
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

        foreach ($this->datosArchivo as $nEstablecimientoComprobar) {

            $codigo = $nEstablecimientoComprobar->getCodigo();
            $nombre = $nEstablecimientoComprobar->getNombre();

            $datoDescargadaActiva = $this->activaDao->comprobarDescargadaActiva($codigo);
            $estado = "";

            $estaActivo = $datoDescargadaActiva->getactiva();

            if (!$datoDescargadaActiva->getesDescargada()) {
                $estadisticas['noDescargados']++;
                $estado = DatosHoteles::NO_DESCARGADA;
            } else {

                // DESCARCAGOS
                $datoEstado = $this->equivalenciaDAO->comprobarEstado($codigo);
                $totalMapeos = $datoEstado['total'];
                $usuarioMapeo = "";

                if ($totalMapeos > 0) {
                    $usuarioMapeo = $datoEstado["usuario"];
                }

                if ($estaActivo) {
                    $estadisticas['activaTotal']++;
                } else {
                    $estadisticas['noActivaTotal']++;
                }
                $estadoEstablecimiento = new EstadoEstablecimiento($totalMapeos, $estaActivo, $usuarioMapeo);
                $estadoActivo = $estadoEstablecimiento->obtenerEstado();

                if (!isset($estadisticas[$estadoActivo])) {
                    throw new \InvalidArgumentException("Estado no registrado");
                }
                $estadisticas[$estadoActivo]++;
                $estado = $estadoEstablecimiento->obtenerTipoEquivalencia();
            }
            $datosHotel = new DatosHoteles($codigo, $nombre, $estado, $estaActivo);

            $datos[] = $datosHotel->asArray();
        }
        $estadisticas['total'] = $this->total;
        $estadisticas['pendientes'] = $estadisticas['activaPendiente'] + $estadisticas['noActivaPendiente'];
        $estadisticas['mapeados'] = $estadisticas['activaMapeado'] + $estadisticas['noActivaMapeado'];
        $estadisticas['mapeadosBlock'] = $estadisticas['activaBlock'] + $estadisticas['noActivaBlock'];


        return [
            'estadisticas' => $estadisticas,
            'datos' => $datos
        ];
    }
}
