<?php
session_start();
include("conexion.php");
$conn = getConexion();

$nombre = $_SESSION['nombre'] ?? null;
$usuarioId = $_SESSION['usuario_id'] ?? null;

$curso = $_GET['curso'] ?? '';
$seccion = $_GET['seccion'] ?? '';
$subcarpeta = $_GET['subcarpeta'] ?? '';



if (!$nombre || !$usuarioId || !$curso || !$subcarpeta) {
    echo "<div class='text-danger'>Faltan datos necesarios para mostrar la lección.</div>";
    exit;
}

$stmt = $conn->prepare("SELECT Ultima_Leccion FROM inscripciones WHERE usuario_id = ? AND nombre_curso = ?");
$stmt->bind_param("is", $usuarioId, $curso);
$stmt->execute();
$stmt->bind_result($ultimaLeccion);
$stmt->fetch();
$stmt->close();

// Obtener el ID actual y de la última lección
$stmt2 = $conn->prepare("SELECT id_leccion FROM `$curso` WHERE nombre_leccion = ?");
$stmt2->bind_param("s", $subcarpeta);
$stmt2->execute();
$stmt2->bind_result($idActual);
$stmt2->fetch();
$stmt2->close();

$idUltima = -1;
if ($ultimaLeccion) {
    $stmt3 = $conn->prepare("SELECT id_leccion FROM `$curso` WHERE nombre_leccion = ?");
    $stmt3->bind_param("s", $ultimaLeccion);
    $stmt3->execute();
    $stmt3->bind_result($idUltima);
    $stmt3->fetch();
    $stmt3->close();
}

$idAnterior = $idActual - 1;
$seccionAnterior = $leccionAnterior = null;
$stmt = $conn->prepare("SELECT nombre_seccion, nombre_leccion FROM `$curso` WHERE id_leccion = ?");
$stmt->bind_param("i", $idAnterior);
$stmt->execute();
$result = $stmt->get_result();
if ($row = $result->fetch_assoc()) {
    $seccionAnterior = $row['nombre_seccion'];
    $leccionAnterior = $row['nombre_leccion'];
}
$stmt->close();

// Buscar lección siguiente
$idSiguiente = $idActual + 1;
$seccionSiguiente = $leccionSiguiente = null;
$stmt = $conn->prepare("SELECT nombre_seccion, nombre_leccion FROM `$curso` WHERE id_leccion = ?");
$stmt->bind_param("i", $idSiguiente);
$stmt->execute();
$result = $stmt->get_result();
if ($row = $result->fetch_assoc()) {
    $seccionSiguiente = $row['nombre_seccion'];
    $leccionSiguiente = $row['nombre_leccion'];
}
$stmt->close();
// === Leer info.txt ===
$rutaRecurso = __DIR__ . "/cursosCreados/" . preg_replace('/[^a-zA-Z0-9_-]/', '_', $curso) . "/recursos/" .
                preg_replace('/[^a-zA-Z0-9_-]/', ' ', $seccion) . "/" .
                preg_replace('/[^a-zA-Z0-9_-]/', ' ', $subcarpeta);

$titulo = 'Sin título';
$descripcion = 'Sin descripción';
$url = null;

