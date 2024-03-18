<?php

namespace ComprobadorEquivalencias\Domain;

interface ActivaDao
{
    /**
     * @return array
     */
    public function getAll(): array;

    /**
     * @param string $codigo
     * @return array
     */
    public function comprobarActiva(string $codigo): array;
}