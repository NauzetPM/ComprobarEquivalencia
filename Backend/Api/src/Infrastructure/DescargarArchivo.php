<?php
namespace ComprobadorEquivalencias\Infrastructure;

class DescargarArchivo
{

    
    /**
     * __construct
     *
     * @return void
     */
    public function __construct()
    {
        
    }
    
    /**
     * descargarArchivo
     *
     * @param  string $nombreArchivo
     * @return void
     */
    public function descargarArchivo(string $nombreArchivo)
    {
        // Nombre del archivo
        $fileName = $nombreArchivo;
        // Ruta del archivo
        $filePath = "/usr/local/programadores/ComprobarEquivalencia/Backend/Api/files/" . $fileName;
        // Verificar si el nombre de archivo no está vacío y si el archivo existe
        if (!empty($fileName) && file_exists($filePath)) {
            if ($this->esCSV($filePath)) {
                $fileType = 'text/csv';
            } elseif ($this->esODS($filePath)) {
                // Si el archivo es ODS, establecer el tipo MIME como application/vnd.oasis.opendocument.spreadsheet
                $fileType = 'application/vnd.oasis.opendocument.spreadsheet';
            } elseif ($this->esXLSX($filePath)) {
                // Si el archivo es XLSX, establecer el tipo MIME como application/vnd.openxmlformats-officedocument.spreadsheetml.sheet
                $fileType = 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';
            }

            // Configurar las cabeceras para forzar la descarga del archivo
            header("Content-Description: Descargar archivo");
            header('Content-Disposition: attachment; filename="' . $fileName . '"');
            header("Content-Type: $fileType");
            header("Content-Length: " . filesize($filePath));
            header("Content-Transfer-Encoding: binary");

            // Leer y enviar el archivo al navegador
            readfile($filePath);
            exit;
        } else {
            // Mostrar un mensaje si el archivo no existe
            echo 'El archivo no existe.';
        }

    }    
    /**
     * esCSV
     *
     * @param  string $filePath
     * @return bool
     */
    private function esCSV(string $filePath): bool {
        $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
        return $extension == 'csv';
    }
        
    /**
     * esODS
     *
     * @param  string $filePath
     * @return bool
     */
    private function esODS(string $filePath): bool {
        $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
        return $extension == 'ods';
    }
        
    /**
     * esXLSX
     *
     * @param  string $filePath
     * @return bool
     */
    private function esXLSX(string $filePath): bool {
        $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
        return $extension == 'xlsx';
    }


}