<?php
session_start();
require 'conexion.php';
$conn = getConexion();

if (!isset($_SESSION['usuario_id'])) {
    echo json_encode([]);
    exit;
}

$usuario_id = $_SESSION['usuario_id'];

$query = $conn->prepare("SELECT nombre_curso, Lecciones_Completadas, fecha_inicio, fecha_fin FROM inscripciones WHERE usuario_id = ?");
$query->bind_param("i", $usuario_id);
$query->execute();
$result = $query->get_result();

$cursos = [];

while ($row = $result->fetch_assoc()) {
    $nombreCurso = $row['nombre_curso'];
    $leccionesCompletadas = (int)$row['Lecciones_Completadas'];

    // Obtener número total de lecciones en la tabla del curso
    $totalLecciones = 0;
    $stmtLecciones = $conn->prepare("SELECT COUNT(*) FROM `$nombreCurso`");
    if ($stmtLecciones) {
        $stmtLecciones->execute();
        $stmtLecciones->bind_result($totalLecciones);
        $stmtLecciones->fetch();
        $stmtLecciones->close();
    }

    // Calcular el porcentaje
    $porcentaje = ($totalLecciones > 0) ? round(($leccionesCompletadas / $totalLecciones) * 100) : 0;

    // Agregar curso con porcentaje incluido
    $cursos[] = [
        'nombre_curso' => $nombreCurso,
        'fecha_inicio' => $row['fecha_inicio'],
        'fecha_fin' => $row['fecha_fin'],
        'Lecciones_Completadas' => $leccionesCompletadas,
        'Total_Lecciones' => $totalLecciones,
        'porcentaje' => $porcentaje
    ];
}

echo json_encode($cursos);
?>
