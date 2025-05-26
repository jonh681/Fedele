
<?php
session_start();
include("conexion.php");
$conn = getConexion();
$nombre = $_SESSION['nombre'];
$usuarioId = $_SESSION['usuario_id'];
$nombreCurso = $_GET['curso'] ?? '';

$nombreCursoLimpio = preg_replace('/[^a-zA-Z0-9_-]/', '_', $nombreCurso);
$ruta = __DIR__ . "/cursosCreados/$nombreCursoLimpio/recursos";

if (!is_dir($ruta)) {
    echo "<h3>No se encontró el curso o la carpeta de recursos.</h3>";
    exit;
}

function limpiarNombre($nombre) {
    return ucwords(str_replace(['_', '-'], ' ', $nombre));
}

function obtenerCarpetas($directorio) {
    $items = array_diff(scandir($directorio), ['.', '..']);
    $carpetas = [];
    foreach ($items as $item) {
        if (is_dir("$directorio/$item")) {
            $carpetas[] = $item;
        }
    }
    return $carpetas;
}

$secciones = obtenerCarpetas($ruta);

// Obtener progreso
$stmt = $conn->prepare("SELECT Lecciones_Completadas FROM inscripciones WHERE usuario_id = ? AND nombre_curso = ?");
$stmt->bind_param("is", $usuarioId, $nombreCurso);
$stmt->execute();
$stmt->store_result();
$stmt->bind_result($leccionesCompletadas);
$stmt->fetch();
$stmt->close();

// Obtener última lección vista
$stmt2 = $conn->prepare("SELECT Ultima_Leccion FROM inscripciones WHERE usuario_id = ? AND nombre_curso = ?");
$stmt2->bind_param("is", $usuarioId, $nombreCurso);
$stmt2->execute();
$stmt2->bind_result($ultimaLeccion);
$stmt2->fetch();
$stmt2->close();

// Construir lista global de lecciones
$leccionesConFecha = [];

foreach ($secciones as $sec) {
    $subRuta = "$ruta/$sec";
    $subcarpetas = obtenerCarpetas($subRuta);

    foreach ($subcarpetas as $sub) {
        $info = "$subRuta/$sub/info.txt";
        if (file_exists($info)) {
            $contenido = file_get_contents($info);
            preg_match('/Título:\s*(.+)/i', $contenido, $match);
            if (isset($match[1])) {
                $leccionesConFecha[] = [
                    'seccion' => $sec,
                    'subcarpeta' => $sub,
                    'titulo' => trim($match[1]),
                    'fecha' => filemtime("$subRuta/$sub") // 👈 importante
                ];
            }
        }
    }
}

// Ordenar por fecha de creación/modificación
usort($leccionesConFecha, function($a, $b) {
    return $a['fecha'] - $b['fecha'];
});

// Limpiar para dejar solo los campos necesarios
$leccionesOrdenadas = array_map(function($l) {
    return [
        'seccion' => $l['seccion'],
        'subcarpeta' => $l['subcarpeta'],
        'titulo' => $l['titulo']
    ];
}, $leccionesConFecha);

// Determinar posición de la última lección
$ultimaIndex = -1;
foreach ($leccionesOrdenadas as $i => $leccion) {
    if ($leccion['titulo'] === $ultimaLeccion) {
        $ultimaIndex = $i;
        break;
    }
}

// Calcular progreso
$totalLecciones = count($leccionesOrdenadas);
$leccionesCompletadas = $leccionesCompletadas ?? 0;
$progreso = ($totalLecciones > 0) ? round(($leccionesCompletadas / $totalLecciones) * 100) : 0;
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Curso: <?= htmlspecialchars($nombreCurso) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet" />
    <style>
    html, body {
        height: 100%;
        overflow: hidden; /* Elimina el scroll global */
    }

    .container-fluid.bg-light {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        z-index: 1030; /* Encima del resto */
    }

    .layout {
        display: flex;
        position: absolute;
        top: 140px; /* Altura del header */
        bottom: 0;
        left: 0;
        right: 0;
        overflow: hidden;
    }

    .menu {
        width: 250px;
        overflow-y: auto;
        border-right: 2px solid #e7eaef;
        padding: 1rem;
        background-color: #fff;
    }

    .contenido {
        flex-grow: 1;
        overflow-y: auto;
        padding: 1rem;
        background-color: #fff;
    }
    .progress-bar.custom-color {
        background-color: #234db8 !important; /* Color personalizado (rojo) */
      }
    </style>
