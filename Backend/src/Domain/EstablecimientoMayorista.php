<?php

declare(strict_types=1);


namespace ComprobadorEquivalencias\Domain;


class EstablecimientoMayorista
{

    private string $codigo;
    private string $nombre;

    /**
     * @param string $codigo
     * @param string $nombre
     */
    public function __construct(string $codigo, string $nombre)
    {

        $this->codigo = $codigo;
        $this->nombre = $nombre;
    }

    /**
     * @param array $datos
     * 
     * @return EstablecimientoMayorista
     */
    public static function fromArray(array $datos): EstablecimientoMayorista
    {
        $codigo = $datos[0] . "";
        $nombre = $datos[1];
        return new EstablecimientoMayorista($codigo, $nombre);
    }

    /**
     * @return string
     */
    public function getCodigo(): string
    {
        return $this->codigo;
    }

    /**
     * @return string
     */
    public function getNombre(): string
    {
        return $this->nombre;
    }
}
