<?php
include(__DIR__ . "/../../php/conexion.php");
$conn = getConexion();

if (!$conn) {
    die("❌ Error de conexión: " . mysqli_connect_error());
}

// 1. Obtener todos los representantes médicos
$sql = "SELECT ID, Nombre, Correo_Electronico FROM usuario WHERE Puesto ='Representante medico'";
$resultado = $conn->query($sql);

$totalRepresentantes = $resultado->num_rows;

$usuarios = [];



if ($resultado->num_rows > 0) {
    while ($fila = $resultado->fetch_assoc()) {
        $id = $fila['ID'];
        $usuarios[$id] = [
            'nombre' => $fila['Nombre'],
            'correo' => $fila['Correo_Electronico'],
            'cursos' => []
        ];
    }
}

// Contadores para gráfica
$sinProgreso = 0;
$enProgreso = 0;
$terminado = 0;

// 2. Obtener inscripciones y progreso por curso
$sql4 = "
    SELECT 
        u.ID AS usuario_id,
        i.nombre_curso,
        i.Lecciones_Completadas
    FROM usuario u
    INNER JOIN inscripciones i ON u.ID = i.usuario_id
    WHERE u.Puesto = 'Representante medico'
";

$resultado4 = $conn->query($sql4);

if ($resultado4->num_rows > 0) {
    while ($fila = $resultado4->fetch_assoc()) {
        $id = $fila['usuario_id'];
        $nombreCurso = $fila['nombre_curso'];
        $leccionesCompletadas = (int)$fila['Lecciones_Completadas'];

        $checkTable = $conn->query("SHOW TABLES LIKE '$nombreCurso'");
        $totalLecciones = 0;

        if ($checkTable && $checkTable->num_rows > 0) {
            $res = $conn->query("SELECT COUNT(*) AS total FROM `$nombreCurso`");
            if ($res) {
                $row = $res->fetch_assoc();
                $totalLecciones = (int)$row['total'];
            }
        }

        if ($totalLecciones === 0 || $leccionesCompletadas === 0) {
            $estado = "<span class='badge bg-danger'>Sin progreso</span>";
            $sinProgreso++;
        } else {
            $porcentaje = ($leccionesCompletadas / $totalLecciones) * 100;
            if ($porcentaje >= 100) {
                $estado = "<span class='badge bg-success'>Terminado</span>";
                $terminado++;
            } else {
                $estado = "<span class='badge bg-warning text-dark'>En progreso</span>";
                $enProgreso++;
            }
        }

        if (isset($usuarios[$id])) {
            $usuarios[$id]['cursos'][] = [
                'curso' => $nombreCurso,
                'progreso' => $estado
            ];
        }
    }
}

// Calcular cursos únicos y total de personas inscritas
$cursosUnicos = [];
$personasInscritas = [];

foreach ($usuarios as $id => $usuario) {
    if (!empty($usuario['cursos'])) {
        $personasInscritas[] = $id; // ID de usuario con inscripción
        foreach ($usuario['cursos'] as $curso) {
            $cursosUnicos[] = $curso['curso'];
        }
    }
}

$cursosUnicos = array_unique($cursosUnicos);
$totalCursos = count($cursosUnicos);
$totalPersonas = count(array_unique($personasInscritas));

$inscritosPorCurso = [];

foreach ($usuarios as $usuario) {
    foreach ($usuario['cursos'] as $curso) {
        $nombre = $curso['curso'];
        if (!isset($inscritosPorCurso[$nombre])) {
            $inscritosPorCurso[$nombre] = 0;
        }
        $inscritosPorCurso[$nombre]++;
    }
}

arsort($inscritosPorCurso);

$labelsCursos = json_encode(array_keys($inscritosPorCurso), JSON_UNESCAPED_UNICODE);
$valoresCursos = json_encode(array_values($inscritosPorCurso));

// Para mostrar porcentajes en texto
function porcentaje($valor, $total) {
    return $total > 0 ? round(($valor / $total) * 100, 1) : 0;
}
$totalCursos = $sinProgreso + $enProgreso + $terminado;

$detallePorCurso = [];

foreach ($usuarios as $usuario) {
    foreach ($usuario['cursos'] as $curso) {
        $nombreCurso = $curso['curso'];
        if (!isset($detallePorCurso[$nombreCurso])) {
            $detallePorCurso[$nombreCurso] = [];
        }
        $detallePorCurso[$nombreCurso][] = [
            'nombre' => $usuario['nombre'],
            'correo' => $usuario['correo']
        ];
    }
}

