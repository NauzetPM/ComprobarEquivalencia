<?php
ini_set('display_errors', 1);

ini_set('display_startup_errors', 1);

error_reporting(E_ALL);

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

include("/usr/local/programadores/ComprobarEquivalencia/Backend/Database.php");
include("/usr/local/programadores/ComprobarEquivalencia/Backend/Hoteles.php");

//Obtener Coneccion BBDD
$database = new Database();
$db = $database->getConnection();

$hoteles = new Hoteles($db);

//coger csv
$rutaCSV = "/usr/local/programadores/ComprobarEquivalencia/Backend/ejemplo.csv";

if (file_exists($rutaCSV)) {
    $busqueda = $hoteles->comprobar($rutaCSV);
    if (count($busqueda) > 0) {
        http_response_code(200);
        echo json_encode($busqueda);
    } else {
        http_response_code(404);
        echo json_encode(
            array("mesage" => "No se encontro coincidencias en la base de datos.")
        );
    }


} else {
    http_response_code(404);
    echo json_encode(
        array("mesage" => "Error con el archivo csv.")
    );
}