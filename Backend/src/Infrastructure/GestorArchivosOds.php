<?php
namespace ComprobadorEquivalencias\Infrastructure;
ini_set('memory_limit', '1024M');
set_time_limit(300);
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Ods;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class GestorArchivosOds
{
    private $filePath;
    private $spreadsheet;

    public function __construct($filePath)
    {
        $this->filePath = $filePath;
        $this->spreadsheet = new Spreadsheet();
    }

    public function crearArchivoOds($datos)
{
    $hoja = $this->spreadsheet->getActiveSheet();

    $hoja->getColumnDimension('A')->setWidth(30);
    $hoja->getColumnDimension('B')->setWidth(50);
    $hoja->getColumnDimension('C')->setWidth(30);
    $hoja->getColumnDimension('D')->setWidth(30);
    $hoja->getColumnDimension('E')->setWidth(30);
    $hoja->getColumnDimension('F')->setWidth(30);
    $hoja->getColumnDimension('G')->setWidth(30);




    $hoja->getStyle('1:1')->getFont()->setBold(true);
    $hoja->getStyle('A1:G1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    $hoja->setCellValue('A1', 'Código');
    $hoja->setCellValue('B1', 'Nombre');
    $hoja->setCellValue('C1', 'Estado');
    $hoja->setCellValue('D1', 'Total');
    $hoja->setCellValue('E1', 'Mapeado');
    $hoja->setCellValue('F1', 'Mapeado Block');
    $hoja->setCellValue('G1', 'Pendiente');

    $fila = 2;
    foreach ($datos["datos"] as $dato) {
        $hoja->setCellValue('A' . $fila, $dato['Codigo']);
        $hoja->setCellValue('B' . $fila, $dato['Nombre']);
        $hoja->setCellValue('C' . $fila, $dato['Estado']);
        $color;
        switch ($dato['Estado']) {
            case 'Pendiente':
                $color="FFC0CB";
                break;
            case 'Mapeado':
                $color="ADD8E6";
                break;
            case 'Mapeado Block':
                $color="98FB98";
                break;
            default:
            $color="FFFFFF";
                break;
        }
        $hoja->getStyle('A' . $fila . ':C' . $fila)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB($color);
        $fila++;
    }
    
    $hoja->setCellValue('D2', $datos["total"]);
    $hoja->setCellValue('E2', $datos["mapeado"]);
    $hoja->setCellValue('F2', $datos["block"]);
    $hoja->setCellValue('G2', $datos["pendiente"]);

    $total = $datos["total"];
    $mapeado = $datos["mapeado"];
    $block = $datos["block"];
    $pendiente = $datos["pendiente"];

    $hoja->setCellValue('E3', number_format(($mapeado / $total) * 100, 2) . '%');
    $hoja->setCellValue('F3', number_format(($block / $total) * 100, 2) . '%');
    $hoja->setCellValue('G3', number_format(($pendiente / $total) * 100, 2) . '%');
    

    $writer = new Ods($this->spreadsheet);
    $writer->save($this->filePath);
}

}
