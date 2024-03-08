<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

include("/usr/local/programadores/ComprobarEquivalencia/Backend/Database.php");
include("/usr/local/programadores/ComprobarEquivalencia/Backend/Hoteles.php");

$peticion=$_REQUEST['servicio'];
if($peticion=="comprobarFichero"){
    $database = new Database();
    $db = $database->getConnection();
    
    $hoteles = new Hoteles($db);
    
    if ($_FILES['file']['error'] === UPLOAD_ERR_OK) {
        $tempFilePath = $_FILES['file']['tmp_name'];
        $busqueda = $hoteles->comprobar($tempFilePath);
    
        if (count($busqueda) > 0) {
            http_response_code(200);
            echo json_encode($busqueda);
        } else {
            http_response_code(404);
            echo json_encode(array("message" => "No se encontraron coincidencias en la base de datos."));
        }
    } else {
        http_response_code(404);
        echo json_encode(array("message" => "Error al procesar el archivo."));
    }
    
    
}