$infoPath = "$rutaRecurso/info.txt";
if (file_exists($infoPath)) {
    $contenido = file($infoPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($contenido as $linea) {
        if (stripos($linea, 'Título:') === 0) {
            $titulo = trim(explode(':', $linea, 2)[1]);
        } elseif (stripos($linea, 'Descripción:') === 0) {
            $descripcion = trim(explode(':', $linea, 2)[1]);
        } elseif (stripos($linea, 'URL:') === 0) {
            $url = trim(explode(':', $linea, 2)[1]);
        }
    }
}

// === Archivos adjuntos ===
$archivos = array_filter(scandir($rutaRecurso), function($f) use ($rutaRecurso) {
    return is_file("$rutaRecurso/$f") && $f !== 'info.txt';
});

// === Estados ===
$esActualConcluida = ($idActual <= $idUltima);
$esSiguiente = ($idActual === $idUltima + 1 || ($idUltima === -1 && $idActual === 1));
?>

<div class="container p-4 bg-white" style="color:#234db8;">
    <h4 class="mb-3" style="text-align: justify;"><?= htmlspecialchars($titulo) ?></h4>

    <div class="d-flex justify-content-between align-items-center mb-3 p-2 border rounded shadow-sm bg-light">
        <div>
            <a href="#"><?= htmlspecialchars($curso) ?></a> › <span><?= htmlspecialchars($seccion) ?> > <?= htmlspecialchars($titulo) ?></span>
        </div>
        <span id="estado-leccion" class="badge <?= $esActualConcluida ? 'bg-success' : 'bg-warning' ?>">
            <?= $esActualConcluida ? 'Completado' : 'En proceso' ?>
        </span>
    </div>

    <p class="mb-4" style="text-align: justify;"><?= nl2br(htmlspecialchars($descripcion)) ?></p>

    <?php if ($url && preg_match('/(youtube\.com\/watch\?v=|youtu\.be\/)([^\s&]+)/', $url, $matches)): ?>
        <?php $videoId = end($matches); ?>
        <div class="mb-4">
            <iframe width="720" height="405" src="https://www.youtube.com/embed/<?= $videoId ?>" allowfullscreen></iframe>
        </div>
    <?php elseif ($url): ?>
        <p><strong>URL:</strong> <a href="<?= htmlspecialchars($url) ?>" target="_blank"><?= htmlspecialchars($url) ?></a></p>
    <?php endif; ?>

    <?php if (count($archivos) > 0): ?>
        <div class="mb-4">
            <?php foreach ($archivos as $archivo): ?>
                <?php
                $ext = strtolower(pathinfo($archivo, PATHINFO_EXTENSION));
                $rutaArchivo = "cursosCreados/" . preg_replace('/[^a-zA-Z0-9_-]/', '_', $curso) . "/recursos/" .
                               preg_replace('/[^a-zA-Z0-9_-]/', ' ', $seccion) . "/" .
                               preg_replace('/[^a-zA-Z0-9_-]/', ' ', $subcarpeta) . "/$archivo";
                ?>
                <?php if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp'])): ?>
                    <img src="<?= $rutaArchivo ?>" class="img-fluid rounded mb-3" style="max-height: 300px;">
                <?php elseif ($ext === 'pdf'): ?>
                    <p><strong>PDF:</strong> <a href="<?= $rutaArchivo ?>" target="_blank">Ver documento</a></p>
                <?php elseif (in_array($ext, ['ppt', 'pptx'])): ?>
                    <p><strong>Presentación:</strong> <a href="<?= $rutaArchivo ?>" target="_blank">Ver presentación</a></p>
                <?php else: ?>
                    <p><strong>Archivo:</strong> <a href="<?= $rutaArchivo ?>" download>Descargar <?= $archivo ?></a></p>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <div class="text-center mt-4">
        <?php if ($esSiguiente): ?>
            <button class="btn btn-dark concluir-btn"
                    data-curso="<?= htmlspecialchars($curso) ?>"
                    data-titulo="<?= htmlspecialchars($subcarpeta) ?>">
                ✔ Concluir lección
            </button>
        <?php elseif ($esActualConcluida): ?>
            <button class="btn btn-success" disabled>✔ Lección completada</button>
        <?php else: ?>
            <button class="btn btn-secondary" disabled>🔒 Lección bloqueada</button>
        <?php endif; ?>
    </div>

    <div class="d-flex justify-content-between mt-4">
        <?php if ($leccionAnterior && $seccionAnterior): ?>
            <button class="btn btn-outline-primary navegar-btn"
                    data-curso="<?= htmlspecialchars($curso) ?>"
                    data-seccion="<?= htmlspecialchars($seccionAnterior) ?>"
                    data-subcarpeta="<?= htmlspecialchars($leccionAnterior) ?>">
                ⬅ Lección anterior
            </button>
        <?php else: ?>
            <div></div>
        <?php endif; ?>

        <?php if ($leccionSiguiente && $seccionSiguiente): ?>
            <button class="btn btn-outline-primary navegar-btn"
                    data-curso="<?= htmlspecialchars($curso) ?>"
                    data-seccion="<?= htmlspecialchars($seccionSiguiente) ?>"
                    data-subcarpeta="<?= htmlspecialchars($leccionSiguiente) ?>">
                Lección siguiente ➡
            </button>
        <?php endif; ?>
    </div>
</div>
<script>
document.querySelector('.concluir-btn')?.addEventListener('click', function () {
    const curso = this.dataset.curso;
    const titulo = this.dataset.titulo;

    fetch('completo.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: new URLSearchParams({ curso, titulo })
    })
    .then(res => res.json())
    .then(data => {
        if (data.error) {
            alert("❌ Error: " + data.msg);
            console.log("DEBUG:", data.debug);
            return;
        }

        this.textContent = "✔ Lección completada";
        this.classList.remove('btn-dark');
        this.classList.add('btn-success');
        this.disabled = true;

        const siguiente = document.querySelector(`[data-id='${data.siguiente_id}']`);
        if (siguiente) {
            siguiente.classList.remove('bloqueado', 'text-muted');
            siguiente.addEventListener('click', listenerRef);
        }
    });
});
</script>
<script>
document.querySelectorAll('.navegar-btn').forEach(btn => {
    btn.addEventListener('click', function () {
        const curso = this.dataset.curso;
        const seccion = this.dataset.seccion;
        const subcarpeta = this.dataset.subcarpeta;

        fetch(`contenido.php?curso=${encodeURIComponent(curso)}&seccion=${encodeURIComponent(seccion)}&subcarpeta=${encodeURIComponent(subcarpeta)}`)
            .then(res => res.text())
            .then(html => {
                const contenedor = document.getElementById('contenido-dinamico');
                contenedor.innerHTML = html;

                // Volver a asignar eventos al menú lateral
                setTimeout(asignarEventos, 50);
            });
    });
});
</script>