<?php
include(__DIR__ . "/../../php/conexion.php");
$conn = getConexion();

if (!$conn) {
    die("❌ Error de conexión: " . mysqli_connect_error());
}

// 1. Obtener todos los representantes médicos
$sql = "SELECT ID, Nombre, Correo_Electronico FROM usuario WHERE Puesto ='Representante medico'";
$resultado = $conn->query($sql);

$usuarios = [];

if ($resultado->num_rows > 0) {
    while ($fila = $resultado->fetch_assoc()) {
        $id = $fila['ID'];
        $usuarios[$id] = [
            'nombre' => $fila['Nombre'],
            'correo' => $fila['Correo_Electronico'],
            'cursos' => [] // Se llenará si tiene inscripciones
        ];
    }
}

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

        // Verificar si la tabla del curso existe
        $checkTable = $conn->query("SHOW TABLES LIKE '$nombreCurso'");
        $totalLecciones = 0;

        if ($checkTable && $checkTable->num_rows > 0) {
            $res = $conn->query("SELECT COUNT(*) AS total FROM `$nombreCurso`");
            if ($res) {
                $row = $res->fetch_assoc();
                $totalLecciones = (int)$row['total'];
            }
        }

        // Calcular el estado con badge
        if ($totalLecciones === 0 || $leccionesCompletadas === 0) {
            $estado = "<span class='badge bg-danger'>Sin progreso</span>";
        } else {
            $porcentaje = ($leccionesCompletadas / $totalLecciones) * 100;
            if ($porcentaje >= 100) {
                $estado = "<span class='badge bg-success'>Terminado</span>";
            } else {
                $estado = "<span class='badge bg-warning text-dark'>En progreso</span>";
            }
        }

        // Agregar curso al usuario si está en la lista
        if (isset($usuarios[$id])) {
            $usuarios[$id]['cursos'][] = [
                'curso' => $nombreCurso,
                'progreso' => $estado
            ];
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Representantes Médicos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div style="padding: 2%; width: 60%; margin: auto;">
    <h3 class="mb-4">Representantes Médicos</h3>
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
                            data-bs-target="#<?= $collapseId ?>" aria-expanded="<?= $index === 0 ? 'true' : '' ?>"
                            aria-controls="<?= $collapseId ?>">
                        <?= htmlspecialchars($usuario['nombre']) ?>
                    </button>
                </h2>
                <div id="<?= $collapseId ?>" class="accordion-collapse collapse <?= $showClass ?>"
                     aria-labelledby="<?= $headingId ?>" data-bs-parent="#accordionUsuarios">
                    <div class="accordion-body">
                        <?php if (empty($usuario['cursos'])): ?>
                            <div class="alert alert-warning text-center">
                                <i class="bi bi-info-circle"></i> No esta incrito a ningun curso.
                            </div>
                        <?php else: ?>
                            <table class="table text-center ">
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
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
