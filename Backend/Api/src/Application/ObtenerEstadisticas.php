<?php
namespace ComprobadorEquivalencias\Application;

use ComprobadorEquivalencias\Domain\GestorEstadisticas;

class ObtenerEstadisticas
{
    private GestorEstadisticas $gestorEstadisticas;

    /**
     * __construct
     *
     * @return void
     */
    public function __construct(
        GestorEstadisticas $gestorEstadisticas
    ) {
        $this->gestorEstadisticas = $gestorEstadisticas;
    }
    /**
     * @return array
     */
    public function __invoke(): array
    {
        return $this->gestorEstadisticas->getEstadisticas();

    }
}