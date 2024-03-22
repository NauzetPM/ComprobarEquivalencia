<?php

namespace ComprobadorEquivalencias\Infrastructure;

use ComprobadorEquivalencias\Domain\GestorSelector;
use PDO;

class GestorSelectorMysql implements GestorSelector
{
    private PDO $pdo;

    /**
     *
     * @param  Database $db
     */
    public function __construct(Database $db)
    {
        $this->pdo = $db->connection;
    }
    /**
     *
     * @param  string $nombre
     * @return array
     */
    public function obtenerCorrespondencias(string $nombre): array
    {
        $sql = "SELECT conexion,tabla1,tabla2 "
            . " FROM EquivalenciasEstablecimientos "
            . " WHERE mayorista = :mayorista";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':mayorista', $nombre);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($result === false) {
            $result = [];
        }
        return $result;
    }
}
