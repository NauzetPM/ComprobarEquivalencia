<?php
namespace ComprobadorEquivalencias\Infrastructure;
ini_set('memory_limit', '256M');
use ComprobadorEquivalencias\Application\ObtenerEstadoEquivalencias;
use ComprobadorEquivalencias\Application\ObtenerSeleccion;
use ComprobadorEquivalencias\Infrastructure\Database;
use ComprobadorEquivalencias\Infrastructure\EquivalenciasDAOMysql;
use ComprobadorEquivalencias\Infrastructure\GestorEstablecimientosCSV;



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

    private $selector;

    private $descargarArchivo;

    private $gestorArchivo;

    private $rutaArchivoCompleto;

    /**
     * __construct
     *
     * @param  array $parametros
     * @return void
     */
    public function __construct(array $parametros)
    {
        $this->parametros = $parametros;

        $dbSelectorName = "opciones";

        $databaseSelector = new Database($this->host, $this->user, $this->pass, $dbSelectorName, $this->dbPort);
        $this->selector = new BBDDSelectorMysql($databaseSelector);
        $caso_de_uso = new ObtenerSeleccion(
            $this->selector
        );

        $parametrosBBDD = $caso_de_uso($this->parametros["NombreEmpresa"]);

        $this->dbName = $parametrosBBDD["conexion"];

        $NombreTabla = $parametrosBBDD["tabla"];

        $this->database = new Database($this->host, $this->user, $this->pass, $this->dbName, $this->dbPort);

        $NombreArchivo = $this->parametros["NombreFile"];

        $this->rutaArchivoCompleto = $this->rutaFiles . $NombreArchivo;
        $extension=$this->parametros['fileExtension'];
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

        $this->equivalenciasDao = new EquivalenciasDAOMysql($this->database, $NombreTabla);



        $this->descargarArchivo = new DescargarArchivo();

        $this->gestorArchivo = new GestorArchivosOds($this->rutaFiles."datos_Estados.ods");
    }



    /**
     * comprobarFichero
     *
     * @return void
     */
    public function comprobarFichero()
    {

    }
    /**
     * descargar
     *
     * @return void
     */
    public function descargar() 
    {
        sleep(4);
        while (!file_exists($this->rutaArchivoCompleto)) {
            sleep(1);
        }
        $filtros = $this->parametros;
        $caso_de_uso = new ObtenerEstadoEquivalencias(

            $this->gestorFicheroCSV,
            $this->equivalenciasDao,
            $filtros
        );
        $datos = $caso_de_uso();
        unset($caso_de_uso);

        $this->gestorArchivo->crearArchivoOds($datos);
        $intentos = 0;
        $maxIntentos = 10;
        while ($intentos < $maxIntentos) {
            $archivo = fopen($this->rutaFiles . "datos_Estados.ods", "r");
            if ($archivo !== false) {
                fclose($archivo);
                break;
            }
            sleep(1);
            $intentos++;
        }
        unset($datos);
        $this->descargarArchivo->descargarArchivo("datos_Estados.ods");
    }
}