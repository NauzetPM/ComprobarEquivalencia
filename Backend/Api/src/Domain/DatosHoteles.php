<?php
namespace ComprobadorEquivalencias\Domain;

class DatosHoteles
{
    private $Codigo;
    private $Nombre;
    private $Estado;
    const ESTADO_PENDIENTE = "Pendiente";
    const ESTADO_MAPEADO = "Mapeado";
    const ESTADO_BLOCK = "Mapeado Block";

    /**
     * __construct
     *
     * @param  mixed $Codigo
     * @param  mixed $Nombre
     * @param  mixed $Estado
     * @return void
     */
    public function __construct($Codigo, $Nombre, $Estado)
    {
        $this->Codigo = $Codigo;
        $this->Nombre = $Nombre;
        $this->Estado = $Estado;
    }
    /**
     * asArray
     *
     * @return array
     */
    public function asArray(): array
    {
        return [
            "Codigo" => $this->Codigo,
            "Nombre" => $this->Nombre,
            "Estado" => $this->Estado
        ];
    }

    /**
     * @return mixed
     */
    public function getCodigo()
    {
        return $this->Codigo;
    }

    /**
     * @return mixed
     */
    public function getNombre()
    {
        return $this->Nombre;
    }

    /**
     * @return mixed
     */
    public function getEstado()
    {
        return $this->Estado;
    }


}