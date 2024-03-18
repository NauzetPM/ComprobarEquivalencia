<?php
namespace ComprobadorEquivalencias\Infrastructure;

use ComprobadorEquivalencias\Application\ObtenerEstadoEquivalencias;
use ComprobadorEquivalencias\Application\ObtenerSeleccion;
use Dotenv\Dotenv;
$dotenv = Dotenv::createImmutable(__DIR__ . '/../../');
$dotenv->load();
class Controlador
{
    private array $parametros;
    private $database;
    private $cacheManager;
    private $NombreTabla1;
    private $NombreTabla2;
    private $dbName;
    private $rutaCache = __DIR__ ."/../../cache/";

    private $rutaFiles = __DIR__ ."/../../files/";

    /**
     * __construct
     *
     * @param  array $parametros
     * @return void
     */
    public function __construct(array $parametros)
    {
        $this->cacheManager = new CacheManager($this->rutaCache);

        global $dotenv;
        
        $this->parametros = $parametros;
        $limpiadorFiles = new FileCleaner();
        $limpiadorFiles->cleanOldFiles($this->rutaFiles);
        $limpiadorFiles->cleanOldFiles($this->rutaCache);

        $this->database = new Database($_ENV['BBDD_HOST'], $_ENV['BBDD_USER'], $_ENV['BBDD_PASS'], $_ENV['BBDD_DATABASE_CONFIG'], $_ENV['BBDD_PORT']);
        $selector = new GestorSelectorMysql($this->database);
        $caso_de_uso = new ObtenerSeleccion(
            $selector
        );
        if (!isset ($this->parametros["NombreMayorista"])) {
            return ([
                "status" => "KO",
                "error" => "No se ha recivido Nombre Mayorista"
            ]);
        }
        $parametrosBBDD = $caso_de_uso($this->parametros["NombreMayorista"]);

        $this->dbName = $parametrosBBDD["conexion"];

        $this->NombreTabla1 = $parametrosBBDD["tabla1"];

        $this->NombreTabla2 = $parametrosBBDD["tabla2"];
    }

    
    /**
     * comprobarToken
     *
     * @return void
     */
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
            $this->cacheManager->guardarToken($this->parametros['token'], $this->parametros['token']);
        }
    }
    /**
     * descargar
     *
     * @return void
     */
    public function descargar()
    {
        if(!isset($this->parametros["NombreFile"])){
            return ([
                "status" => "KO",
                "error" => "No se ha recivido NombreFile"
            ]);
        }
        $equivalenciasDao = new EquivalenciasDAOMysql($this->database, $this->NombreTabla1,$this->dbName);
        $comprobarActiva = new ComprobarActiva($this->database, $this->NombreTabla2,$this->dbName);
        $descargarArchivo = new DescargarArchivo();

        $rutaArchivoCompleto = $this->rutaFiles . $this->parametros["NombreFile"];
        
        $partes = explode('.', $this->parametros["NombreFile"]);
        $resultado = $partes[0];
        $extension = $partes[1];
        $nombreArchivoDescargar = "datos_Estados" . $resultado . $extension . ".ods";
        $gestorArchivo = new GestorArchivosOds($this->rutaFiles . $nombreArchivoDescargar);
        $gestorFichero = null;
        if ($extension == "csv") {
            $gestorFichero = new GestorEstablecimientosCSV($rutaArchivoCompleto);
        } elseif ($extension == "ods") {
            $gestorFichero = new GestorEstablecimientosODS($rutaArchivoCompleto);
        } elseif ($extension == "xlsx") {
            $gestorFichero = new GestorEstablecimientosXLSX($rutaArchivoCompleto);
        } else {
            echo "Tipo de archivo no valido";
            die;
        }

        if (!file_exists($this->rutaFiles . $nombreArchivoDescargar)) {
            sleep(4);
            while (!file_exists($rutaArchivoCompleto)) {
                sleep(1);
            }
            $filtros = $this->parametros;
            $caso_de_uso = new ObtenerEstadoEquivalencias(

                $gestorFichero,
                $equivalenciasDao,
                $comprobarActiva,
                $filtros
            );
            $datos = $caso_de_uso();
            unset($caso_de_uso);

            $gestorArchivo->crearArchivoOds($datos);
            unset($datos);
        }
        $intentos = 0;
        $maxIntentos = 100;
        while ($intentos < $maxIntentos) {
            $archivo = fopen($this->rutaFiles . $nombreArchivoDescargar, "r");
            if ($archivo !== false) {
                fclose($archivo);
                sleep(5);
                break;
            }
            sleep(1);
            $intentos++;
        }
        $descargarArchivo->descargarArchivo($nombreArchivoDescargar);
    }
}