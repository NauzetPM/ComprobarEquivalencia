<?php
namespace ComprobadorEquivalencias\Infrastructure;

use ComprobadorEquivalencias\Domain\GestorEstadisticas;
use ComprobadorEquivalencias\Infrastructure\Database;
use PDO;

class Estadisticas implements GestorEstadisticas
{
    private $pdo;
    private $csvfile;
    private $lenght = 1000;
    private $tabla;
    /**
     * __construct
     *
     * @param  mixed $db
     * @param  mixed $csvFile
     * @param  mixed $tabla
     * @return void
     */
    public function __construct(Database $db, $csvFile, $tabla)
    {
        $this->pdo = $db->connection;
        $this->csvfile = $csvFile;
        $this->tabla = $tabla;
    }

    /**
     * getEstadisticas
     *
     * @return array
     */
    public function getEstadisticas(): array
    {
        $total = 0;
        $totalMapeados = 0;
        $totalMapeadosBlock = 0;
        $pendientes = 0;
        $sql = "SELECT COUNT(*) as total, usuario "
            . " FROM " . $this->tabla
            . " WHERE codigo = :codigo"
            . " GROUP BY usuario";
        if (($gestor = fopen($this->csvfile, "r")) !== FALSE) {
            while (($datos = fgetcsv($gestor, $this->lenght, ",")) !== FALSE) {
                $datosExpode = explode("|", $datos[0]);
                $codigo = $datosExpode[0];
                $stmt = $this->pdo->prepare($sql);
                $stmt->bindParam(':codigo', $codigo);
                $stmt->execute();
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                if ($result == false) {
                    $pendientes++;
                } elseif ($result['usuario'] == 'casamientoBlock') {
                    $totalMapeadosBlock += $result['total'];
                } else {
                    $totalMapeados += $result['total'];
                }
                $total++;
            }
        }
        fclose($gestor);

        return [
            'total' => $total,
            'mapeado' => $totalMapeados,
            'block' => $totalMapeadosBlock,
            'pendiente' => $pendientes
        ];
    }

}