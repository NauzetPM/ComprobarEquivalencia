<?php
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

    if (isset ($_REQUEST['service'])) {
        $servicio = $_REQUEST['service'];
    } elseif (isset ($request_body['service'])) {
        $servicio = $request_body['service'];
    } else {
        $servicio = '';
    }

    return $servicio;
}

/**
 * @throws Exception
 * @return array
 */
function obtenerParametros(): array
{
    $request_body = json_decode(file_get_contents('php://input'), true);

    if (isset ($_REQUEST['service'])) {
        if (isset ($request_body)) {
            $parametros = array_merge($_REQUEST, $request_body);
        } else {
            $parametros = $_REQUEST;
        }
    } else {
        $parametros = $request_body;
    }

    if (isset ($_FILES['chunk'])) {
        if ($_FILES['chunk']['error'] === UPLOAD_ERR_OK) {
            $directory = '/usr/local/programadores/ComprobarEquivalencia/Backend/temporales/';
            $fileName = $_POST['NombreFile'];
            $chunkIndex = (int) $_POST['chunkIndex'];
            $tempFilePath = $directory . $fileName . '_' . $chunkIndex;

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

            }
        } else {
            throw new Exception("Error al subir el fragmento: " . $_FILES['chunk']['error']);
        }

    }

    if (!is_array($parametros)) {
        return [];
    }
    unset($parametros['service']);

    return $parametros;
}

/**
 * buscarServicioDisponible
 *
 * @param  string $verbo_http
 * @param  string $servicio
 * @return array
 */
function buscarServicioDisponible(string $verbo_http, string $servicio): array
{
    /* AÑADIR LOS WEB SERVICES */
    $router = [
        'post' => [
            'subirFichero' => [
                'controlador' => 'ComprobadorEquivalencias\Infrastructure\Controlador',
                'funcion' => 'subirFichero'
            ],
            'descargar' => [
                'controlador' => 'ComprobadorEquivalencias\Infrastructure\Controlador',
                'funcion' => 'descargar'
            ],
            'comprobarToken' => [
                'controlador' => 'ComprobadorEquivalencias\Infrastructure\Controlador',
                'funcion' => 'comprobarToken'
            ],
        ]
    ];

    if (!isset ($router[$verbo_http][$servicio])) {
        $error = [
            "status" => "KO",
            "error" => "Servicio no disponible:[$verbo_http][$servicio]"
        ];

        die (json_encode($error));
    }
    return $router[$verbo_http][$servicio];

}