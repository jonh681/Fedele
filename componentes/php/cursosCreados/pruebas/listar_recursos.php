<?php
header('Content-Type: application/json');
ini_set('display_errors', 1);
error_reporting(E_ALL);

$directorioBase = 'recursos/';
$recursos = [];

if (is_dir($directorioBase)) {
    $secciones = array_diff(scandir($directorioBase), ['.', '..']);
    natcasesort($secciones); // Ordena alfabéticamente de forma natural
    $secciones = array_values($secciones);
    $seccionIndex = 1;

    foreach ($secciones as $seccion) {
        $rutaSeccion = $directorioBase . $seccion . '/';

        if (is_dir($rutaSeccion)) {
            $recursosSeccion = array_diff(scandir($rutaSeccion), ['.', '..']);

            foreach ($recursosSeccion as $recursoDir) {
                $rutaRecurso = $rutaSeccion . $recursoDir . '/';

                if (is_dir($rutaRecurso)) {
                    $infoFile = $rutaRecurso . 'info.txt';
                    $titulo = $recursoDir;
                    $descripcion = '';
                    $url = '';
                    $archivo = '';
                    $fecha = filemtime($rutaRecurso);

                    if (file_exists($infoFile)) {
                        $contenido = file_get_contents($infoFile);
                        preg_match('/Título: (.*)/', $contenido, $matchTitulo);
                        preg_match('/Descripción: (.*)/', $contenido, $matchDesc);
                        preg_match('/URL: (.*)/', $contenido, $matchURL);

                        $titulo = $matchTitulo[1] ?? $recursoDir;
                        $descripcion = $matchDesc[1] ?? '';
                        $url = $matchURL[1] ?? '';
                    }

                    $archivos = array_diff(scandir($rutaRecurso), ['.', '..', 'info.txt']);
                    if (!empty($archivos)) {
                        $archivoNombre = reset($archivos);
                        $archivo = 'recursos/' . $seccion . '/' . $recursoDir . '/' . $archivoNombre;
                    }

                    // SOLO AQUÍ SE AGREGA EL RECURSO
                    $recursos[] = [
                        'titulo' => trim($titulo),
                        'descripcion' => trim($descripcion),
                        'url' => $url,
                        'archivo' => $archivo,
                        'seccion' => $seccion,
                        'carpeta' => $recursoDir,
                        'fecha' => $fecha
                    ];
                }
            }
        }

        $seccionIndex++;
    }

    // Ordenar por fecha DESC
    usort($recursos, function ($a, $b) {
        return $b['fecha'] - $a['fecha'];
    });
}

echo json_encode($recursos);
