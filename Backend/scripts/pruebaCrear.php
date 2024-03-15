<?php
// Incluye la autoloader de Composer para cargar las clases de PhpSpreadsheet
require '/usr/local/programadores/ComprobarEquivalencia/Backend/Api/vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Ods;

// Crea una nueva instancia de Spreadsheet
$spreadsheet = new Spreadsheet();

// Haz las modificaciones necesarias en tu hoja de cálculo, por ejemplo:
$sheet = $spreadsheet->getActiveSheet();
$sheet->setCellValue('A1','Hello World!');

// Crea un objeto Writer para guardar el archivo en formato ODS
$writer = new Ods($spreadsheet);

// Guarda el archivo en la ubicación deseada
$writer->save('/usr/local/programadores/ComprobarEquivalencia/Backend/Api/scripts/sd.ods');

echo 'El archivo ODS ha sido creado correctamente.';

