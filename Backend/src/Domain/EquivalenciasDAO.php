<?php

namespace ComprobadorEquivalencias\Domain;

interface EquivalenciasDAO
{
    /**
     * @return array
     */
    public function getAll(): array;

    /**
     * @param string $codigo
     * @return array
     */
    public function comprobarEstado(string $codigo): array;
}