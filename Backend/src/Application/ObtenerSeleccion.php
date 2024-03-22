<?php

declare(strict_types=1);

namespace ComprobadorEquivalencias\Application;

use ComprobadorEquivalencias\Domain\GestorSelector;

class ObtenerSeleccion
{
    private GestorSelector $gestorSelector;
    private string $nombreEmpresa;
    /**
     *
     * @param  GestorSelector $gestorSelector
     * @param  string $nombreEmpresa
     */
    public function __construct(
        GestorSelector $gestorSelector,
        string $nombreEmpresa
    ) {
        $this->gestorSelector = $gestorSelector;
        $this->nombreEmpresa = $nombreEmpresa;
    }
    /**
     *
     * @return array
     */
    public function __invoke(): array
    {
        $parametrosBBDD = $this->gestorSelector->obtenerCorrespondencias($this->nombreEmpresa);
        return $parametrosBBDD;
    }
}
