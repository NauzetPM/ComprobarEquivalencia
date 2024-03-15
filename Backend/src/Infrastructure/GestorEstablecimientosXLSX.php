<?php

namespace ComprobadorEquivalencias\Infrastructure;

use ComprobadorEquivalencias\Domain\GestorEstablecimientos;
use PhpOffice\PhpSpreadsheet\IOFactory;

class GestorEstablecimientosXLSX implements GestorEstablecimientos
{
    private $xlsxFile;

    /**
     *
     * @param  string $xlsxFile
     */
    public function __construct(string $xlsxFile)
    {
        $this->xlsxFile = $xlsxFile;
    }

    /**
     *
     * @return array 
     */
    public function getDatos(): array
    {
        $datosXLSX = [];
        $total = 0;

        $spreadsheet = IOFactory::load($this->xlsxFile);

        $sheet = $spreadsheet->getActiveSheet();

        foreach ($sheet->getRowIterator() as $row) {
            $rowData = [];
            $cellIterator = $row->getCellIterator();
            $cellIterator->setIterateOnlyExistingCells(false);
            foreach ($cellIterator as $cell) {
                $rowData[] = $cell->getValue();
            }

            $datosXLSX[] = $rowData;
            $total++;
        }

        return [
            "datos" => $datosXLSX,
            "total" => $total,
        ];
    }
}
