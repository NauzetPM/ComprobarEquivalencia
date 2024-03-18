<?php
namespace ComprobadorEquivalencias\Infrastructure;

ini_set('memory_limit', '256M');
use ComprobadorEquivalencias\Application\ObtenerEstadoEquivalencias;
use ComprobadorEquivalencias\Application\ObtenerSeleccion;
use ComprobadorEquivalencias\Infrastructure\Database;
use ComprobadorEquivalencias\Infrastructure\EquivalenciasDAOMysql;
use ComprobadorEquivalencias\Infrastructure\GestorEstablecimientosCSV;
use ComprobadorEquivalencias\Infrastructure\FileCleaner;
use ComprobadorEquivalencias\Infrastructure\CacheManager;


class Controlador
{
    private $host = "localhost";
    private $user = "npestano";
    private $pass = "veturis2024";
    private $dbName;
    private $dbPort = "3306";
    private array $parametros;
    private $rutaFiles = "/usr/local/programadores/ComprobarEquivalencia/Backend/files/";
    private $database;
    private $equivalenciasDao;
    private $gestorFicheroCSV;
    private $estadisticas;

    private $comprobarActiva;

    private $selector;

    private $descargarArchivo;

    private $gestorArchivo;

    private $rutaArchivoCompleto;

    private $nombreArchivoDescargar;

    private $cacheManager;
    private $rutaCache="/usr/local/programadores/ComprobarEquivalencia/Backend/cache/";

    /**
     * __construct
     *
     * @param  array $parametros
     * @return void
     */
    public function __construct(array $parametros)
    {
        $this->cacheManager = new CacheManager($this->rutaCache);

        $this->parametros = $parametros;
        $limpiadorFiles = new FileCleaner();
        $limpiadorFiles->cleanOldFiles($this->rutaFiles);
        $limpiadorFiles->cleanOldFiles($this->rutaCache);

        if (isset ($parametros["NombreEmpresa"])) {
            $dbSelectorName = "opciones";

            $databaseSelector = new Database($this->host, $this->user, $this->pass, $dbSelectorName, $this->dbPort);
            $this->selector = new BBDDSelectorMysql($databaseSelector);
            $caso_de_uso = new ObtenerSeleccion(
                $this->selector
            );

            $parametrosBBDD = $caso_de_uso($this->parametros["NombreEmpresa"]);

            $this->dbName = $parametrosBBDD["conexion"];

            $NombreTabla1 = $parametrosBBDD["tabla1"];

            $NombreTabla2 = $parametrosBBDD["tabla2"];

            $this->database = new Database($this->host, $this->user, $this->pass, $this->dbName, $this->dbPort);

            $NombreArchivo = $this->parametros["NombreFile"];

            $this->rutaArchivoCompleto = $this->rutaFiles . $NombreArchivo;
            $extension = $this->parametros['fileExtension'];
            if ($extension == "csv") {
                $this->gestorFicheroCSV = new GestorEstablecimientosCSV($this->rutaArchivoCompleto);
            } elseif ($extension == "ods") {
                $this->gestorFicheroCSV = new GestorEstablecimientosODS($this->rutaArchivoCompleto);
            } elseif ($extension == "xlsx") {
                $this->gestorFicheroCSV = new GestorEstablecimientosXLSX($this->rutaArchivoCompleto);
            } else {
                echo "Tipo de archivo no valido";
                die;
            }
            $partes = explode('.', $NombreArchivo);
            $resultado = $partes[0];
            $this->nombreArchivoDescargar = "datos_Estados" . $resultado . $extension . ".ods";
            $this->equivalenciasDao = new EquivalenciasDAOMysql($this->database, $NombreTabla1);

            $this->comprobarActiva = new ComprobarActiva($this->database, $NombreTabla2);

            $this->descargarArchivo = new DescargarArchivo();

            $this->gestorArchivo = new GestorArchivosOds($this->rutaFiles . $this->nombreArchivoDescargar);

        }
    }


    public function comprobarToken()
    {
        $token = $this->parametros['token'];
        if (isset ($token)) {
            $return = $this->cacheManager->comprobarToken($token);
            return $return;
        }
        return null;
    }
    /**
     * comprobarFichero
     *
     * @return void
     */
    public function comprobarFichero()
    {
        
        if (isset ($this->parametros['token'])) {
            $this->cacheManager->guardarToken($this->parametros['token'],$this->parametros['token']);
        }
    }
    /**
     * descargar
     *
     * @return void
     */
    public function descargar()
    {
        if (!file_exists($this->rutaFiles . $this->nombreArchivoDescargar)) {
            sleep(4);
            while (!file_exists($this->rutaArchivoCompleto)) {
                sleep(1);
            }
            $filtros = $this->parametros;
            $caso_de_uso = new ObtenerEstadoEquivalencias(

                $this->gestorFicheroCSV,
                $this->equivalenciasDao,
                $this->comprobarActiva,
                $filtros
            );
            $datos = $caso_de_uso();
            unset($caso_de_uso);

            $this->gestorArchivo->crearArchivoOds($datos);
            unset($datos);
        }
        $intentos = 0;
        $maxIntentos = 100;
        while ($intentos < $maxIntentos) {
            $archivo = fopen($this->rutaFiles . $this->nombreArchivoDescargar, "r");
            if ($archivo !== false) {
                fclose($archivo);
                sleep(5);
                break;
            }
            sleep(1);
            $intentos++;
        }
        $this->descargarArchivo->descargarArchivo($this->nombreArchivoDescargar);
    }
}