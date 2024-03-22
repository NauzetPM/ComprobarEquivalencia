<?php

namespace ComprobadorEquivalencias\Infrastructure;

class FileCleaner
{

    /**
     *
     */
    public function __construct()
    {
    }

    /**
     *
     * @param  string $directory
     * @return void
     */
    public function cleanOldFiles(string $directory): void
    {
        $fecha_limite = strtotime('-10 minutes');
        $archivos = scandir($directory);

        foreach ($archivos as $archivo) {
            $ruta = $directory . '/' . $archivo;
            if (is_file($ruta) && filemtime($ruta) < $fecha_limite) {
                unlink($ruta);
            }
        }
    }
}
