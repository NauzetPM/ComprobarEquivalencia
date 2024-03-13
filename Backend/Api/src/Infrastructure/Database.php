<?php
namespace ComprobadorEquivalencias\Infrastructure;

use PDO;
use PDOException;

class Database
{

    private $host;
    private $user;
    private $pass;
    private $dbName;
    private $dbPort;


    public $connection;

    /**
     * __construct
     *
     * @param  string $host
     * @param  string $user
     * @param  string $pass
     * @param  string $dbName
     * @param  string $dbPort
     * @return void
     */
    public function __construct(string $host, string $user, string $pass, string $dbName, string $dbPort)
    {
        $this->host = $host;
        $this->user = $user;
        $this->pass = $pass;
        $this->dbName = $dbName;
        $this->dbPort = $dbPort;
        $this->connection = $this->getConnection();

    }

    /**
     * getConnection
     *
     * @return PDO
     */
    private function getConnection(): PDO
    {
        try {
            $pdo = new PDO("mysql:host=" . $this->host . "; port=" . $this->dbPort . ";dbname=" . $this->dbName, $this->user, $this->pass);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $pdo;
        } catch (PDOException $e) {
            print "Error: No puede conectarse con la base de datos. {$e->getMessage()}\n";
            exit;
        }
    }
}
?>