$detallePorCursoJSON = json_encode($detallePorCurso, JSON_UNESCAPED_UNICODE);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Representantes Médicos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
<div style="padding: 2%; width: 60%; margin: auto;">
    <h3 class="mb-4">Representantes Médicos</h3>
    <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="pills-home-tab" data-bs-toggle="pill" data-bs-target="#pills-home" type="button" role="tab" aria-controls="pills-home" aria-selected="true">Resumen</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="pills-profile-tab" data-bs-toggle="pill" data-bs-target="#pills-profile" type="button" role="tab" aria-controls="pills-profile" aria-selected="false">Detalle</button>
        </li>
    </ul>
    <div class="tab-content" id="pills-tabContent">
        <!-- TAB RESUMEN CON GRÁFICA -->
        <div class="tab-pane fade show active" id="pills-home" role="tabpanel" aria-labelledby="pills-home-tab" tabindex="0">
            <div class="container text-center">
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="border rounded-4 p-3 shadow-sm bg-light">
                            <strong>Total representantes:</strong><br>
                            <?= $totalRepresentantes ?>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="border rounded-4 p-3 shadow-sm bg-light">
                            <strong>Cursos en plataforma:</strong><br>
                            <?= $totalCursos ?>
                        </div>
                    </div>
                </div>
                <div class="row align-items-start mb-4 ">
                    <div class="col-md-6">
                        <div class="border rounded-4 p-3 shadow-sm bg-light" style="max-width: 280px; margin: auto;">
                            <strong>Progreso general de los cursos</strong>
                            <canvas id="graficaResumen"></canvas>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="border rounded-4 p-3 shadow-sm bg-light">
                            <strong>Participación por curso</strong>
                            <canvas id="graficaBarras"></canvas>
                        </div>
                    </div>
                </div>
                <div class="row border rounded-4 p-3 shadow-sm bg-light">
                    <div class="col-12">
                        <h5 class="text-center">📊 Inscripciones por curso</h5>
                        <table class="table table-bordered text-center mt-3">
                            <thead class="table-light">
                                <tr>
                                    <th>Curso</th>
                                    <th>Representantes inscritos</th>
                                    <th>% Participación</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($inscritosPorCurso as $curso => $cantidad): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($curso) ?></td>
                                        <td><?= $cantidad ?></td>
                                        <td><?= porcentaje($cantidad, $totalRepresentantes) ?>%</td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <!-- Modal (por si quieres mantenerlo también) -->
            <div class="modal fade" id="modalDetalleCurso" tabindex="-1" aria-labelledby="modalDetalleLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header bg-primary text-white">
                            <h5 class="modal-title" id="modalDetalleLabel">Representantes inscritos</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                        </div>
                        <div class="modal-body" id="modalDetalleContenido"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- TAB DETALLE CON ACORDEÓN -->
        <div class="tab-pane fade" id="pills-profile" role="tabpanel" aria-labelledby="pills-profile-tab" tabindex="0">
            <div class="accordion accordion-flush" id="accordionUsuarios">
                <?php $index = 0; ?>
                <?php foreach ($usuarios as $id => $usuario): ?>
                    <?php
                    $collapseId = "collapseUser" . $id;
                    $headingId = "headingUser" . $id;
                    $showClass = ($index === 0) ? "show" : "";
                    $collapsed = ($index === 0) ? "" : "collapsed";
                    ?>
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="<?= $headingId ?>">
                            <button class="accordion-button <?= $collapsed ?>" type="button" data-bs-toggle="collapse"
                                    data-bs-target="#<?= $collapseId ?>" aria-expanded="<?= $index === 0 ? 'true' : 'false' ?>"
                                    aria-controls="<?= $collapseId ?>">
                                <?= htmlspecialchars($usuario['nombre']) ?>
                            </button>
                        </h2>
                        <div id="<?= $collapseId ?>" class="accordion-collapse collapse <?= $showClass ?>"
                             aria-labelledby="<?= $headingId ?>" data-bs-parent="#accordionUsuarios">
                            <div class="accordion-body">
                                <?php if (empty($usuario['cursos'])): ?>
                                    <div class="alert alert-warning text-center">
                                        <i class="bi bi-info-circle"></i> No está inscrito a ningún curso.
                                    </div>
                                <?php else: ?>
                                    <table class="table text-center">
                                        <thead>
                                        <tr>
                                            <th>Curso</th>
                                            <th>Progreso</th>
                                        </tr>
                                        </thead>
                                        <tbody class="table-group-divider">
                                        <?php foreach ($usuario['cursos'] as $curso): ?>
                                            <tr>
                                                <td><?= htmlspecialchars($curso['curso']) ?></td>
                                                <td><?= $curso['progreso'] ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <?php $index++; ?>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- Chart.js Script -->
