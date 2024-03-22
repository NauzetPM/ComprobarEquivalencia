<?php

namespace ComprobadorEquivalencias\Infrastructure;

use ComprobadorEquivalencias\Domain\GestorEstablecimientos;
use PhpOffice\PhpSpreadsheet\IOFactory;
use ComprobadorEquivalencias\Domain\EstablecimientoMayorista;

class GestorEstablecimientosODS implements GestorEstablecimientos
{
    private string $odsFilePath;

    /**
     *
     * @param  string $odsFilePath
     */
    public function __construct(string $odsFilePath)
    {
        $this->odsFilePath = $odsFilePath;
    }

    /**
     *
     * @return array
     */
    public function getDatos(): array
    {
        $datosODS = array();
        $total = 0;

        $objPHPExcel = IOFactory::load($this->odsFilePath);

        $sheet = $objPHPExcel->getActiveSheet();

        foreach ($sheet->getRowIterator() as $row) {
            $datosFila = array();

            foreach ($row->getCellIterator() as $cell) {
                $datosFila[] = $cell->getValue();
            }

            $datosODS[] = EstablecimientoMayorista::fromArray($datosFila);
            $total++;
        }

        return [
            "datos" => $datosODS,
            "total" => $total,
        ];
    }
}
