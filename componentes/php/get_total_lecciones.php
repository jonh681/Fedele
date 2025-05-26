<?php
$curso = $_GET['curso'] ?? '';

$cursoSanitizado = preg_replace('/[^a-zA-Z0-9_-]/', '_', $curso);
$rutaCurso = __DIR__ . "/cursosCreados/$cursoSanitizado";

$total = 0;

if (is_dir($rutaCurso)) {
    $secciones = array_diff(scandir($rutaCurso), ['.', '..']);
    foreach ($secciones as $seccion) {
        $rutaSeccion = "$rutaCurso/$seccion";
        if (is_dir($rutaSeccion)) {
            $lecciones = array_diff(scandir($rutaSeccion), ['.', '..']);
            foreach ($lecciones as $leccion) {
                if (is_dir("$rutaSeccion/$leccion")) {
                    $total++;
                }
            }
        }
    }
}

echo json_encode(['total' => $total]);
