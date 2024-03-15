<?php
namespace ComprobadorEquivalencias\Infrastructure;

use ComprobadorEquivalencias\Domain\GestorEstablecimientos;
use PhpOffice\PhpSpreadsheet\IOFactory;

class GestorEstablecimientosODS implements GestorEstablecimientos
{
    private $odsFile;

    public function __construct(string $odsFile)
    {
        $this->odsFile = $odsFile;
    }

    public function getDatos(): array
    {
        $datosODS = array();
        $total = 0;

        $objPHPExcel = IOFactory::load($this->odsFile);

        $sheet = $objPHPExcel->getActiveSheet();

        // Itera sobre las filas de la hoja
        foreach ($sheet->getRowIterator() as $row) {
            $datosFila = array();

            foreach ($row->getCellIterator() as $cell) {
                $datosFila[] = $cell->getValue();
            }

            $datosODS[] = $datosFila;
            $total++;
        }

        return [
            "datos" => $datosODS,
            "total" => $total,
        ];
    }
}
