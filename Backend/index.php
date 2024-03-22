<?php

declare(strict_types=1);
/* MOSTRAR LOS ERRORES */
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


include 'metodos.php';


/* ASIGNAR LOS HEADERS A LA API */
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method, Content-Encoding");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
header("Allow: GET, POST, OPTIONS, PUT, DELETE");
header('Content-Type: application/json');
header('Accept-Encoding: gzip, deflate');


const ROOT_DIR = __DIR__;

require(ROOT_DIR . '/vendor/autoload.php');

/* CONTROLA QUE LA PETICIÓN SEA HTTP (EVITA PROBLEMAS DE CORS) */
if (!isset($_REQUEST) or !isset($_SERVER['REQUEST_METHOD'])) {
    throw new Exception('Peticion HTTP invalida');
}


$verbo_http = obtenerMetodoHTTP();
$servicio = obtenerServicio();
$parametros = obtenerParametros();

$gestorServicio = buscarServicioDisponible($verbo_http, $servicio);
$controlador = $gestorServicio['controlador'];
$funcion = $gestorServicio['funcion'];
try {
    $gestorControlador = new $controlador($parametros);
    $datos = $gestorControlador->$funcion();
    die(json_encode($datos));
} catch (Exception $e) {
    $error = [
        "status" => "KO",
        "error" => $e->getMessage()
    ];

    die(json_encode($error));
}