</head>
<body class="m-0">
    <div class="container-fluid bg-light py-3 shadow-sm rounded mb-4">
        <div class="row align-items-center text-center text-lg-start">
            <div class="col-12 col-lg-4 d-flex justify-content-center justify-content-lg-start">
                <a href="/fedele/componentes/user/homePageUser.php">
                    <img src="/fedele/imagenes/fedele.png" alt="Logo" width="120" class="img-fluid">
                </a>
            </div>
            <div class="col-12 col-lg-4 text-center">
                <p id="texto-progreso" class="mb-2 fw-bold text-secondary">
                    <?= $progreso ?>% Completado: <?= $leccionesCompletadas ?>/<?= $totalLecciones ?> lecciones
                </p>
                <div class="progress mx-auto" style="height: 20px; max-width: 80%;">
                    <div id="barra-progreso" class="progress-bar progress-bar-striped progress-bar-animated custom-color"
                        role="progressbar" style="width: <?= $progreso ?>%;" aria-valuenow="<?= $progreso ?>" aria-valuemin="0" aria-valuemax="100">
                        <?= $progreso ?>%
                    </div>
                </div>
            </div>
            <div class="col-12 col-lg-4 d-flex justify-content-center justify-content-lg-end">
                <div class="dropdown">
                    <button class="btn btn-outline-dark dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        👤 <?= htmlspecialchars($nombre); ?>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                        <li><a class="dropdown-item text-center" href="#">⚙️ Configuración</a></li>
                        <li><a class="dropdown-item text-center text-danger" href="/fedele/componentes/php/logout.php">🚪 Cerrar sesión</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

   
    <div class="layout">
        <!-- Menú lateral -->
        <div class="menu">
            <div class="accordion" id="accordionSecciones" >
                <?php foreach ($secciones as $index => $seccion): ?>
                    <?php
                        $idSeccion = 'seccion' . $index;
                        $nombreLimpio = limpiarNombre($seccion);
                        $rutaSeccion = "$ruta/$seccion";
                        $subcarpetas = obtenerCarpetas($rutaSeccion);

                        // Asociar cada subcarpeta con su fecha de modificación
                        $subcarpetasConFecha = [];
                        foreach ($subcarpetas as $sub) {
                            $rutaCompleta = "$rutaSeccion/$sub";
                            $fecha = filemtime($rutaCompleta); // última modificación (puede usarse como fecha de creación en muchos casos)
                            $subcarpetasConFecha[] = ['nombre' => $sub, 'fecha' => $fecha];
                        }
                        
                        // Ordenar descendente por fecha
                        usort($subcarpetasConFecha, function($a, $b) {
                            return $a['fecha'] - $b['fecha'];
                        });
                        
                        // Obtener solo los nombres ordenados
                        $subcarpetas = array_column($subcarpetasConFecha, 'nombre');                    ?>
                    <div class="accordion-item" style='color:#234db8;'>
                        <h2 class="accordion-header" id="heading<?= $idSeccion ?>">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                    data-bs-target="#collapse<?= $idSeccion ?>" aria-expanded="false"
                                    aria-controls="collapse<?= $idSeccion ?>">
                                <?= $nombreLimpio; ?>
                            </button>
                        </h2>
                        <div id="collapse<?= $idSeccion ?>" class="accordion-collapse collapse"
                             aria-labelledby="heading<?= $idSeccion ?>" data-bs-parent="#accordionSecciones" style='color:#234db8;'>
                            <div class="accordion-body p-0">
                                <div class="list-group list-group-flush">
                                    <?php foreach ($subcarpetas as $sub): ?>
                                        <?php
                                            $indexGlobal = -1;
                                            foreach ($leccionesOrdenadas as $i => $lec) {
                                                if ($lec['seccion'] === $seccion && $lec['subcarpeta'] === $sub) {
                                                    $indexGlobal = $i;
                                                    break;
                                                }
                                            }
                                            $bloqueado = ($indexGlobal > $ultimaIndex + 1);
                                            $claseBloqueo = $bloqueado ? 'bloqueado' : '';
                                        ?>
                                        <a href="#"
                                           class="list-group-item list-group-item-action px-3 cargar-subcarpeta <?= $claseBloqueo ?>"
                                           data-global="<?= $indexGlobal ?>"
                                           data-curso="<?= htmlspecialchars($nombreCurso) ?>"
                                           data-seccion="<?= htmlspecialchars($seccion) ?>"
                                           data-subcarpeta="<?= htmlspecialchars($sub) ?>">
                                            <?= limpiarNombre($sub) ?>
                                        </a>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Contenido dinámico -->
        <div class="contenido">
            <div id="contenido-dinamico" class="text-muted text-center">
                ¡Bienvenido a este curso! Selecciona la lección donde te quedaste o tu lección principal
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function cargarSubcarpeta(link) {
            const curso = link.dataset.curso;
            const seccion = link.dataset.seccion;
            const subcarpeta = link.dataset.subcarpeta;
            const indexGlobal = parseInt(link.dataset.global);

            fetch(`contenido.php?curso=${encodeURIComponent(curso)}&seccion=${encodeURIComponent(seccion)}&subcarpeta=${encodeURIComponent(subcarpeta)}`)
                .then(res => res.text())
                .then(html => {
                    const contenedor = document.getElementById('contenido-dinamico');
                    contenedor.innerHTML = html;

                    const btnConcluir = contenedor.querySelector('.concluir-btn');
                    if (btnConcluir) {
                        btnConcluir.addEventListener('click', function () {
                            const titulo = this.dataset.titulo;
                            fetch('completo.php', {
                                method: 'POST',
                                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                                body: new URLSearchParams({ curso: curso, titulo: titulo })
                            })
                            .then(res => res.json())
                            .then(data => {
                                if (data.error) return;

                                document.getElementById('estado-leccion')?.classList.replace('bg-warning', 'bg-success');
                                document.getElementById('estado-leccion').textContent = 'Completado';
                                this.textContent = '✔ Lección completada';
                                this.classList.remove('btn-dark');
                                this.classList.add('btn-success');
                                this.disabled = true;

                                const barra = document.getElementById('barra-progreso');
                                barra.style.width = data.porcentaje + '%';
                                barra.setAttribute('aria-valuenow', data.porcentaje);
                                barra.textContent = data.porcentaje + '%';

                                const texto = document.getElementById('texto-progreso');
                                texto.textContent = `${data.porcentaje}% Completado: ${data.lecciones}/${data.total} lecciones`;

                                const siguiente = document.querySelector(`[data-global='${indexGlobal + 1}']`);
                                if (siguiente) {
                                    siguiente.classList.remove('bloqueado');
                                    siguiente.addEventListener('click', listenerRef);
                                }
                            });
                        });
                    }

                    setTimeout(asignarEventos, 50);
                });
        }

        function listenerRef(e) {
            e.preventDefault();
            cargarSubcarpeta(this);
        }

        function asignarEventos() {
            document.querySelectorAll('.cargar-subcarpeta:not(.bloqueado)').forEach(link => {
                link.removeEventListener('click', listenerRef);
                link.addEventListener('click', listenerRef);
            });
        }

        document.addEventListener('DOMContentLoaded', asignarEventos);
    </script>
    
</body>
</html>
