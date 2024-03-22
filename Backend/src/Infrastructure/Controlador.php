<?php

declare(strict_types=1);

namespace ComprobadorEquivalencias\Infrastructure;

use ComprobadorEquivalencias\Application\ObtenerEstadoEquivalencias;
use ComprobadorEquivalencias\Application\ObtenerSeleccion;
use Dotenv\Dotenv;

class Controlador
{
    private array $parametros;
    private CacheManager $cacheManager;
    private string $rutaCache = __DIR__ . "/../../cache/";

    private string $rutaFiles = __DIR__ . "/../../files/";
    private string $rutaTemporales = __DIR__ . "/../../temporales/";

    /**
     *
     * @param  array $parametros
     */
    public function __construct(array $parametros)
    {
        $this->cacheManager = new CacheManager($this->rutaCache);

        $dotenv = Dotenv::createImmutable(__DIR__ . '/../../');
        $dotenv->load();

        $this->parametros = $parametros;
        $limpiadorFiles = new FileCleaner();
        $limpiadorFiles->cleanOldFiles($this->rutaFiles);
        $limpiadorFiles->cleanOldFiles($this->rutaCache);
        $limpiadorFiles->cleanOldFiles($this->rutaTemporales);
        sleep(1);
    }



    /**
     * @throws \Exception
     * @return bool
     */
    public function comprobarToken(): array
    {
        if (!isset($this->parametros['token'])) {
            throw new \Exception('No se ha recibido token');
        }
        $token = $this->parametros['token'];
        $return = $this->cacheManager->esTokenValido($token);
        return ([
            "status" => "OK",
            "tokenValido" => $return
        ]);
    }

    /**
     * @throws \Exception
     * @return array
     */
    public function subirFichero(): array
    {
        if (!isset($this->parametros["totalChunks"]) || !isset($this->parametros["chunkIndex"])) {
            throw new \Exception('No se ha recibido totalChunks o chunkIndex');
        }
        if ($this->parametros["totalChunks"] == $this->parametros["chunkIndex"] + 1) {
            if (!isset($this->parametros['token'])) {
                throw new \Exception('No se ha recibido token');
            }
            $this->cacheManager->guardarToken($this->parametros['token'], $this->parametros['token']);
            return ([
                "status" => "OK",
                "code" => "Reconstruccion del archivo correcta y creacion del token"
            ]);
        }
        return ([
            "status" => "OK",
            "code" => "Se subio correctamente el fragmento"
        ]);
    }

    /**
     *
     * @throws \Exception
     * @return void
     */
    public function descargar(): void
    {
        if (!isset($this->parametros['token'])) {
            throw new \Exception('No se ha recibido token');
        }
        if (!isset($this->parametros["NombreMayorista"])) {
            throw new \Exception('No se ha recibido Nombre Mayorista');
        }
        if (!isset($this->parametros["NombreFile"])) {
            throw new \Exception('No se ha recibido NombreFile');
        }
        $database = new Database($_ENV['BBDD_HOST'], $_ENV['BBDD_USER'], $_ENV['BBDD_PASS'], $_ENV['BBDD_DATABASE_CONFIG'], $_ENV['BBDD_PORT']);
        $selector = new GestorSelectorMysql($database);


        $BBDDSelector = new ObtenerSeleccion(
            $selector,
            $this->parametros["NombreMayorista"]
        );

        $parametrosBBDD = $BBDDSelector();
        unset($BBDDSelector);
        unset($selector);
        $dbName = $parametrosBBDD["conexion"];

        $NombreTabla1 = $parametrosBBDD["tabla1"];

        $NombreTabla2 = $parametrosBBDD["tabla2"];
        unset($parametrosBBDD);
        $partes = explode('.', $this->parametros["NombreFile"]);
        $extension = $partes[1];
        unset($partes);

        $nombreArchivoDescargar = $this->parametros['token'] . ".ods";
        $gestorArchivo = new GestorArchivosOds($this->rutaFiles, $nombreArchivoDescargar);
        if (!file_exists($this->rutaFiles . $nombreArchivoDescargar)) {
            $equivalenciasDao = new EquivalenciasDAOMysql($database, $NombreTabla1, $dbName);
            $comprobarActiva = new ActivaDaoMysql($database, $NombreTabla2, $dbName);
            unset($database);
            unset($dbName);
            unset($NombreTabla1);
            unset($NombreTabla2);
            $rutaArchivoCompleto = $this->rutaFiles . $this->parametros["NombreFile"];
            if ($extension == "csv") {
                $gestorEstablecimientos = new GestorEstablecimientosCSV($rutaArchivoCompleto);
            } elseif ($extension == "ods") {
                $gestorEstablecimientos = new GestorEstablecimientosODS($rutaArchivoCompleto);
            } elseif ($extension == "xlsx") {
                $gestorEstablecimientos = new GestorEstablecimientosXLSX($rutaArchivoCompleto);
            } else {
                throw new \Exception('Tipo de archivo no valido');
            }
            unset($extension);
            if (!file_exists($rutaArchivoCompleto)) {
                throw new \Exception('Tipo de archivo no valido');
            }
            $obtenerEquivalencias = new ObtenerEstadoEquivalencias(

                $gestorEstablecimientos,
                $equivalenciasDao,
                $comprobarActiva
            );
            $datos = $obtenerEquivalencias();
            unset($obtenerEquivalencias);
            unset($gestorEstablecimientos);
            unset($rutaArchivoCompleto);
            unset($comprobarActiva);
            unset($equivalenciasDao);

            $gestorArchivo->crearArchivo($datos);
            unset($datos);
        }
        $gestorArchivo->descargarArchivo($this->rutaFiles, $nombreArchivoDescargar);
    }
}
