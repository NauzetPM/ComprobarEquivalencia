<?php
namespace ComprobadorEquivalencias\Infrastructure;

use ComprobadorEquivalencias\Application\ObtenerEstadisticas;
use ComprobadorEquivalencias\Application\ObtenerEstadoEquivalencias;
use ComprobadorEquivalencias\Infrastructure\Database;
use ComprobadorEquivalencias\Infrastructure\EquivalenciasDAOMysql;
use ComprobadorEquivalencias\Infrastructure\GestorEstablecimientosCSV;
use ComprobadorEquivalencias\Infrastructure\Estadisticas;
use ComprobadorEquivalencias\Infrastructure\BBDDSelector;

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
    private $EquivalenciasDao;
    private $GestorFicheroCSV;
    private $Estadisticas;

    private $BBDDSelector;

    private $rutaArchivoCompleto;

    /**
     * __construct
     *
     * @param  mixed $parametros
     * @return void
     */
    public function __construct(array $parametros)
    {
        $this->parametros = $parametros;
        $this->BBDDSelector = new BBDDSelector();
        
        /*$prueba=new BBDDSelectorJSON();
        var_dump($prueba->obtenerCorrespondencias($this->parametros["NombreEmpresa"]));*/

        $parametrosBBDD = $this->BBDDSelector->obtenerCorrespondencias($this->parametros["NombreEmpresa"]);
        $this->dbName = $parametrosBBDD["BBDD"];
        $NombreTabla = $parametrosBBDD["Tabla"];
        $this->database = new Database($this->host, $this->user, $this->pass, $this->dbName, $this->dbPort);
        $NombreArchivo = $this->parametros["NombreFile"];
        $this->rutaArchivoCompleto = $this->rutaCSV . $NombreArchivo;
        $this->GestorFicheroCSV = new GestorEstablecimientosCSV($this->rutaArchivoCompleto);
        $this->EquivalenciasDao = new EquivalenciasDAOMysql($this->database, $NombreTabla);
        $this->Estadisticas = new Estadisticas($this->database, $this->rutaArchivoCompleto, $NombreTabla);
        $tiempoLimite = 60; // Establece el tiempo límite en segundos
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
            $this->GestorFicheroCSV,
            $this->EquivalenciasDao,
            $filtros
        );

        return $caso_de_uso();
    }
    /**
     * obtenerEstadisticas
     *
     * @return array
     */
    public function obtenerEstadisticas(): array
    {
        $caso_de_uso = new ObtenerEstadisticas(
            $this->Estadisticas,
        );

        return $caso_de_uso();
    }
}