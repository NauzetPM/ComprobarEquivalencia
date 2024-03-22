<?php

namespace ComprobadorEquivalencias\Infrastructure;

use ComprobadorEquivalencias\Domain\EquivalenciasDAO;
use PDO;

class EquivalenciasDAOMysql implements EquivalenciasDAO
{
    private string $tabla;
    private PDO $pdo;
    private string $db;

    /**
     *
     * @param  Database $db
     * @param  string $tabla
     * @param  string $dbName
     */
    public function __construct(Database $db, string $tabla, string $dbName)
    {
        $this->tabla = $tabla;
        $this->pdo = $db->connection;
        $this->db = $dbName;
    }

    /**
     *
     * @return array
     */
    public function getAll(): array
    {
        $sql = "SELECT * FROM " . $this->db . "." . $this->tabla;
        $prepare = $this->pdo->prepare($sql);
        $prepare->execute();
        $datos = array();
        while ($row = $prepare->fetch(PDO::FETCH_ASSOC)) {
            $datos[] = $row;
        }
        return $datos;
    }
    /**
     *
     * @param  string $codigo
     * @return array
     */
    public function comprobarEstado(string $codigo): array
    {
        $sql = "SELECT COUNT(*) as total,usuario "
            . " FROM " . $this->db . "." . $this->tabla . " "
            . " WHERE codigo = :codigo "
            . " GROUP BY usuario";
        $prepare = $this->pdo->prepare($sql);
        $prepare->bindParam(':codigo', $codigo);
        $prepare->execute();
        $datos = $prepare->fetch(PDO::FETCH_ASSOC);
        if ($datos == false) {
            $datos = [
                "total" => 0,
            ];
        }
        return $datos;
    }
}
