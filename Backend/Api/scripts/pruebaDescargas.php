<?php
// Nombre del archivo
$fileName = basename('prueba.ods');
// Ruta del archivo
$filePath = "/usr/local/programadores/ComprobarEquivalencia/Backend/Api/scripts/".$fileName;

// Verificar si el nombre de archivo no está vacío y si el archivo existe
if(!empty($fileName) && file_exists($filePath)){
    // Obtener el tipo MIME del archivo
    $fileType = mime_content_type($filePath);

    // Configurar las cabeceras para forzar la descarga del archivo
    header("Content-Description: Descargar archivo");
    header('Content-Disposition: attachment; filename="' . $fileName . '"');
    header("Content-Type: $fileType");
    header("Content-Length: " . filesize($filePath));
    header("Content-Transfer-Encoding: binary");

    // Leer y enviar el archivo al navegador
    //readfile($filePath);
    exit;
}else{
    // Mostrar un mensaje si el archivo no existe
    echo 'El archivo no existe.';
}

