<?php

declare(strict_types=1);

namespace ComprobadorEquivalencias\Domain;

class DatosHoteles
{
    private string $codigo;
    private string $nombre;
    private string $estado;
    private bool $activa;
    const USUARIO_MAPEADO_BLOCK = "casamientoBlock";
    const ESTADO_PENDIENTE = "Pendiente";
    const ESTADO_MAPEADO = "Mapeado";
    const ESTADO_BLOCK = "Mapeado Block";
    const NO_DESCARGADA = "No descargada";

    const ESTADO_MAPEADO_NO_ACTIVO = "noActivaMapeado";
    const ESTADO_MAPEADO_ACTIVO = "activaMapeado";
    const ESTADO_MAPEADO_BLOCK_NO_ACTIVO = "noActivaBlock";
    const ESTADO_MAPEADO_BLOCK_ACTIVO = "activaBlock";

    const ESTADO_PENDIENTE_NO_ACTIVO = "noActivaPendiente";
    const ESTADO_PENDIENTE_ACTIVO = "activaPendiente";
    /**
     *
     * @param  string $codigo
     * @param  string $nombre
     * @param  string $estado
     * @param bool $activa
     */
    public function __construct(
        string $codigo,
        string $nombre,
        string $estado,
        bool $activa
    ) {
        $this->codigo = $codigo;
        $this->nombre = $nombre;
        $this->estado = $estado;
        $this->activa = $activa;
    }
    /**
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
