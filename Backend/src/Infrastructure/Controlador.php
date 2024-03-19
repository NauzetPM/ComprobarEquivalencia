<?php
namespace ComprobadorEquivalencias\Infrastructure;

use ComprobadorEquivalencias\Application\ObtenerEstadoEquivalencias;
use ComprobadorEquivalencias\Application\ObtenerSeleccion;
use Dotenv\Dotenv;

class Controlador
{
    private array $parametros;
    private $cacheManager;
    private $rutaCache = __DIR__ . "/../../cache/";

    private $rutaFiles = __DIR__ . "/../../files/";
    private $rutaTemporales = __DIR__ . "/../../temporales/";

    /**
     * __construct
     *
     * @param  array $parametros
     * @return void
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

    }



    /**
     * comprobarToken
     *
     * @return bool
     */
    public function comprobarToken(): bool
    {
        if (!isset ($this->parametros['token'])) {
            throw new \Exception('No se ha recibido token');
        }
        $token = $this->parametros['token'];
        $return = $this->cacheManager->esTokenValido($token);
        return $return;
        
    }

    /**
     * comprobarFichero
     *
     * @return array
     */
    public function subirFichero(): array
    {
        if(!isset($this->parametros["totalChunks"]) || !isset($this->parametros["chunkIndex"])){
            throw new \Exception('No se ha recibido totalChunks o chunkIndex');
        }
        if($this->parametros["totalChunks"]==$this->parametros["chunkIndex"]+1){
            if (!isset ($this->parametros['token'])) {
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
     * descargar
     *
     * @return void
     */
    public function descargar(): void
    {   
        if (!isset ($this->parametros['token'])) {
            throw new \Exception('No se ha recibido token');
        }
        if (!isset ($this->parametros["NombreMayorista"])) {
            throw new \Exception('No se ha recibido Nombre Mayorista');
        }
        if (!isset ($this->parametros["NombreFile"])) {
            throw new \Exception('No se ha recibido NombreFile');
        }
        $database = new Database($_ENV['BBDD_HOST'], $_ENV['BBDD_USER'], $_ENV['BBDD_PASS'], $_ENV['BBDD_DATABASE_CONFIG'], $_ENV['BBDD_PORT']);
        $selector = new GestorSelectorMysql($database);


        $BBDDSelector = new ObtenerSeleccion(
            $selector,
            $this->parametros["NombreMayorista"]
        );

        $parametrosBBDD = $BBDDSelector();

        $dbName = $parametrosBBDD["conexion"];

        $NombreTabla1 = $parametrosBBDD["tabla1"];

        $NombreTabla2 = $parametrosBBDD["tabla2"];

        $partes = explode('.', $this->parametros["NombreFile"]);
        $resultado = $partes[0];
        $extension = $partes[1];

        $nombreArchivoDescargar = $this->parametros['token'] . ".ods";
        $gestorArchivo = new GestorArchivosOds($this->rutaFiles, $nombreArchivoDescargar);
        if (!file_exists($this->rutaFiles . $nombreArchivoDescargar)) {
            $equivalenciasDao = new EquivalenciasDAOMysql($database, $NombreTabla1, $dbName);
            $comprobarActiva = new ComprobarActiva($database, $NombreTabla2, $dbName);
            $rutaArchivoCompleto = $this->rutaFiles . $this->parametros["NombreFile"];
            $gestorEstablecimientos = null;
            if ($extension == "csv") {
                $gestorEstablecimientos = new GestorEstablecimientosCSV($rutaArchivoCompleto);
            } elseif ($extension == "ods") {
                $gestorEstablecimientos = new GestorEstablecimientosODS($rutaArchivoCompleto);
            } elseif ($extension == "xlsx") {
                $gestorEstablecimientos = new GestorEstablecimientosXLSX($rutaArchivoCompleto);
            } else {
                throw new \Exception('Tipo de archivo no valido');
            }
            while (!file_exists($rutaArchivoCompleto)) {
                sleep(1);
            }
            $filtros = $this->parametros;
            $obtenerEquivalencias = new ObtenerEstadoEquivalencias(

                $gestorEstablecimientos,
                $equivalenciasDao,
                $comprobarActiva,
                $filtros
            );
            $datos = $obtenerEquivalencias();
            unset($obtenerEquivalencias);

            $gestorArchivo->crearArchivo($datos);
        }
        $gestorArchivo->descargarArchivo($this->rutaFiles, $nombreArchivoDescargar);
    }
}