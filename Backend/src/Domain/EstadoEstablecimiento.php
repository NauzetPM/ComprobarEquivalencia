<?php

declare(strict_types=1);


namespace ComprobadorEquivalencias\Domain;


class EstadoEstablecimiento
{
    private int $totalMapeos;
    private bool $estaActivo;
    private string $usuarioMapeo;

    private string $tipoEquivalencia;

    /**
     * @param int $totalMapeos
     * @param bool $estaActivo
     * @param string $usuarioMapeo
     */
    public function __construct(int $totalMapeos, bool $estaActivo, string $usuarioMapeo)
    {
        $this->totalMapeos = $totalMapeos;
        $this->estaActivo = $estaActivo;
        $this->usuarioMapeo = $usuarioMapeo;
    }

    /**
     * @return string
     */
    public function obtenerEstado(): string
    {
        if ($this->totalMapeos > 0) {
            $esMapeadoBlock = $this->usuarioMapeo === DatosHoteles::USUARIO_MAPEADO_BLOCK;
            if ($esMapeadoBlock) {
                $this->tipoEquivalencia = DatosHoteles::ESTADO_BLOCK;
            } else {
                $this->tipoEquivalencia = DatosHoteles::ESTADO_MAPEADO;
            }
            if (!$this->estaActivo) {
                return ($esMapeadoBlock) ? DatosHoteles::ESTADO_MAPEADO_BLOCK_NO_ACTIVO : DatosHoteles::ESTADO_MAPEADO_NO_ACTIVO;
            }
            return ($esMapeadoBlock) ? DatosHoteles::ESTADO_MAPEADO_BLOCK_ACTIVO : DatosHoteles::ESTADO_MAPEADO_ACTIVO;
        }
        $this->tipoEquivalencia = DatosHoteles::ESTADO_PENDIENTE;
        return ($this->estaActivo) ? DatosHoteles::ESTADO_PENDIENTE_ACTIVO : DatosHoteles::ESTADO_PENDIENTE_NO_ACTIVO;
    }

    /**
     * @return string
     */
    public function obtenerTipoEquivalencia(): string
    {
        return $this->tipoEquivalencia;
    }
}
