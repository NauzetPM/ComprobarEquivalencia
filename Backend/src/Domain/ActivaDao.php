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
     * @return ActivaDaoDatos
     */
    public function comprobarDescargadaActiva(string $codigo): ActivaDaoDatos;
}
