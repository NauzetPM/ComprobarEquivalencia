<?php
//DatosHoteles
class DatosHoteles
{
    public $Codigo;
    public $Nombre;
    public $Estado;

}

//Database
$host = "localhost";
$user = "npestano";
$pass = "veturis2024";
$dbName = "comprobadorEquivalente";
$port = "3306";
try {
    $pdo = new PDO("mysql:host=" . $host . "; port=" . $port . ";dbname=" . $dbName, $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    print "    <p class=\"aviso\">Error: No puede conectarse con la base de datos. {$e->getMessage()}</p>\n";
    exit;
}


//Hoteles(bbdd)
//Metodo comprobar(csv)
$prepare = $pdo->prepare("SELECT * FROM Estado");
$prepare->execute();
$datosJSON = [];
while ($row = $prepare->fetch(PDO::FETCH_ASSOC)) {
    if (($gestor = fopen("/usr/local/programadores/Comprobador_Equivalencia/Backend/ejemplo.csv", "r")) !== FALSE) {
        while (($datos = fgetcsv($gestor, 1000, ",")) !== FALSE) {
            $numero = count($datos);
            for ($c = 0; $c < $numero; $c++) {
                if ($c == 0) {
                    if ($row["codigoHotel"] == $datos[$c]) {
                        $datosHotel = new DatosHoteles();
                        $datosHotel->Codigo = $datos[0];
                        $datosHotel->Nombre = $datos[1];
                        if ($row["usuario"] == "Block") {
                            $datosHotel->Estado = "Mapeado Block";
                            $datosJSON[] = $datosHotel;
                        } else {
                            $datosHotel->Estado = "Mapeado";
                            $datosJSON[] = $datosHotel;
                        }
                    }
                }
            }
        }
    }

    fclose($gestor);
}


if (($gestor = fopen("/usr/local/programadores/Comprobador_Equivalencia/Backend/ejemplo.csv", "r")) !== FALSE) {
    while (($datos = fgetcsv($gestor, 1000, ",")) !== FALSE) {
        $noEsta = true;
        for ($i = 0; $i < count($datosJSON); $i++) {
            for ($c = 0; $c < $numero; $c++) {
                if ($c == 0) {
                    if ($datosJSON[$i]->Codigo == $datos[0]) {
                        $noEsta = false;
                    }
                }
            }

        }
        if ($noEsta) {
            $datosHotel = new DatosHoteles();
            $datosHotel->Codigo = $datos[0];
            $datosHotel->Nombre = $datos[1];
            $datosHotel->Estado = "Pendiente";
            $datosJSON[] = $datosHotel;
        }
    }
}

fclose($gestor);


print_r(json_encode($datosJSON));






// Leer fichero csv 
/*$fila = 1;
if (($gestor = fopen("/usr/local/programadores/Comprobador_Equivalencia/Backend/ejemplo.csv", "r")) !== FALSE) {
    while (($datos = fgetcsv($gestor, 1000, ",")) !== FALSE) {
        $numero = count($datos);
        echo "<p> $numero de campos en la línea $fila: <br /></p>\n";
        $fila++;
        for ($c=0; $c < $numero; $c++) {
            if($c==0){
                echo "Codigo Hotel: ". $datos[$c];
            }else if($c==1){
                echo "Nombre Hotel: ". $datos[$c];
            }
            //echo $datos[$c] . "<br />\n";
            echo "<br/>\n";
        }
    }
    fclose($gestor);
}
*/



/*  Consulta BBDD
$host = "localhost";
$user = "npestano";
$pass = "veturis2024";
$dbName = "comprobadorEquivalente";
$port="3306";
try {
    $pdo = new PDO("mysql:host=". $host . "; port=" . $port . ";dbname=" . $dbName, $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    print "    <p class=\"aviso\">Error: No puede conectarse con la base de datos. {$e->getMessage()}</p>\n";
    exit;
}
echo "Adios";
$prepare=$pdo->prepare("SELECT * FROM Hoteles");
$prepare->execute();
while($row = $prepare->fetch(PDO::FETCH_ASSOC)){
    print_r($row);
}
*/

