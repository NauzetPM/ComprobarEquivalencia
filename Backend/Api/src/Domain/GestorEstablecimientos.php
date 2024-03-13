<?php declare(strict_types=1);

namespace ComprobadorEquivalencias\Domain;

interface GestorEstablecimientos
{
    /**
     * getDatosPaginados
     *
     * @param  mixed $page
     * @param  mixed $perPage
     * @return array
     */
    public function getDatosPaginados(int $page, int $perPage): array;
    /**
     * getDatosByNombrePaginados
     *
     * @param  mixed $nombre
     * @param  mixed $page
     * @param  mixed $perPage
     * @return array
     */
    public function getDatosByNombrePaginados(string $nombre, int $page, int $perPage): array;
    /**
     * getDatosByCodigo
     *
     * @param  mixed $codigo
     * @return array
     */
    public function getDatosByCodigo(string $codigo): array;
}