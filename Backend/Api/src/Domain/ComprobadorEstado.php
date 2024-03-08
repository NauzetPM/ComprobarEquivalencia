<?php
namespace ComprobadorEquivalencias\Domain;

class ComprobadorEstado
{
    private $datosDB;
    private $datosCSV;
    public function __construct($datosDB, $datosCSV)
    {
        $this->datosDB = $datosDB;
        $this->datosCSV = $datosCSV;
    }
    public function getEstados()
    {
        //print_r($this->datosDB);
        $datosJSON = array();
        for ($i = 0; $i < count($this->datosDB); $i++) {
            //Recorro base de datos arriba
            for ($c = 0; $c < count($this->datosCSV); $c++) { //array({codigoHotel,Nombre},{codigoHotel,Nombre})
                //recorro csv arriba
                if ($this->datosDB[$i]["codigoHotel"] == $this->datosCSV[$c][0]) {
                    $datosHotel = new DatosHoteles();
                    $datosHotel->Codigo = $this->datosCSV[$c][0];
                    $datosHotel->Nombre = $this->datosCSV[$c][1];
                    if ($this->datosDB[$i]["usuario"] == "Block") {
                        $datosHotel->Estado = "Mapeado Block";
                        $datosJSON[] = $datosHotel;
                    } else {
                        $datosHotel->Estado = "Mapeado";
                        $datosJSON[] = $datosHotel;
                    }
                }
            }

        }

        for ($c = 0; $c < count($this->datosCSV); $c++) { //array({codigoHotel,Nombre},{codigoHotel,Nombre})
            //recorro csv arriba
            $noEsta = true;
            for ($i = 0; $i < count($datosJSON); $i++) {
                    if ($c == 0) {
                        //print_r("CodigoJson->>>>".$datosJSON[$i]->Codigo."****");
                        //print_r("CodigoCSV->>>>>>>>".$this->datosCSV[$c][0]."//////////");
                        if ($datosJSON[$i]->Codigo == $this->datosCSV[$c][0]) {
                            $noEsta = false;
                        }
                }

            }
            if ($noEsta) {
                $datosHotel = new DatosHoteles();
                $datosHotel->Codigo = $this->datosCSV[$c][0];
                $datosHotel->Nombre = $this->datosCSV[$c][1];
                $datosHotel->Estado = "Pendiente";
                $datosJSON[] = $datosHotel;
            }
        }


        return $datosJSON;
    }

}