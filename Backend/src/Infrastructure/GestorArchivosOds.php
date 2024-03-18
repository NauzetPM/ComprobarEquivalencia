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
    $hoja->getColumnDimension('B')->setWidth(70);
    $hoja->getColumnDimension('C')->setWidth(20);
    $hoja->getColumnDimension('D')->setWidth(20);
    $hoja->getColumnDimension('E')->setWidth(20);
    $hoja->getColumnDimension('F')->setWidth(20);
    $hoja->getColumnDimension('G')->setWidth(20);
    $hoja->getColumnDimension('H')->setWidth(20);
    $hoja->getColumnDimension('I')->setWidth(20);   




    $hoja->getStyle('1:1')->getFont()->setBold(true);
    $hoja->getStyle('A1:G1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    $hoja->setCellValue('A1', 'Código');
    $hoja->setCellValue('B1', 'Nombre');
    $hoja->setCellValue('C1', 'Estado');
    $hoja->setCellValue('D1', 'Activa');
    $hoja->setCellValue('E1', 'Total');
    $hoja->setCellValue('F1', 'Mapeado');
    $hoja->setCellValue('G1', 'Mapeado Block');
    $hoja->setCellValue('H1', 'Pendiente');
    $hoja->setCellValue('I1', 'No descargado');
    $fila = 2;
    foreach ($datos["datos"] as $dato) {
        $hoja->setCellValue('A' . $fila, $dato['Codigo']);
        $hoja->setCellValue('B' . $fila, $dato['Nombre']);
        $hoja->setCellValue('C' . $fila, $dato['Estado']);
        $hoja->setCellValue('D' . $fila, $dato['Activa']);

        
        $color;
        if($dato["Activa"]=="No descargada"){
            $color="FF0000";
        }else{
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
    }
        $hoja->getStyle('A' . $fila . ':D' . $fila)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB($color);
        $fila++;
    }
    $total = $datos["total"];
    $mapeado = $datos["mapeado"];
    $block = $datos["block"];
    $pendiente = $datos["pendiente"];
    $noDescargado=$datos["no descargado"];
    $hoja->setCellValue('E2', $total);
    $hoja->setCellValue('F2', $mapeado);
    $hoja->setCellValue('G2', $block);
    $hoja->setCellValue('H2', $pendiente);
    $hoja->setCellValue('I2', $noDescargado);


    $hoja->setCellValue('F3', number_format(($mapeado / $total) * 100, 4) . '%');
    $hoja->setCellValue('G3', number_format(($block / $total) * 100, 4) . '%');
    $hoja->setCellValue('H3', number_format(($pendiente / $total) * 100, 4) . '%');
    $hoja->setCellValue('I3', number_format(($noDescargado / $total) * 100, 4) . '%');

    $writer = new Ods($this->spreadsheet);
    $writer->save($this->filePath);
}

}
