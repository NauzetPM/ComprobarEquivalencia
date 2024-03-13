<?php
namespace ComprobadorEquivalencias\Infrastructure;

class BBDDSelector
{
    private $BBDDCorrespondencias;
    /**
     * __construct
     *
     * @return void
     */
    public function __construct()
    {
        $this->BBDDCorrespondencias = array();
        $this->BBDDCorrespondencias[] = array(
            "Nombre" => "Expedia",
            "BBDD" => "accionviajesExpedia",
            "Tabla" => "equivalenciaestablecimientos_expediarapid",
        );
        $this->BBDDCorrespondencias[] = array(
            "Nombre" => "Cntravel",
            "BBDD" => "accionviajesCntravel",
            "Tabla" => "equivalenciaestablecimientos_cntravel",
        );
    }
    /**
     * obtenerCorrespondencias
     *
     * @param  mixed $nombre
     * @return array
     */
    public function obtenerCorrespondencias(string $nombre): array
    {
        $respuesta = [];
        foreach ($this->BBDDCorrespondencias as $key => $value) {
            if ($nombre == $value["Nombre"]) {
                $respuesta = $value;
            }
        }
        return $respuesta;
    }

}