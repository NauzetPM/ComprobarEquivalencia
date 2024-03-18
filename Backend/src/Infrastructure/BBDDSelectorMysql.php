<?php
namespace ComprobadorEquivalencias\Infrastructure;

use ComprobadorEquivalencias\Domain\GestorSelector;
use PDO;
class BBDDSelectorMysql implements GestorSelector
{
    private $pdo;
    
    /**
     * __construct
     *
     * @param  Database $db
     * @return void
     */
    public function __construct(Database $db)
    {
        $this->pdo = $db->connection;
    }
    /**
     * obtenerCorrespondencias
     *
     * @param  string $nombre
     * @return array
     */
    public function obtenerCorrespondencias(string $nombre): array
    {   
        //añadir la otra tabla
        $sql="SELECT conexion,tabla1,tabla2 " 
        ." FROM EquivalenciasEstablecimientos " 
        ." WHERE mayorista = :mayorista";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':mayorista', $nombre);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result;
    }

}