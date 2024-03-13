<?php declare(strict_types=1);

namespace ComprobadorEquivalencias\Domain;

interface GestorEstablecimientos
{
    /**
     * getDatosPaginados
     *
     * @param  int $page
     * @param  int $perPage
     * @return array
     */
    public function getDatosPaginados(int $page, int $perPage): array;
    /**
     * getDatosByNombrePaginados
     *
     * @param  string $nombre
     * @param  int $page
     * @param  int $perPage
     * @return array
     */
    public function getDatosByNombrePaginados(string $nombre, int $page, int $perPage): array;
    /**
     * getDatosByCodigo
     *
     * @param  string $codigo
     * @return array
     */
    public function getDatosByCodigo(string $codigo): array;
}