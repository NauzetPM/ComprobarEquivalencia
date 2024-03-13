<?php
namespace ComprobadorEquivalencias\Infrastructure;
use ComprobadorEquivalencias\Domain\GestorSelector;

class BBDDSelector implements GestorSelector
{
    private $correspondencias;
    /**
     * __construct
     *
     * @return void
     */
    public function __construct()
    {
        $this->correspondencias = array();
        $this->correspondencias[] = array(
            "Nombre" => "Expedia",
            "conexion" => "accionviajesExpedia",
            "tabla" => "equivalenciaestablecimientos_expediarapid",
        );
        $this->correspondencias[] = array(
            "Nombre" => "Cntravel",
            "conexion" => "accionviajesCntravel",
            "tabla" => "equivalenciaestablecimientos_cntravel",
        );
    }
    /**
     * obtenerCorrespondencias
     *
     * @param  string $nombre
     * @return array
     */
    public function obtenerCorrespondencias(string $nombre): array
    {
        $respuesta = [];
        foreach ($this->correspondencias as $key => $value) {
            if ($nombre == $value["Nombre"]) {
                $respuesta = $value;
            }
        }
        return $respuesta;
    }

}