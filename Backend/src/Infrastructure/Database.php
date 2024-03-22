<?php

namespace ComprobadorEquivalencias\Infrastructure;

use PDO;
use PDOException;

class Database
{

    private string $host;
    private string $user;
    private string $pass;
    private string $dbName;
    private string $dbPort;


    public PDO $connection;

    /**
     *
     * @param  string $host
     * @param  string $user
     * @param  string $pass
     * @param  string $dbName
     * @param  string $dbPort
     * @throws \Exception
     */
    public function __construct(
        string $host,
        string $user,
        string $pass,
        string $dbName,
        string $dbPort
    ) {
        $this->host = $host;
        $this->user = $user;
        $this->pass = $pass;
        $this->dbName = $dbName;
        $this->dbPort = $dbPort;
        $this->connection = $this->getConnection();
    }

    /**
     *
     * @throws \Exception
     * @return PDO
     */
    private function getConnection(): PDO
    {
        try {
            $pdo = new PDO(
                "mysql:host=" . $this->host .
                    "; port=" . $this->dbPort .
                    ";dbname=" . $this->dbName,
                $this->user,
                $this->pass
            );
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $pdo;
        } catch (PDOException $e) {
            throw new \Exception("Error: No puede conectarse con la base de datos. {$e->getMessage()}\n");
        }
    }
}
