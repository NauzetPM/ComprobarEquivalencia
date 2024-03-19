<?php
namespace ComprobadorEquivalencias\Infrastructure;

use ComprobadorEquivalencias\Domain\GestorEstablecimientos;
use PhpOffice\PhpSpreadsheet\IOFactory;

class GestorEstablecimientosODS implements GestorEstablecimientos
{
    private $odsFile;

    /**
     * __construct
     *
     * @param  mixed $odsFile
     * @return void
     */
    public function __construct(string $odsFile)
    {
        $this->odsFile = $odsFile;
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

        $objPHPExcel = IOFactory::load($this->odsFile);

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
