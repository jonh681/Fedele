<?php
header('Content-Type: application/json');

$directorioCursos = __DIR__ . '/cursosCreados';
$cursos = [];

if (is_dir($directorioCursos)) {
    foreach (scandir($directorioCursos) as $folder) {
        if ($folder === '.' || $folder === '..') continue;

        $rutaCurso = $directorioCursos . '/' . $folder;

        if (is_dir($rutaCurso)) {
            $nombreCurso = $folder;
            $descripcion = '';
            $nombreCorto = '';
            $fechaInicio = '';
            $fechaFin = '';
            $imagen = '';

            $archivoTxt = $rutaCurso . '/' . $nombreCurso . '.txt';
            if (file_exists($archivoTxt)) {
                $contenido = file_get_contents($archivoTxt); 
                preg_match('/Nombre original:\s*(.*)/', $contenido, $no);
                preg_match('/Nombre corto:\s*(.*)/', $contenido, $nc);
                preg_match('/Fecha de inicio:\s*(.*)/', $contenido, $fi);
                preg_match('/Fecha de fin:\s*(.*)/', $contenido, $ff);
                preg_match('/Descripción:\s*(.*)/', $contenido, $desc);
                $nombreOriginal = $no[1] ?? '';
                $nombreCorto = $nc[1] ?? '';
                $fechaInicio = $fi[1] ?? '';
                $fechaFin = $ff[1] ?? '';
                $descripcion = $desc[1] ?? '';
            }

            // Buscar imagen (si existe .jpg, .png o .jpeg)
            foreach (['jpg', 'png', 'jpeg'] as $ext) {
                $imagenPath = $rutaCurso . '/' . $nombreCurso . '.' . $ext;
                if (file_exists($imagenPath)) {
                    $imagen = '/fedele/componentes/php/cursosCreados/' . $nombreCurso . '/' . $nombreCurso . '.' . $ext;
                    break;
                }
            }

            $cursos[] = [
                'TituloOriginal' => $nombreOriginal,
                'titulo' => $nombreCurso,
                'nombreCorto' => $nombreCorto,
                'fechaInicio' => $fechaInicio,
                'fechaFin' => $fechaFin,
                'descripcion' => $descripcion,
                'imagen' => $imagen
            ];
        }
    }
}

echo json_encode($cursos);
