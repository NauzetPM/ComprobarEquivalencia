<?php

declare(strict_types=1);

namespace ComprobadorEquivalencias\Domain;

interface GestorSelector
{

    /**
     *
     * @param  string $nombre
     * @return array
     */
    public function obtenerCorrespondencias(string $nombre): array;
}
