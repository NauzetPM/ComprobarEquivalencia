<?php
namespace ComprobadorEquivalencias\Domain;

class DatosHoteles
{
    private $codigo;
    private $nombre;
    private $estado;
    const ESTADO_PENDIENTE = "Pendiente";
    const ESTADO_MAPEADO = "Mapeado";
    const ESTADO_BLOCK = "Mapeado Block";

    /**
     * __construct
     *
     * @param  string $codigo
     * @param  string $nombre
     * @param  string $estado
     * @return void
     */
    public function __construct(string $codigo,string $nombre,string $estado)
    {
        $this->codigo = $codigo;
        $this->nombre = $nombre;
        $this->estado = $estado;
    }
    /**
     * asArray
     *
     * @return array
     */
    public function asArray(): array
    {
        return [
            "Codigo" => $this->codigo,
            "Nombre" => $this->nombre,
            "Estado" => $this->estado
        ];
    }

    /**
     * @return string
     */
    public function getCodigo()
    {
        return $this->codigo;
    }

    /**
     * @return string
     */
    public function getNombre()
    {
        return $this->nombre;
    }

    /**
     * @return string
     */
    public function getEstado()
    {
        return $this->estado;
    }


}