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
     * getDatos
     *
     * @return array
     */
    public function getDatos(): array
    {
        $datosCSV = array();
        $total = 0;
        if (($gestor = fopen($this->csvfile, "r")) !== FALSE) {
            while (($datos = fgetcsv($gestor, $this->lenght, ",")) !== FALSE) {
                $datosExpode = explode("|", $datos[0]);
                $datosCSV[] = $datosExpode;
                $total++;
            }
        }
        fclose($gestor);
        return [
            "datos" => $datosCSV,
            "total" => $total,
        ];
    }
}
