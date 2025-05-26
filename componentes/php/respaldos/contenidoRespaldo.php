
<?php
session_start();
include("conexion.php");
$conn = getConexion();

$nombre = $_SESSION['nombre'];
$usuarioId = $_SESSION['usuario_id'];

$curso = $_GET['curso'] ?? '';
$seccion = $_GET['seccion'] ?? '';
$subcarpeta = $_GET['subcarpeta'] ?? '';

$cursoLimpio = preg_replace('/[^a-zA-Z0-9_-]/', '_', $curso);
$seccionLimpia = preg_replace('/[^a-zA-Z0-9_-]/', ' ', $seccion);
$subLimpia = preg_replace('/[^a-zA-Z0-9_-]/', ' ', $subcarpeta);
$nomSeccion = str_ireplace(['_', '-'], ' ', $seccion);

$ruta = __DIR__ . "/cursosCreados/$cursoLimpio/recursos/$seccionLimpia/$subLimpia";

if (!is_dir($ruta)) {
    echo "<div class='text-danger'>No se encontró la subcarpeta.</div>";
    echo "<p>Ruta esperada: $ruta</p>";
    exit;
}

$titulo = 'Sin título';
$descripcion = 'Sin descripción';
$url = null;

// Leer info.txt
$infoFile = "$ruta/info.txt";
if (file_exists($infoFile)) {
    $lineas = file($infoFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES); // obtiene un array de líneas

    foreach ($lineas as $linea) {
        $linea = ltrim($linea); // elimina espacios o tabs al inicio

        if (stripos($linea, 'Título:') === 0) {
            $titulo = trim(explode(':', $linea, 2)[1]);
        } elseif (stripos($linea, 'Descripción:') === 0) {
            $descripcion = trim(explode(':', $linea, 2)[1]);
        } elseif (stripos($linea, 'URL:') === 0) {
            $url = trim(explode(':', $linea, 2)[1]);
        }
    }
}
// Archivos adjuntos
$archivos = array_filter(scandir($ruta), function($f) use ($ruta) {
    return is_file("$ruta/$f") && $f !== 'info.txt';
});

// Obtener lista global de lecciones
$directorioBase = __DIR__ . "/cursosCreados/$cursoLimpio/recursos";
$leccionesOrdenadas = [];
$secciones = array_diff(scandir($directorioBase), ['.', '..']);
foreach ($secciones as $sec) {
    $subRuta = "$directorioBase/$sec";
    if (!is_dir($subRuta)) continue;
    $subs = array_diff(scandir($subRuta), ['.', '..']);
    foreach ($subs as $sub) {
        $info = "$subRuta/$sub/info.txt";
        if (file_exists($info)) {
            $cont = file_get_contents($info);
            preg_match('/Título:\s*(.+)/i', $cont, $match);
            if (isset($match[1])) {
                $leccionesOrdenadas[] = [
                    'seccion' => $sec,
                    'subcarpeta' => $sub,
                    'titulo' => trim($match[1])
                ];
            }
        }
    }
}

// Obtener lección actual y última
$leccionActual = $titulo;

$stmt = $conn->prepare("SELECT Ultima_Leccion FROM inscripciones WHERE usuario_id = ? AND nombre_curso = ?");
$stmt->bind_param("is", $usuarioId, $curso);
$stmt->execute();
$stmt->bind_result($ultimaLeccion);
$stmt->fetch();
$stmt->close();

$actualIndex = -1;
$ultimaIndex = -1;
$seccionNormalizada = strtolower(trim(preg_replace('/[^a-zA-Z0-9_-]/', '_', $seccion)));
$subcarpetaNormalizada = strtolower(trim(preg_replace('/[^a-zA-Z0-9_-]/', '_', $subcarpeta)));

foreach ($leccionesOrdenadas as $i => $lec) {
    $lecSeccion = strtolower(trim(preg_replace('/[^a-zA-Z0-9_-]/', '_', $lec['seccion'])));
    $lecSubcarpeta = strtolower(trim(preg_replace('/[^a-zA-Z0-9_-]/', '_', $lec['subcarpeta'])));

    if ($lecSeccion === $seccionNormalizada && $lecSubcarpeta === $subcarpetaNormalizada) {
        $actualIndex = $i;
    }

    // También puedes normalizar aquí si `Ultima_Leccion` tiene inconsistencias
    if (trim($lec['titulo']) === trim($ultimaLeccion)) {
        $ultimaIndex = $i;
    }
}
$esActualConcluida = ($actualIndex <= $ultimaIndex);
$esSiguiente = ($actualIndex === $ultimaIndex + 1);

