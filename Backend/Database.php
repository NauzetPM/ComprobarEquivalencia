<?php
class Database{

    private $host = "localhost";
    private $user = "npestano";
    private $pass = "veturis2024";
    private $dbName = "comprobadorEquivalente";
    private $port = "3306";

public function getConnection(){ 

    
    try {
        $pdo = new PDO("mysql:host=" . $this->host . "; port=" . $this->port . ";dbname=" . $this->dbName, $this->user, $this->pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    } catch (PDOException $e) {
        print "    <p class=\"aviso\">Error: No puede conectarse con la base de datos. {$e->getMessage()}</p>\n";
        exit;
    }
}
}
?>