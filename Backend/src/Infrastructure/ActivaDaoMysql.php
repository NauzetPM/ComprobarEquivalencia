<?php

namespace ComprobadorEquivalencias\Infrastructure;

use ComprobadorEquivalencias\Domain\ActivaDao;
use ComprobadorEquivalencias\Domain\ActivaDaoDatos;
use PDO;

class  ActivaDaoMysql  implements ActivaDao
{
    private $tabla;
    private $pdo;

    private $db;


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
    public function comprobarDescargadaActiva(string $codigo): ActivaDaoDatos
    {
        $sql = "SELECT COUNT(*) as total,codigo,activo "
            . " FROM " . $this->db . "." . $this->tabla . " "
            . " WHERE codigo = :codigo "
            . " GROUP BY codigo, activo ";
        $prepare = $this->pdo->prepare($sql);
        $prepare->bindParam(':codigo', $codigo);
        $prepare->execute();
        $datos = $prepare->fetch(PDO::FETCH_ASSOC);
        if ($datos == false) {
            $datos = [
                "total" => 0,
            ];
        }
        $datosDevolver = ActivaDaoDatos::fromArray($datos);
        return $datosDevolver;
    }
}
