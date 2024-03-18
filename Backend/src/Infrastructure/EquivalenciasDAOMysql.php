<?php
namespace ComprobadorEquivalencias\Infrastructure;

use ComprobadorEquivalencias\Domain\EquivalenciasDAO;
use PDO;

class EquivalenciasDAOMysql implements EquivalenciasDAO
{
    private $tabla;
    private $pdo;
    /**
     * __construct
     *
     * @param  Database $db
     * @param  string $tabla
     * @return void
     */
    public function __construct(Database $db, string $tabla)
    {
        $this->tabla = $tabla;
        $this->pdo = $db->connection;
    }

    /**
     * getAll
     *
     * @return array
     */
    public function getAll(): array
    {
        $sql = "SELECT * FROM " . $this->tabla;
        $prepare = $this->pdo->prepare($sql);
        $prepare->execute();
        $datos = array();
        while ($row = $prepare->fetch(PDO::FETCH_ASSOC)) {
            $datos[] = $row;
        }
        return $datos;
    }
    /**
     * comprobarEstado
     *
     * @param  string $codigo
     * @return array
     */
    public function comprobarEstado(string $codigo): array
    {
        $sql = "SELECT COUNT(*) as total,codigo,usuario "
            . " FROM " . $this->tabla . " "
            . " WHERE codigo = :codigo "
            . "GROUP BY codigo, usuario";
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