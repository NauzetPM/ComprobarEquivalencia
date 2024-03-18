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
header('Accept-Encoding: gzip, deflate');


const ROOT_DIR = __DIR__;

require(ROOT_DIR . '/vendor/autoload.php');

/* CONTROLA QUE LA PETICIÓN SEA HTTP (EVITA PROBLEMAS DE CORS) */
if (!isset($_REQUEST) or !isset($_SERVER['REQUEST_METHOD'])) {
    throw new Exception('Peticion HTTP invalida');
}

/* AÑADIR LOS WEB SERVICES */
$router = [
    'post' => [
        'comprobarFichero' => [
            'controlador' => 'ComprobadorEquivalencias\Infrastructure\Controlador',
            'funcion' => 'comprobarFichero'
        ],
        'descargar' => [
            'controlador' => 'ComprobadorEquivalencias\Infrastructure\Controlador',
            'funcion' => 'descargar'
        ],
        'comprobar' => [
            'controlador' => 'ComprobadorEquivalencias\Infrastructure\Controlador',
            'funcion' => 'comprobarToken'
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
    if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
        header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method, Content-Encoding");
        exit();
    } elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
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
            $compressedContent = file_get_contents($tempFilePath);
            $uncompressedContent = @zlib_decode($compressedContent);
            echo var_export($_FILES['file'], true);
            if ($uncompressedContent !== false) {
                $tempFilePath = $directory . $parametros["NombreFile"];
                file_put_contents($tempFilePath, $uncompressedContent);
            } else {
                echo "Error al descomprimir el archivo.";
            }
        } else {
            echo "Error al subir el archivo: " . var_export($_FILES['file'], true);
        }
    }


    //
    if (isset($_FILES['chunk'])) {
        if ($_FILES['chunk']['error'] === UPLOAD_ERR_OK) {
            $directory = '/usr/local/programadores/ComprobarEquivalencia/Backend/temporales/';
            $fileName = $_POST['NombreFile'];
            $chunkIndex = (int) $_POST['chunkIndex'];
            $tempFilePath = $directory . $fileName . '_' . $chunkIndex;

            // Verificar si es la primera vez que se suben fragmentos
            /*if ($chunkIndex === 0) {
                // Eliminar todos los archivos en la carpeta files
                $completeDirectory = '/usr/local/programadores/ComprobarEquivalencia/Backend/files/';
                $files = glob($completeDirectory . '*'); // Obtener todos los archivos en la carpeta
                foreach ($files as $file) {
                    if (is_file($file)) {
                        unlink($file); // Eliminar cada archivo encontrado
                    }
                }
                sleep(1);
            }*/
            
            move_uploaded_file($_FILES['chunk']['tmp_name'], $tempFilePath);
            $totalChunks = (int) $_POST['totalChunks'];
            $receivedChunks = count(glob($directory . $fileName . '_*'));

            if ($receivedChunks == $totalChunks) {
                $completeFilePath = '/usr/local/programadores/ComprobarEquivalencia/Backend/files/' . $fileName;
                $completeFile = fopen($completeFilePath, 'wb');

                for ($i = 0; $i < $totalChunks; $i++) {
                    $chunkContent = file_get_contents($directory . $fileName . '_' . $i);
                    fwrite($completeFile, $chunkContent);
                    unlink($directory . $fileName . '_' . $i);
                }

                fclose($completeFile);

                echo "Archivo reconstruido correctamente.";
            }
        } else {
            echo "Error al subir el fragmento: " . $_FILES['chunk']['error'];
        }

    }

    //

    if (!is_array($parametros)) {
        return [];
    }
    unset($parametros['service']);

    return $parametros;
}
