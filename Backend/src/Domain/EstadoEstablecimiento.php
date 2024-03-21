<?php declare(strict_types=1);


namespace ComprobadorEquivalencias\Domain;


class EstadoEstablecimiento
{
    private int $totalMapeos;
    private bool $estaActivo;
    private string $usuarioMapeo;

    /**
     * @param int $totalMapeos
     * @param bool $estaDescargado
     * @param bool $estaActivo
     * @param string $usuarioMapeo
     */
    public function __construct(int $totalMapeos, bool $estaActivo, string $usuarioMapeo)
    {
        $this->totalMapeos = $totalMapeos;
        $this->estaActivo = $estaActivo;
        $this->usuarioMapeo = $usuarioMapeo;
    }

    public function obtenerEstado()
    {

        if ($this->totalMapeos > 0) {
            //Mapeado o Mapeado Block
            if (!$this->estaActivo && $this->usuarioMapeo !== 'casamientoBlock') {
                //Mapeado No Activo
                return DatosHoteles::ESTADO_MAPEADO_NO_ACTIVO;
            }
            if ($this->estaActivo && $this->usuarioMapeo !== 'casamientoBlock') {
                //Mapeado Activo
                return DatosHoteles::ESTADO_MAPEADO_ACTIVO;
            }
            if ($this->estaActivo && $this->usuarioMapeo === 'casamientoBlock') {
                //Mapeado Block Activo
                return DatosHoteles::ESTADO_MAPEADO_BLOCK_ACTIVO;
            }
            //Mapeado Block No Activo
            return DatosHoteles::ESTADO_MAPEADO_BLOCK_NO_ACTIVO;
        }
        if ($this->estaActivo) {
            //Pendiente activo
            return DatosHoteles::ESTADO_PENDIENTE_ACTIVO;
        }

        return DatosHoteles::ESTADO_PENDIENTE_NO_ACTIVO;
    }

    public function getTotalMapeos(): int
    {
        return $this->totalMapeos;
    }

    public function isEstaActivo(): bool
    {
        return $this->estaActivo;
    }

    public function getUsuarioMapeo(): string
    {
        return $this->usuarioMapeo;
    }


}
