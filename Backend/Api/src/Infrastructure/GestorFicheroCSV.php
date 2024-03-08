<?php
namespace ComprobadorEquivalencias\Infrastructure;

class GestorFicheroCSV{
    private $csvfile;
    public function __construct($csvFile){
        $this->csvfile=$csvFile;
    }
    public function getDatos(){
        $datosCSV=array();
        if (($gestor = fopen($this->csvfile, "r")) !== FALSE) {
            while (($datos = fgetcsv($gestor, 1000, ",")) !== FALSE) {
                $datosCSV[]=$datos;
            }
        }
        fclose($gestor);
        //print_r($datosCSV);
        return $datosCSV;
    }
}