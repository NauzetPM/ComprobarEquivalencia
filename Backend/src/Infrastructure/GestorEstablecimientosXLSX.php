<?php

namespace ComprobadorEquivalencias\Infrastructure;

use ComprobadorEquivalencias\Domain\GestorEstablecimientos;
use PhpOffice\PhpSpreadsheet\IOFactory;
use ComprobadorEquivalencias\Domain\EstablecimientoMayorista;

class GestorEstablecimientosXLSX implements GestorEstablecimientos
{
    private string $xlsxFilePath;

    /**
     *
     * @param  string $xlsxFilePath
     */
    public function __construct(string $xlsxFilePath)
    {
        $this->xlsxFilePath = $xlsxFilePath;
    }

    /**
     *
     * @return array 
     */
    public function getDatos(): array
    {
        $datosXLSX = [];
        $total = 0;

        $spreadsheet = IOFactory::load($this->xlsxFilePath);

        $sheet = $spreadsheet->getActiveSheet();

        foreach ($sheet->getRowIterator() as $row) {
            $rowData = [];
            $cellIterator = $row->getCellIterator();
            $cellIterator->setIterateOnlyExistingCells(false);
            foreach ($cellIterator as $cell) {
                $rowData[] = $cell->getValue();
            }

            $datosXLSX[] = EstablecimientoMayorista::fromArray($rowData);
            $total++;
        }

        return [
            "datos" => $datosXLSX,
            "total" => $total,
        ];
    }
}
