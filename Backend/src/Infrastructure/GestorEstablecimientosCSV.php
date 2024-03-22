<?php

namespace ComprobadorEquivalencias\Infrastructure;

use ComprobadorEquivalencias\Domain\EstablecimientoMayorista;
use ComprobadorEquivalencias\Domain\GestorEstablecimientos;

class GestorEstablecimientosCSV implements GestorEstablecimientos
{
    private string $csvfilePath;
    private int $lenght = 1000;
    /**
     *
     * @param  string $csvfilePath
     */
    public function __construct(string $csvfilePath)
    {
        $this->csvfilePath = $csvfilePath;
    }


    /**
     *
     * @return array
     */
    public function getDatos(): array
    {
        $datosCSV = array();
        $total = 0;
        if (($gestor = fopen($this->csvfilePath, "r")) !== FALSE) {
            while (($datos = fgetcsv($gestor, $this->lenght, ",")) !== FALSE) {
                $datosExpode = explode("|", $datos[0]);
                $datosCSV[] = EstablecimientoMayorista::fromArray($datosExpode);
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
