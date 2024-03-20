<?php
namespace ComprobadorEquivalencias\Infrastructure;

use ComprobadorEquivalencias\Domain\GestorEstablecimientos;
use PhpOffice\PhpSpreadsheet\IOFactory;

class GestorEstablecimientosODS implements GestorEstablecimientos
{
    private string $odsFilePath;

    /**
     * __construct
     *
     * @param  string $odsFilePath
     */
    public function __construct(string $odsFilePath)
    {
        $this->odsFilePath = $odsFilePath;
    }

    /**
     * getDatos
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

            $datosODS[] = $datosFila;
            $total++;
        }

        return [
            "datos" => $datosODS,
            "total" => $total,
        ];
    }
}
