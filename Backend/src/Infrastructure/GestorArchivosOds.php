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
        $estadisticas=$datos['estadisticas'];
        $hoja->getColumnDimension('A')->setWidth(30);
        $hoja->getColumnDimension('B')->setWidth(70);
        $hoja->getColumnDimension('C')->setWidth(20);
        $hoja->getColumnDimension('D')->setWidth(20);
        $hoja->getColumnDimension('E')->setWidth(20);
        $hoja->getColumnDimension('F')->setWidth(10);
        $hoja->getColumnDimension('G')->setWidth(20);
        $hoja->getColumnDimension('H')->setWidth(25);
        $hoja->getColumnDimension('I')->setWidth(25);
        $hoja->getColumnDimension('J')->setWidth(25);




        $hoja->getStyle('1:1')->getFont()->setBold(true);
        $hoja->getStyle('A1:J1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $hoja->setCellValue('A1', 'Código');
        $hoja->setCellValue('B1', 'Nombre');
        $hoja->setCellValue('C1', 'Estado');
        $hoja->setCellValue('D1', 'Activa');
        $hoja->setCellValue('E1', 'Total');

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


        $total = $estadisticas["total"];
        $totalActiva = $estadisticas["activaTotal"];
        $totalNoActiva = $estadisticas["noActivaTotal"];
        $hoja->setCellValue('E2', $total);
        $hoja->setCellValue('E3', '100%');
        $hoja->getStyle('E4:E4')->getFont()->setBold(true);
        $hoja->getStyle('E4:E4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $hoja->setCellValue('E4', 'Activa');
        $hoja->setCellValue('E5', $totalActiva);
        $hoja->setCellValue('E6', number_format(($totalActiva / $total) * 100, 2) . '%');
        $hoja->getStyle('E7:E7')->getFont()->setBold(true);
        $hoja->getStyle('E7:E7')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $hoja->setCellValue('E7', 'No Activa');
        $hoja->setCellValue('E8', $totalNoActiva);
        $hoja->setCellValue('E9', number_format(($totalNoActiva / $total) * 100, 2) . '%');
        $hoja->getStyle('E10:E10')->getFont()->setBold(true);
        $hoja->getStyle('E10:E10')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $hoja->setCellValue('E10', 'No descargado');
        $noDescargado = $estadisticas["noDescargados"];
        $hoja->setCellValue('E11', $noDescargado);
        $hoja->setCellValue('E12', number_format(($noDescargado / $total) * 100, 2) . '%');
        $hoja->setCellValue('H1', 'Total');
        $hoja->setCellValue('I1', 'Activos');
        $hoja->setCellValue('J1', 'No activos');


        $hoja->getStyle('G1:G6')->getFont()->setBold(true);
        $hoja->getStyle('G1:G6')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $mapeado = $estadisticas["mapeados"];
        $mapeadoActiva = $estadisticas["activaMapeado"];
        $mapeadoNoActiva = $estadisticas["noActivaMapeado"];
        $hoja->setCellValue('G2', 'Mapeado');
        $hoja->setCellValue('H2', $mapeado."(".number_format(($mapeado / $total) * 100, 2) . '%'.")");
        $hoja->setCellValue('I2', $mapeadoActiva."(".number_format(($mapeadoActiva / $totalActiva) * 100, 2) . '%'.")");
        $hoja->setCellValue('J2', $mapeadoNoActiva."(".number_format(($mapeadoNoActiva / $totalNoActiva) * 100, 2) . '%'.")");

        
        $block = $estadisticas["mapeadosBlock"];
        $blockActiva = $estadisticas["activaBlock"];
        $blockNoActiva = $estadisticas["noActivaBlock"];
        $hoja->setCellValue('G4', 'Mapeado Block');
        $hoja->setCellValue('H4', $block."(".number_format(($block / $total) * 100, 2) . '%'.")");
        $hoja->setCellValue('I4', $blockActiva."(".number_format(($blockActiva / $totalActiva) * 100, 2) . '%'.")");
        $hoja->setCellValue('J4', $blockNoActiva."(".number_format(($blockNoActiva / $totalNoActiva) * 100, 2) . '%'.")");


        $pendiente = $estadisticas["pendientes"];
        $pendienteActiva = $estadisticas["activaPendiente"];
        $pendienteNoActiva = $estadisticas["noActivaPendiente"];
        $hoja->setCellValue('G6', 'Pendiente');
        $hoja->setCellValue('H6', $pendiente."(".number_format(($pendiente / $total) * 100, 2) . '%'.")");
        $hoja->setCellValue('I6', $pendienteActiva."(".number_format(($pendienteActiva / $totalActiva) * 100, 2) . '%'.")");
        $hoja->setCellValue('J6', $pendienteNoActiva."(".number_format(($pendienteNoActiva / $totalNoActiva) * 100, 2) . '%'.")");


        $writer = new Ods($this->spreadsheet);
        try {
            $writer->save($this->filePath . $this->nombreArchivo);
        } catch (\Throwable $e) {
            throw new \Exception("Error no esperado: ", $e->getMessage());
        }
    }

}
