<?php
namespace ComprobadorEquivalencias\Infrastructure;

use ComprobadorEquivalencias\Infrastructure\Database;
use ComprobadorEquivalencias\Infrastructure\EstadoDAO;
use ComprobadorEquivalencias\Infrastructure\GestorFicheroCSV;
use ComprobadorEquivalencias\Domain\ComprobadorEstado;
class Controlador{
    private $host = "localhost";
    private $user = "npestano";
    private $pass = "veturis2024";
    private $dbName = "comprobadorEquivalente";
    private $dbPort = "3306";
    private array $parametros;

    private $database;
    private $EstadoDao;
    private $GestorFicheroCSV;

    public function __construct(array $parametros)
    {
        $this->parametros = $parametros;
        $this->database=new Database($this->host,$this->user,$this->pass,$this->dbName,$this->dbPort);
        if (isset($parametros['file'])) {
        $this->GestorFicheroCSV=new GestorFicheroCSV($parametros['file']);
        }
        $this->EstadoDao=new EstadoDAO($this->database);
    }


    public function comprobarFichero(){
        $datosdb=$this->EstadoDao->getAll();
        $datosCSV=$this->GestorFicheroCSV->getDatos();
        $Comprobador=new ComprobadorEstado($datosdb,$datosCSV);
        $JSONEstados=$Comprobador->getEstados();
        return $JSONEstados;
    }
}