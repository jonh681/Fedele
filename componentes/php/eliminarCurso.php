<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = $_POST['titulo'] ?? '';

    if ($titulo === '') {
        echo "Título no especificado";
        exit;
    }

    $ruta = __DIR__ . "/cursosCreados/" . $titulo;

    function eliminarCarpeta($ruta) {
        if (!file_exists($ruta)) return false;
        if (!is_dir($ruta)) return unlink($ruta);

        foreach (scandir($ruta) as $item) {
            if ($item === '.' || $item === '..') continue;
            eliminarCarpeta($ruta . DIRECTORY_SEPARATOR . $item);
        }

        return rmdir($ruta);
    }

    if (eliminarCarpeta($ruta)) {
        echo "OK";
    } else {
        echo "Error al eliminar la carpeta";
    }
}
?>
