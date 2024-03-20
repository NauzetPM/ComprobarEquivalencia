<?php
namespace ComprobadorEquivalencias\Domain;

class DatosHoteles
{
    private string $codigo;
    private string $nombre;
    private string $estado;
    private string $activa;
    const ESTADO_PENDIENTE = "Pendiente";
    const ESTADO_MAPEADO = "Mapeado";
    const ESTADO_BLOCK = "Mapeado Block";

    const Activa = "Si";

    const No_Aciva = "No";

    const No_Descargada = "No descargada";

    /**
     * __construct
     *
     * @param  string $codigo
     * @param  string $nombre
     * @param  string $estado
     * @param string $activa
     */
    public function __construct(
        string $codigo,
        string $nombre,
        string $estado,
        string $activa
    ) {
        $this->codigo = $codigo;
        $this->nombre = $nombre;
        $this->estado = $estado;
        $this->activa = $activa;
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
            "Estado" => $this->estado,
            "Activa" => $this->activa,
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