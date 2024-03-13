<?php
/* MOSTRAR LOS ERRORES */
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

/* ASIGNAR LOS HEADERS A LA API */
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method, Content-Encoding");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
header("Allow: GET, POST, OPTIONS, PUT, DELETE");
header('Content-Type: application/json');


const ROOT_DIR = __DIR__;

require(ROOT_DIR . '/vendor/autoload.php');
use ComprobadorEquivalencias\Infrastructure\Controlador;

/* CONTROLA QUE LA PETICIÓN SEA HTTP (EVITA PROBLEMAS DE CORS) */
if (!isset($_REQUEST) or !isset($_SERVER['REQUEST_METHOD'])) {
    throw new Exception('Peticion HTTP invalida');
}

/* AÑADIR LOS WEB SERVICES */
$router = [
    'get' => [
        'hello_world' => [
            'controlador' => 'ComprobadorEquivalencias\Infrastructure\Controlador',
            'funcion' => 'helloWorld'
        ],
    ],
    'post' => [
        'comprobarFichero' => [
            'controlador' => 'ComprobadorEquivalencias\Infrastructure\Controlador',
            'funcion' => 'comprobarFichero'
        ],
        'obtenerEstadisticas' => [
            'controlador' => 'ComprobadorEquivalencias\Infrastructure\Controlador',
            'funcion' => 'obtenerEstadisticas'
        ],
    ]
];
$verbo_http = obtenerMetodoHTTP();
$servicio = obtenerServicio();
$parametros = obtenerParametros();
/*Si la peticion no tienen ese servicio acaba*/
if (!isset($router[$verbo_http][$servicio])) {
    $error = [
        "status" => "KO",
        "error" => "Servicio no disponible:[$verbo_http][$servicio]"
    ];

    die(json_encode($error));
}


$gestorServicio = $router[$verbo_http][$servicio];
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

/**
 * @return string
 * @throws Exception
 */
function obtenerMetodoHTTP(): string
{
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $metodo = 'post';
    } elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $metodo = 'get';
    } elseif ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
        $metodo = 'delete';
    } else {
        throw new Exception('Metodo http no disponible:' . $_SERVER['REQUEST_METHOD']);
    }

    return $metodo;
}


/**
 * @return string
 */
function obtenerServicio(): string
{
    $request_body = json_decode(file_get_contents('php://input'), true);

    if (isset($_REQUEST['service'])) {
        $servicio = $_REQUEST['service'];
    } elseif (isset($request_body['service'])) {
        $servicio = $request_body['service'];
    } else {
        $servicio = '';
    }

    return $servicio;
}

/**
 * @return array
 */
function obtenerParametros(): array
{
    $request_body = json_decode(file_get_contents('php://input'), true);

    if (isset($_REQUEST['service'])) {
        if (isset($request_body)) {
            $parametros = array_merge($_REQUEST, $request_body);
        } else {
            $parametros = $_REQUEST;
        }
    } else {
        $parametros = $request_body;
    }



    if (isset($_FILES['file'])) {
        $directory = __DIR__ . '/files/';
        //Eliminar archivos anteriores
        $files = scandir($directory);
    
        foreach ($files as $file) {
            if ($file != '.' && $file != '..') {
                $filePath = $directory . $file;
                unlink($filePath);
            }
        }

        sleep(2);
        if ($_FILES['file']['error'] === UPLOAD_ERR_OK) {
            $tempFilePath = $_FILES['file']['tmp_name'];

            if ($_SERVER['HTTP_CONTENT_ENCODING'] === 'gzip') {
                $compressedContent = file_get_contents($tempFilePath);

                $uncompressedContent = zlib_decode($compressedContent);

                $tempFilePath = $directory . $parametros["NombreFile"];

                file_put_contents($tempFilePath, $uncompressedContent);
            }

            $parametros['file'] = $tempFilePath;
        }
    }




    if (!is_array($parametros)) {
        return [];
    }
    unset($parametros['service']);

    return $parametros;
}
