<?php
namespace ComprobadorEquivalencias\Infrastructure;

class FileCleaner
{

    /**
     * __construct
     *
     */
    public function __construct()
    {
    }

    /**
     * cleanOldFiles
     *
     * @param  string $directory
     * @return void
     */
    public function cleanOldFiles(string $directory): void
    {
        //$fecha_limite = strtotime('-3 hours');
        //$fecha_limite = strtotime('-3 days');
        $fecha_limite = strtotime('-3 minutes');
        $archivos = scandir($directory);

        foreach ($archivos as $archivo) {
            $ruta = $directory . '/' . $archivo;
            if (is_file($ruta) && filemtime($ruta) < $fecha_limite) {
                unlink($ruta);
            }
        }
        sleep(1);
    }
}