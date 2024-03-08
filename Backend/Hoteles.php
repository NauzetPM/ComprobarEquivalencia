<?php
include('/usr/local/programadores/ComprobarEquivalencia/Backend/DatosHoteles.php');

class Hoteles
{
    private $conn;

    function __construct($db)
    {
        $this->conn = $db;
    }
    //Se le pasa la ruta
    function comprobar($rutaCSV)
    {
        $prepare = $this->conn->prepare("SELECT * FROM Estado");
        $prepare->execute();
        $datosJSON = [];
        while ($row = $prepare->fetch(PDO::FETCH_ASSOC)) {
            if (($gestor = fopen($rutaCSV, "r")) !== FALSE) {
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


        if (($gestor = fopen($rutaCSV, "r")) !== FALSE) {
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
        return $datosJSON;
    }
}