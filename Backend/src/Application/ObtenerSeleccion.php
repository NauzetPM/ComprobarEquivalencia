<?php
namespace ComprobadorEquivalencias\Application;
use ComprobadorEquivalencias\Domain\GestorSelector;

class ObtenerSeleccion
{
    private $gestorSelector;
    public function __construct(GestorSelector $gestorSelector){
        $this->gestorSelector=$gestorSelector;
    }
    public function __invoke(string $nombreEmpresa){
        $parametrosBBDD = $this->gestorSelector->obtenerCorrespondencias($nombreEmpresa);
        return $parametrosBBDD;
    }
}