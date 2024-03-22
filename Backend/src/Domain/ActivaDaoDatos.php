<?php

namespace ComprobadorEquivalencias\Domain;

class ActivaDaoDatos
{
    private bool $esDescargada;
    private bool $activa;
    public function __construct(bool $esDescargada, bool $activa)
    {
        $this->esDescargada = $esDescargada;
        $this->activa = $activa;
    }

    public function getesDescargada(): bool
    {
        return $this->esDescargada;
    }

    public function getactiva(): bool
    {
        return $this->activa;
    }

    /**
     * @param array $datos
     * 
     * @return ActivaDaoDatos
     */
    public static function fromArray(array $datos): ActivaDaoDatos
    {
        if ($datos['total'] == 0) {
            $esDescargada = false;
            $activa = false;
        } else {
            $esDescargada = true;
            if ($datos['activo'] == 1) {
                $activa = true;
            } else {
                $activa = false;
            }
        }
        return new ActivaDaoDatos($esDescargada, $activa);
    }
}
