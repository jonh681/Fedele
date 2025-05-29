<?php
session_start();
include("conexion.php");
$conn = getConexion();

// Asegurarse que el usuario está logueado
if (!isset($_SESSION['usuario_id']) || !isset($_SESSION['nombre'])) {
    header("Location: /fedele/index.html");
    exit();
}

$nombre = $_SESSION['nombre'];
$usuarioId = $_SESSION['usuario_id'];
$nombreCurso = $_GET['curso'] ?? '';

if (!$nombreCurso) {
    echo "Curso no especificado.";
    exit();
}

function limpiarNombre($nombre) {
    return ucwords(str_replace(['_', '-'], ' ', $nombre));
}

// Obtener la última lección vista
$stmt = $conn->prepare("SELECT Ultima_Leccion FROM inscripciones WHERE usuario_id = ? AND nombre_curso = ?");
$stmt->bind_param("is", $usuarioId, $nombreCurso);
$stmt->execute();
$stmt->bind_result($ultimaLeccion);
$stmt->fetch();
$stmt->close();

// Obtener todas las lecciones del curso desde su tabla
$stmt = $conn->prepare("SELECT nombre_seccion, nombre_leccion, id_leccion FROM `$nombreCurso` ORDER BY id_leccion ASC");
$stmt->execute();
$resultado = $stmt->get_result();

$estructura = [];
$ultimoId = -1;
while ($row = $resultado->fetch_assoc()) {
    $seccion = $row['nombre_seccion'];
    $leccion = $row['nombre_leccion'];
    $id = (int)$row['id_leccion'];

    // Agrupar por sección
    $estructura[$seccion][] = [
        'leccion' => $leccion,
        'id' => $id
    ];

    // Detectar cuál es el ID de la última lección completada
    if (trim($leccion) === trim($ultimaLeccion)) {
        $ultimoId = $id;
    }
}
$stmt->close();

// Obtener progreso del curso
$stmt = $conn->prepare("SELECT Lecciones_Completadas FROM inscripciones WHERE usuario_id = ? AND nombre_curso = ?");
$stmt->bind_param("is", $usuarioId, $nombreCurso);
$stmt->execute();
$stmt->bind_result($leccionesCompletadas);
$stmt->fetch();
$stmt->close();

// Contar total de lecciones
$totalLecciones = 0;
foreach ($estructura as $lecciones) {
    $totalLecciones += count($lecciones);
}

