<?php
namespace ComprobadorEquivalencias\Domain;

abstract class GestorFicheroBase
{
    
    /**
     * crearArchivo
     *
     * @param  array $datos
     * @return void
     */
    abstract public function crearArchivo(array $datos): void;

    /**
     * esArchivoCreado
     *
     * @param  string $filePath
     * @param  string $nombreArchivo
     * @return bool
     */
    private function esArchivoCreado(string $filePath,string $nombreArchivo): bool
    {
        $intentos = 0;
        $maxIntentos = 100;
        while ($intentos < $maxIntentos) {
            $archivo = fopen($filePath . $nombreArchivo, "r");
            if ($archivo !== false) {
                fclose($archivo);
                sleep(5);
                return true;
            }
            sleep(1);
            $intentos++;
        }
        return false;
    }

    /**
     * descargarArchivo
     *
     * @throws \Exception
     * @param  string $fileRut
     * @param  string $nombreArchivo
     * @return void
     */
    public function descargarArchivo(string $fileRut, string $nombreArchivo)
    {

        if ($this->esArchivoCreado($fileRut, $nombreArchivo)) {
            $fileName = $nombreArchivo;
            $filePath = $fileRut . $fileName;
            if (!empty ($fileName) && file_exists($filePath)) {
                if ($this->esCSV($filePath)) {
                    $fileType = 'text/csv';
                } elseif ($this->esODS($filePath)) {
                    $fileType = 'application/vnd.oasis.opendocument.spreadsheet';
                } elseif ($this->esXLSX($filePath)) {
                    $fileType = 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';
                } else {
                    throw new \Exception('Tipo de archivo no valido para descargar');
                }

                // Configurar las cabeceras para forzar la descarga del archivo
                header("Content-Description: Descargar archivo");
                header('Content-Disposition: attachment; filename="' . $fileName . '"');
                header("Content-Type: $fileType");
                header("Content-Length: " . filesize($filePath));
                header("Content-Transfer-Encoding: binary");

                readfile($filePath);
                exit;
            } else {
                throw new \Exception('El archivo no existe');
            }
        } else {
            throw new \Exception('Error al Generar el archivo.Tiempo de espera agotado.');
        }
    }
    /**
     * esCSV
     *
     * @param  string $filePath
     * @return bool
     */
    private function esCSV(string $filePath): bool
    {
        $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
        return $extension == 'csv';
    }

    /**
     * esODS
     *
     * @param  string $filePath
     * @return bool
     */
    private function esODS(string $filePath): bool
    {
        $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
        return $extension == 'ods';
    }

    /**
     * esXLSX
     *
     * @param  string $filePath
     * @return bool
     */
    private function esXLSX(string $filePath): bool
    {
        $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
        return $extension == 'xlsx';
    }
}