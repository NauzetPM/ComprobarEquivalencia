<?php
namespace ComprobadorEquivalencias\Infrastructure;

use ComprobadorEquivalencias\Domain\GestorEstablecimientos;

class GestorEstablecimientosCSV implements GestorEstablecimientos
{
    private $csvfile;
    private $lenght = 1000;
    /**
     * __construct
     *
     * @param  string $csvFile
     * @return void
     */
    public function __construct(string $csvFile)
    {
        $this->csvfile = $csvFile;
    }

    /**
     * @param int $page
     * @param int $perPage
     * @return array
     */
    public function getDatosPaginados(int $page, int $perPage): array
    {
        $datosCSV = array();
        $startIndex = ($page - 1) * $perPage + 1;
        $endIndex = $startIndex + $perPage - 1;
        $currentIndex = 1;
        $total = 0;
        if (($gestor = fopen($this->csvfile, "r")) !== FALSE) {
            while (($datos = fgetcsv($gestor, $this->lenght, ",")) !== FALSE) {
                $datosExpode = explode("|", $datos[0]);
                if ($currentIndex >= $startIndex && $currentIndex <= $endIndex) {
                    $datosCSV[] = $datosExpode;
                }
                $currentIndex++;
            }
        }
        fclose($gestor);
        return [
            "datos" => $datosCSV,
            "total" => $total,
        ];
    }
    /**
     * getDatosByCodigo
     *
     * @param  string $codigo
     * @return array
     */
    public function getDatosByCodigo(string $codigo): array
    {
        $datosCSV = array();
        if (($gestor = fopen($this->csvfile, "r")) !== FALSE) {
            while (($datos = fgetcsv($gestor, $this->lenght, ",")) !== FALSE) {
                $datosExpode = explode("|", $datos[0]);
                if ($datosExpode[0] == $codigo) {
                    $datosCSV[] = $datosExpode;
                }
            }
        }
        fclose($gestor);

        return $datosCSV;
    }
    /**
     * getDatosByNombrePaginados
     *
     * @param  string $nombre
     * @param  int $page
     * @param  int $perPage
     * @return array
     */
    public function getDatosByNombrePaginados(string $nombre,int $page,int $perPage): array
    {
        $datosCSV = array();
        $startIndex = ($page - 1) * $perPage + 1;
        $endIndex = $startIndex + $perPage - 1;
        $currentIndex = 1;
        $total = 0;
        if (($gestor = fopen($this->csvfile, "r")) !== FALSE) {
            while (($datos = fgetcsv($gestor, $this->lenght, ",")) !== FALSE) {
                $datosExpode = explode("|", $datos[0]);

                if (stripos($datosExpode[1], $nombre) !== false) {
                    if ($currentIndex >= $startIndex && $currentIndex <= $endIndex) {
                        $datosCSV[] = $datosExpode;
                    }
                    $currentIndex++;
                    $total++;
                }
            }
        }
        fclose($gestor);

        return [
            "datos" => $datosCSV,
            "total" => $total,
        ];
    }

}
