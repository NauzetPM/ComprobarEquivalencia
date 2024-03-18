<?php
namespace ComprobadorEquivalencias\Infrastructure;
class FileCleaner {

    public function __construct() {
    }

    public function cleanOldFiles($directory) {
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