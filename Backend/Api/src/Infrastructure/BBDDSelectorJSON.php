<?php
namespace ComprobadorEquivalencias\Infrastructure;
class BBDDSelectorJSON
{
    private $BBDDCorrespondencias;
    /**
     * __construct
     *
     * @return void
     */
    public function __construct()
    {
        $json=file_get_contents("/usr/local/programadores/ComprobarEquivalencia/Backend/Api/tablas.json");
        $this->BBDDCorrespondencias=json_decode($json);
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
        var_dump($respuesta);
        return $respuesta;
    }

}