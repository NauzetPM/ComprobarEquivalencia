<?php
namespace ComprobadorEquivalencias\Infrastructure;

//require(__DIR__ . "/../../vendor/autoload.php");

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Ods;

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

        // Obtener la hoja activa
        $hoja = $this->spreadsheet->getActiveSheet();

        // Definir los encabezados de la hoja de cálculo
        $hoja->setCellValue('A1', 'Código');
        $hoja->setCellValue('B1', 'Nombre');
        $hoja->setCellValue('C1', 'Estado');

        // Insertar datos en la hoja de cálculo
        $fila = 2;
        foreach ($datos as $dato) {
            $hoja->setCellValue('A' . $fila, $dato['Codigo']);
            $hoja->setCellValue('B' . $fila, $dato['Nombre']);
            $hoja->setCellValue('C' . $fila, $dato['Estado']);
            $fila++;
        }

        // Crear un objeto Writer para guardar el archivo ODS
        $writer = new Ods($this->spreadsheet);

        // Guardar el archivo ODS en el servidor
        $writer->save($this->filePath);
    }
}


$datos = [
    ['Codigo' => '001', 'Nombre' => 'Producto 1', 'Estado' => 'Activo'],
    ['Codigo' => '002', 'Nombre' => 'Producto 2', 'Estado' => 'Inactivo'],
    ['Codigo' => '003', 'Nombre' => 'Producto 3', 'Estado' => 'Activo']
];

$gestor = new GestorArchivosOds('/usr/local/programadores/ComprobarEquivalencia/Backend/Api/files/ds.ods');
$gestor->crearArchivoOds($datos);

echo 'El archivo ODS ha sido creado correctamente.';
