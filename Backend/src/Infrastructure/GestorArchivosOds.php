<?php
namespace ComprobadorEquivalencias\Infrastructure;

ini_set('memory_limit', '1024M');
set_time_limit(300);
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Ods;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use ComprobadorEquivalencias\Domain\GestorFicheroBase;

class GestorArchivosOds extends GestorFicheroBase
{
    private string $filePath;
    private Spreadsheet $spreadsheet;

    private string $nombreArchivo;

    
    /**
     * __construct
     *
     * @param  string $filePath
     * @param  string $nombreArchivo
     */
    public function __construct(string $filePath, string $nombreArchivo)
    {
        $this->filePath = $filePath;
        $this->nombreArchivo = $nombreArchivo;
        $this->spreadsheet = new Spreadsheet();
    }

    /**
     * crearArchivoOds
     *
     * @param  array $datos
     * @return void
     */
    public function crearArchivo(array $datos): void
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


            if ($dato["Activa"] == "No descargada") {
                $color = "FF0000";
            } else {
                switch ($dato['Estado']) {
                    case 'Pendiente':
                        $color = "FFC0CB";
                        break;
                    case 'Mapeado':
                        $color = "ADD8E6";
                        break;
                    case 'Mapeado Block':
                        $color = "98FB98";
                        break;
                    default:
                        $color = "FFFFFF";
                        break;
                }
            }
            $hoja->getStyle('A' . $fila . ':D' . $fila)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB($color);
            $fila++;
        }


        $total = $datos["total"];
        $totalActiva = $datos["activa total"];
        $totalNoActiva = $datos["no activa total"];
        $hoja->setCellValue('E2', $total);
        $hoja->setCellValue('E4', 'Activa');
        $hoja->setCellValue('E5', $totalActiva);
        $hoja->setCellValue('E6', 'No Activa');
        $hoja->setCellValue('E7', $totalNoActiva);


        $mapeado = $datos["mapeado"];
        $mapeadoActiva = $datos["activa mapeado"];
        $mapeadoNoActiva = $datos["no activa mapeado"];
        $hoja->setCellValue('F2', $mapeado);
        $hoja->setCellValue('F4', 'Activa');
        $hoja->setCellValue('F5', $mapeadoActiva);
        $hoja->setCellValue('F6', number_format(($mapeadoActiva / $totalActiva) * 100, 4) . '%');
        $hoja->setCellValue('F7', 'No Activa');
        $hoja->setCellValue('F8', $mapeadoNoActiva);
        $hoja->setCellValue('F9', number_format(($mapeadoNoActiva / $totalNoActiva) * 100, 4) . '%');

        $block = $datos["block"];
        $blockActiva = $datos["activa block"];
        $blockNoActiva = $datos["no activa block"];
        $hoja->setCellValue('G2', $block);
        $hoja->setCellValue('G4', 'Activa');
        $hoja->setCellValue('G5', $blockActiva);
        $hoja->setCellValue('G6', number_format(($blockActiva / $totalActiva) * 100, 4) . '%');
        $hoja->setCellValue('G7', 'No Activa');
        $hoja->setCellValue('G8', $blockNoActiva);
        $hoja->setCellValue('G9', number_format(($blockNoActiva / $totalNoActiva) * 100, 4) . '%');


        $pendiente = $datos["pendiente"];
        $pendienteActiva = $datos["activa pendiente"];
        $pendienteNoActiva = $datos["no activa pendiente"];
        $hoja->setCellValue('H2', $pendiente);
        $hoja->setCellValue('H4', 'Activa');
        $hoja->setCellValue('H5', $pendienteActiva);
        $hoja->setCellValue('H6', number_format(($pendienteActiva / $totalActiva) * 100, 4) . '%');
        $hoja->setCellValue('H7', 'No Activa');
        $hoja->setCellValue('H8', $pendienteNoActiva);
        $hoja->setCellValue('H9', number_format(($pendienteNoActiva / $totalNoActiva) * 100, 4) . '%');

        $noDescargado = $datos["no descargado"];
        $hoja->setCellValue('I2', $noDescargado);


        $hoja->setCellValue('F3', number_format(($mapeado / $total) * 100, 4) . '%');
        $hoja->setCellValue('G3', number_format(($block / $total) * 100, 4) . '%');
        $hoja->setCellValue('H3', number_format(($pendiente / $total) * 100, 4) . '%');
        $hoja->setCellValue('I3', number_format(($noDescargado / $total) * 100, 4) . '%');

        $writer = new Ods($this->spreadsheet);
        $writer->save($this->filePath . $this->nombreArchivo);

    }

}
