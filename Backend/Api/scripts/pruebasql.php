<?php
require(__DIR__ . "/../vendor/autoload.php");
use ComprobadorEquivalencias\Infrastructure\Database;

$host = "localhost";
$user = "npestano";
$pass = "veturis2024";
$dbName = "accionviajesCntravel";
$dbPort = "3306";
$tabla = "equivalenciaestablecimientos_cntravel";
$database = new Database($host, $user, $pass, $dbName, $dbPort);
$pdo = $database->connection;
$codigo = "0000002110";
$stmt = $pdo->prepare(
    "SELECT COUNT(*) as total, usuario "
    . " FROM " . $tabla
    . " WHERE codigo = :codigo"
    . " GROUP BY usuario"
);
$stmt->bindParam(':codigo', $codigo);
$stmt->execute();
$result = $stmt->fetch(PDO::FETCH_ASSOC);

$consulta = $pdo->query(    
"SELECT COUNT(*) as total, usuario ,codigo"
. " FROM " . $tabla
. " WHERE codigo = '0000000399'"
. " GROUP BY usuario,codigo"
);
$result = $consulta->fetch(PDO::FETCH_ASSOC);

var_dump($result);