echo "<div class='container p-4 bg-white' style='color:#234db8;'>";
echo "<h4 class='mb-3' style='text-align: justify;'>" . htmlspecialchars($titulo) . "</h4>";

echo "<div class='d-flex justify-content-between align-items-center mb-3 p-2  border rounded shadow-sm' style='background-color:rgb(221, 221, 221);'>";
echo "<div><a href='#'>" . htmlspecialchars($curso) . "</a> › <span>" . htmlspecialchars($nomSeccion) . " > " . htmlspecialchars($titulo) . "</span></div>";


$badge = $esActualConcluida ? "<span id='estado-leccion' class='badge bg-success'>Completado</span>" : "<span id='estado-leccion' class='badge bg-warning'>En proceso</span>";
echo $badge;
echo "</div>";

echo "<p class='mb-4' style='text-align: justify;'>" . nl2br(htmlspecialchars($descripcion)) . "</p>";

// Video
if ($url && preg_match('/(youtube\.com\/watch\?v=|youtu\.be\/)([^\s&]+)/', $url, $matches)) {
    $videoId = end($matches);
    echo "<div class=' mb-4'>
            <iframe width='720' height='405' src='https://www.youtube.com/embed/$videoId' title='Video del curso' allowfullscreen></iframe>
          </div>";
} elseif ($url) {
    echo "<p><strong>URL:</strong> <a href='" . htmlspecialchars($url) . "' target='_blank'>" . htmlspecialchars($url) . "</a></p>";
}

// Descripción

// Archivos
if (count($archivos) > 0) {
    echo "<div class='mb-4'>";
    foreach ($archivos as $archivo) {
        $ext = pathinfo($archivo, PATHINFO_EXTENSION);
        $rutaRelativa = "cursosCreados/$cursoLimpio/recursos/$seccionLimpia/$subLimpia/$archivo";
        if (in_array(strtolower($ext), ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
            echo "<p><strong>Imagen:</strong></p><img src='$rutaRelativa' class='img-fluid rounded mb-3' style='max-height: 300px;'>";
        } elseif ($ext === 'pdf') {
            echo "<p><strong>PDF:</strong> <a href='$rutaRelativa' target='_blank'>Ver documento</a></p>";
        } elseif (in_array($ext, ['ppt', 'pptx'])) {
            echo "<p><strong>Presentación:</strong> <a href='$rutaRelativa' target='_blank'>Ver presentación</a></p>";
        } else {
            echo "<p><strong>Archivo:</strong> <a href='$rutaRelativa' download>Descargar $archivo</a></p>";
        }
    }
    echo "</div>";
} else {
    echo "<p class='text-muted mb-4'></p>";
}

if ($esActualConcluida) {
    echo "<button class='btn btn-success' disabled>✔ Lección completada</button>";
} elseif ($esSiguiente) {
    echo "<button class='btn btn-dark concluir-btn'
            data-curso='" . htmlspecialchars($curso) . "'
            data-titulo='" . htmlspecialchars($titulo) . "'
            style='--bs-btn-padding-y: .25rem; --bs-btn-padding-x: .5rem; --bs-btn-font-size: .75rem;'>
            Clic para completar lección
        </button>";
}

echo "</div>";
?>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const btn = document.querySelector('.concluir-btn');
    if (btn) {
        btn.addEventListener('click', function () {
            const curso = this.dataset.curso;
            const titulo = this.dataset.titulo;

            fetch('completo.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: new URLSearchParams({ curso: curso, titulo: titulo })
            })
            .then(res => res.json())
            .then(data => {
                if (data.error) return;
                const span = document.getElementById('estado-leccion');
                if (span) {
                    span.textContent = 'Completado';
                    span.classList.remove('bg-warning');
                    span.classList.add('bg-success');
                }
                this.textContent = '✔ Lección completada';
                this.classList.remove('btn-dark');
                this.classList.add('btn-success');
                this.disabled = true;
            });
        });
    }
});
</script>
