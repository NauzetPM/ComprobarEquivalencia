<?php
namespace ComprobadorEquivalencias\Domain;

interface GestorSelector
{

    /**
     * obtenerCorrespondencias
     *
     * @param  string $nombre
     * @return array
     */
    public function obtenerCorrespondencias(string $nombre): array;
}