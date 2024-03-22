<?php

declare(strict_types=1);

namespace ComprobadorEquivalencias\Domain;

abstract class GestorFicheroBase
{

    /**
     *
     * @param  array $datos
     * @return void
     */
    abstract public function crearArchivo(array $datos): void;


    /**
     *
     * @throws \Exception
     * @param  string $fileRut
     * @param  string $nombreArchivo
     * @return void
     */
    public function descargarArchivo(string $fileRut, string $nombreArchivo): void
    {
        $fileName = $nombreArchivo;
        $filePath = $fileRut . $fileName;
        if (empty($fileName) || !file_exists($filePath)) {
            throw new \Exception('El archivo no existe');
        }

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
        return;
    }
    /**
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
