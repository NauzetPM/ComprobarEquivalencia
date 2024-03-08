<?php
namespace ComprobadorEquivalencias\Infrastructure;

use ComprobadorEquivalencias\Infrastructure\Database;
use PDO;

class EstadoDAO{
    private $pdo;
    public function __construct(Database $db){
        $this->pdo=$db->connection;
    }

    public function getAll(){
        $prepare = $this->pdo->prepare("SELECT * FROM Estado");
        $prepare->execute();
        $datos=array();
        while ($row = $prepare->fetch(PDO::FETCH_ASSOC)) {
            $datos[]=$row;
        }
        return $datos;
    }
}