<script>
document.addEventListener("DOMContentLoaded", () => {
    const tabResumen = document.getElementById('pills-home-tab');

    if (tabResumen) {
        tabResumen.addEventListener('shown.bs.tab', () => {
            if (typeof crearGrafico === 'function') crearGrafico();
            if (typeof crearGraficaBarras === 'function') crearGraficaBarras();
        });

        // Si el tab ya está activo al cargar la página
        if (tabResumen.classList.contains('active')) {
            crearGrafico();
            crearGraficaBarras();
        }
    }
});
</script>
<script>
document.addEventListener("DOMContentLoaded", () => {
    const tabResumen = document.getElementById('pills-home-tab');

    if (tabResumen) {
        tabResumen.addEventListener('shown.bs.tab', () => {
            if (typeof crearGrafico === 'function') crearGrafico();
            if (typeof crearGraficaBarras === 'function') crearGraficaBarras();
        });

        // Si el tab ya está activo al cargar
        if (tabResumen.classList.contains('active')) {
            crearGrafico();
            crearGraficaBarras();
        }
    }
});

window.chartCreado = false;
window.crearGrafico = function () {
  if (window.chartCreado) return;

  const ctx = document.getElementById('graficaResumen')?.getContext('2d');
  if (!ctx) return;

  window.graficaResumenInstancia = new Chart(ctx, {
    type: 'pie',
    data: {
      labels: ['Sin progreso', 'En progreso', 'Terminado'],
      datasets: [{
        data: [<?= $sinProgreso ?>, <?= $enProgreso ?>, <?= $terminado ?>],
        backgroundColor: ['#dc3545', '#ffc107', '#28a745']
      }]
    },
    options: {
      responsive: true,
      plugins: {
        legend: {
          position: 'bottom'
        }
      }
    }
  });

  window.chartCreado = true;
};

window.graficaBarrasCreada = false;
window.crearGraficaBarras = function () {
  if (window.graficaBarrasCreada) return;

  const barras = document.getElementById('graficaBarras');
  const labels = <?= $labelsCursos ?>;
  const datos = <?= $valoresCursos ?>;
  const detallePorCurso = <?= $detallePorCursoJSON ?>;

  if (barras && labels.length && datos.length) {
    window.graficaBarrasInstancia = new Chart(barras, {
      type: 'bar',
      data: {
        labels: labels,
        datasets: [{
          label: 'Representantes inscritos',
          data: datos,
          backgroundColor: 'rgba(54, 162, 235, 0.7)',
          borderColor: 'rgba(54, 162, 235, 1)',
          borderWidth: 1
        }]
      },
      options: {
        indexAxis: 'y',
        responsive: true,
        scales: {
          x: {
            beginAtZero: true,
            precision: 0
          }
        },
        plugins: {
          legend: { display: false }
        }
      }
    });

    // Cursor como puntero al pasar por encima
    barras.addEventListener('mousemove', function(evt) {
      const chart = window.graficaBarrasInstancia;
      const points = chart.getElementsAtEventForMode(evt, 'nearest', { intersect: true }, true);
      barras.style.cursor = points.length ? 'pointer' : 'default';
    });

    // Clic sobre barra → mostrar modal
    barras.addEventListener('click', function (evt) {
      const chart = window.graficaBarrasInstancia;
      const points = chart.getElementsAtEventForMode(evt, 'nearest', { intersect: true }, true);
      if (!points.length) return;

      const index = points[0].index;
      const cursoSeleccionado = chart.data.labels[index];
      const inscritos = detallePorCurso[cursoSeleccionado] || [];

      const modalContenido = document.getElementById('modalDetalleContenido');
      if (!modalContenido) return;

      if (inscritos.length === 0) {
        modalContenido.innerHTML = `
          <div class="alert alert-warning text-center">
            No hay representantes inscritos en <strong>${cursoSeleccionado}</strong>.
          </div>`;
      } else {
        let tabla = `
          <div class="table-responsive">
            <table class="table table-bordered text-center">
              <thead><tr><th>Nombre</th><th>Correo</th></tr></thead>
              <tbody>`;
        inscritos.forEach(rep => {
          tabla += `<tr><td>${rep.nombre}</td><td>${rep.correo}</td></tr>`;
        });
        tabla += `</tbody></table></div>`;

        modalContenido.innerHTML = `
          <div class="alert alert-info text-center">
            👥 Representantes inscritos en <strong>${cursoSeleccionado}</strong>
          </div>${tabla}`;
      }

      const modal = new bootstrap.Modal(document.getElementById('modalDetalleCurso'));
      modal.show();
    });

    window.graficaBarrasCreada = true;
  }
};
</script>

</body>
</html>
