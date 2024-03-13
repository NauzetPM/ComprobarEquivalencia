<?php
namespace ComprobadorEquivalencias\Infrastructure;

use ComprobadorEquivalencias\Application\ObtenerEstadisticas;
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
    private $rutaCSV = "/usr/local/programadores/ComprobarEquivalencia/Backend/Api/files/";
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

        $this->rutaArchivoCompleto = $this->rutaCSV . $NombreArchivo;

        $this->gestorFicheroCSV = new GestorEstablecimientosCSV($this->rutaArchivoCompleto);

        $this->equivalenciasDao = new EquivalenciasDAOMysql($this->database, $NombreTabla);

        $this->estadisticas = new GestorEstadisticasCSV($this->database, $this->rutaArchivoCompleto, $NombreTabla);

        $this->descargarArchivo = new DescargarArchivo();

        $this->gestorArchivo = new GestorArchivosOds('/usr/local/programadores/ComprobarEquivalencia/Backend/Api/files/datos_Estados.ods');

        $tiempoLimite = 60;

        $tiempoInicio = time();

        while (!file_exists($this->rutaArchivoCompleto)) {

            sleep(1);

            if ((time() - $tiempoInicio) > $tiempoLimite) {

                throw new \Exception("Tiempo de espera agotado. El archivo no está disponible.");

            }
        }
    }


    /**
     * comprobarFichero
     *
     * @return array
     */
    public function comprobarFichero(): array
    {

        $page = isset($this->parametros['page']) ? intval($this->parametros['page']) : 1;
        $perPage = isset($this->parametros['perPage']) ? intval($this->parametros['perPage']) : 100;
        $filtros = $this->parametros;
        $caso_de_uso = new ObtenerEstadoEquivalencias(
            $page,
            $perPage,
            $this->gestorFicheroCSV,
            $this->equivalenciasDao,
            $filtros
        );
        $datos = $caso_de_uso();
        $this->gestorArchivo->crearArchivoOds($datos);

        return $datos;
    }
    /**
     * descargar
     *
     * @return void
     */
    public function descargar()
    {
        $this->descargarArchivo->descargarArchivo("datos_Estados.ods");
    }
    /**
     * obtenerEstadisticas
     *
     * @return array
     */
    public function obtenerEstadisticas(): array
    {
        $caso_de_uso = new ObtenerEstadisticas(
            $this->estadisticas,
        );

        return $caso_de_uso();
    }
}