$progresoPorcentaje = ($totalLecciones > 0) ? round(($leccionesCompletadas / $totalLecciones) * 100) : 0;

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
            overflow: hidden;
        }

        .container-fluid.bg-light {
            position: fixed;
            top: 0; left: 0; right: 0;
            z-index: 1030;
        }

        .layout {
            display: flex;
            position: absolute;
            top: 140px;
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

        .bloqueado {
            pointer-events: none;
            opacity: 0.5;
        }

        .progress-bar.custom-color {
            background-color: #234db8 !important;
        }
        .active-leccion {
        background-color: #234db8 !important;
        color: white !important;
        font-weight: bold;
        }

        .contenido-bloqueado {
            display: none !important;        
        }
    </style>
</head>
<body>
    <div class="container-fluid bg-light py-3 shadow-sm rounded mb-4">
        <div class="row align-items-center text-center text-lg-start">
            <div class="col-12 col-lg-4 d-flex justify-content-center justify-content-lg-start">
                <a href="/fedele/componentes/user/homePageUser.php">
                    <img src="/fedele/imagenes/fedele.png" alt="Logo" width="120" class="img-fluid">
                </a>
            </div>
            <div class="col-12 col-lg-4 text-center">
                <div class="text-center w-100">
                    <p class="mb-1 fw-bold text-secondary">
                        Curso: <?= limpiarNombre($nombreCurso) ?>
                    </p>
                    <p id="texto-progreso" class="mb-1 text-muted small">
                        <?= $progresoPorcentaje ?>% Completado: <?= $leccionesCompletadas ?>/<?= $totalLecciones ?> lecciones
                    </p>
                    <div class="progress" style="height: 16px;">
                        <div id="barra-progreso" class="progress-bar progress-bar-striped progress-bar-animated custom-color"
                            role="progressbar" style="width: <?= $progresoPorcentaje ?>%;" aria-valuenow="<?= $progresoPorcentaje ?>"
                            aria-valuemin="0" aria-valuemax="100">
                            <?= $progresoPorcentaje ?>%
                        </div>
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
        <div class="menu">
            <div class="accordion" id="accordionSecciones">
                <?php foreach ($estructura as $nombreSeccion => $lecciones): ?>
                    <?php $idSeccion = 'seccion_' . md5($nombreSeccion); ?>
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="heading<?= $idSeccion ?>">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                    data-bs-target="#collapse<?= $idSeccion ?>" aria-expanded="false"
                                    aria-controls="collapse<?= $idSeccion ?>">
                                <?= limpiarNombre($nombreSeccion) ?>
                            </button>
                        </h2>
                        <div id="collapse<?= $idSeccion ?>" class="accordion-collapse collapse"
                             aria-labelledby="heading<?= $idSeccion ?>" data-bs-parent="#accordionSecciones">
                            <div class="accordion-body p-0">
                                <div class="list-group list-group-flush">
                                    <?php foreach ($lecciones as $leccion): ?>
                                        <?php
                                            $bloqueado = ($ultimoId === -1) ? ($leccion['id'] > 1) : ($leccion['id'] > $ultimoId + 1);
                                            $clase = $bloqueado ? 'bloqueado text-muted' : '';
                                        ?>
                                        <a href="#"
                                        class="list-group-item list-group-item-action px-3 cargar-subcarpeta <?= $clase ?>"
                                        data-idseccion="<?= $idSeccion ?>"
                                        data-curso="<?= htmlspecialchars($nombreCurso) ?>"
                                        data-seccion="<?= htmlspecialchars($nombreSeccion) ?>"
                                        data-subcarpeta="<?= htmlspecialchars($leccion['leccion']) ?>"
                                        data-id="<?= $leccion['id'] ?>">
                                           <?= limpiarNombre($leccion['leccion']) ?>
                                        </a>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="contenido">
            <div id="contenido-dinamico" class="text-muted text-center">
                ¡Bienvenido al curso! Selecciona una lección para comenzar.
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        function inicializarCarrusel() {
            const el = document.getElementById('libroCarousel');
            if (el) {
                bootstrap.Carousel.getOrCreateInstance(el, {
                    interval: false,
                    ride: false,
                    pause: true,
                    wrap: false // importante: no se reinicia automáticamente
                });
                configurarAvisoFinalCarrusel();
            }
        }

        function configurarAvisoFinalCarrusel() {
            const carrusel = document.getElementById('libroCarousel');
            if (!carrusel) return;

            const instance = bootstrap.Carousel.getInstance(carrusel);
            const totalSlides = carrusel.querySelectorAll('.carousel-item').length;
            let currentIndex = 0;

            // Actualiza el índice cuando se hace slide
            carrusel.addEventListener('slide.bs.carousel', function (event) {
                currentIndex = event.to;
            });

            // Botones externos
            const btnPrev = document.getElementById('btn-prev-slide');
            const btnNext = document.getElementById('btn-next-slide');

            if (btnPrev) {
                btnPrev.addEventListener('click', function () {
                    if (currentIndex === 0) {
                        alert('🚫 Ya estás en la primera página del libro.');
                    } else {
                        instance.prev();
                    }
                });
            }

            if (btnNext) {
                btnNext.addEventListener('click', function () {
                    if (currentIndex === totalSlides - 1) {
                        alert('📖 Fin del contenido del libro.');
                    } else {
                        instance.next();
                    }
                });
            }
        }
        
        function cargarSubcarpeta(link) {
            document.querySelectorAll('.cargar-subcarpeta').forEach(el => el.classList.remove('active-leccion'));

            // Agrega la clase activa al enlace actual
            link.classList.add('active-leccion');
            const seccionId = link.dataset.idseccion;
            const collapse = document.getElementById(`collapse${seccionId}`);
            if (collapse) {
                const bsCollapse = new bootstrap.Collapse(collapse, { toggle: true });
            }
            const curso = link.dataset.curso;
            const seccion = link.dataset.seccion;
            const subcarpeta = link.dataset.subcarpeta;

            fetch(`contenido.php?curso=${encodeURIComponent(curso)}&seccion=${encodeURIComponent(seccion)}&subcarpeta=${encodeURIComponent(subcarpeta)}`)
                .then(res => res.text())
                .then(html => {
                    const contenedor = document.getElementById('contenido-dinamico');
                    contenedor.innerHTML = html;
                    inicializarCarrusel();

                    // Evento para botón de concluir
                    const btnConcluir = contenedor.querySelector('.concluir-btn');
                    if (btnConcluir) {
                        btnConcluir.addEventListener('click', function () {
                            const curso = this.dataset.curso;
                            const titulo = this.dataset.titulo;

                            fetch("completo.php", {
                                method: "POST",
                                headers: { "Content-Type": "application/x-www-form-urlencoded" },
                                body: new URLSearchParams({ curso, titulo })
                            })
                            .then(res => res.json())
                            .then(data => {
                                if (data.error) {
                                    alert("❌ Error: " + data.msg);
                                    return;
                                }
                                const barra = document.getElementById('barra-progreso');
                                const texto = document.getElementById('texto-progreso');
                                if (barra && texto) {
                                    barra.style.width = data.porcentaje + '%';
                                    barra.setAttribute('aria-valuenow', data.porcentaje);
                                    barra.textContent = data.porcentaje + '%';

                                    texto.textContent = `${data.porcentaje}% Completado: ${data.lecciones}/${data.total} lecciones`;
                                }

                                this.textContent = "✔ Lección completada";
                                this.classList.remove("btn-dark");
                                this.classList.add("btn-success");
                                this.disabled = true;

                                const siguiente = document.querySelector(`[data-id='${data.siguiente_id}']`);
                                if (siguiente) {
                                    siguiente.classList.remove("bloqueado", "text-muted");
                                    siguiente.addEventListener("click", listenerRef);
                                }
                            });
                        });
                    }

                    // Evento para botones de navegación
                    document.querySelectorAll('.navegar-btn').forEach(btn => {
                        btn.addEventListener('click', function () {
                            const curso = this.dataset.curso;
                            const seccion = this.dataset.seccion;
                            const subcarpeta = this.dataset.subcarpeta;

                            const targetLink = document.querySelector(`.cargar-subcarpeta[data-seccion="${seccion}"][data-subcarpeta="${subcarpeta}"]`);
                            if (targetLink) {
                                // Marcar activo
                                document.querySelectorAll('.cargar-subcarpeta').forEach(el => el.classList.remove('active-leccion'));
                                targetLink.classList.add('active-leccion');

                                // Expandir sección
                                const seccionId = targetLink.dataset.idseccion;
                                const collapse = document.getElementById(`collapse${seccionId}`);
                                if (collapse) {
                                    new bootstrap.Collapse(collapse, { toggle: true });
                                }

                                // Cargar lección
                                cargarSubcarpeta(targetLink);
                            }
                        });
                    });

                    setTimeout(asignarEventos, 50);
                });
        }

        function asignarEventosContenido() {
            const btnConcluir = document.querySelector('.concluir-btn');
            if (btnConcluir) {
                btnConcluir.addEventListener('click', function () {
                    const curso = this.dataset.curso;
                    const titulo = this.dataset.titulo;

                    fetch("completo.php", {
                        method: "POST",
                        headers: { "Content-Type": "application/x-www-form-urlencoded" },
                        body: new URLSearchParams({ curso, titulo })
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.error) return;

                        this.textContent = "✔ Lección completada";
                        this.classList.remove("btn-dark");
                        this.classList.add("btn-success");
                        this.disabled = true;

                        const siguiente = document.querySelector(`[data-id='${data.siguiente_id}']`);
                        if (siguiente) {
                            siguiente.classList.remove("bloqueado", "text-muted");
                            siguiente.addEventListener("click", listenerRef);
                        }
                    });
                });
            }

            document.querySelectorAll('.navegar-btn').forEach(btn => {
                btn.addEventListener('click', function () {
                    const curso = this.dataset.curso;
                    const seccion = this.dataset.seccion;
                    const subcarpeta = this.dataset.subcarpeta;

                    cargarSubcarpeta(
                        document.querySelector(`.cargar-subcarpeta[data-seccion="${seccion}"][data-subcarpeta="${subcarpeta}"]`)
                    );
                });
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
