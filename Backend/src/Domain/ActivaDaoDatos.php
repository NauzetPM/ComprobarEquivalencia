<?php

declare(strict_types=1);

namespace ComprobadorEquivalencias\Domain;

class ActivaDaoDatos
{
    private bool $esDescargada;
    private bool $activa;
    /**
     * @param bool $esDescargada
     * @param bool $activa
     */
    public function __construct(bool $esDescargada, bool $activa)
    {
        $this->esDescargada = $esDescargada;
        $this->activa = $activa;
    }

    /**
     * @return bool
     */
    public function getesDescargada(): bool
    {
        return $this->esDescargada;
    }

    /**
     * @return bool
     */
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
