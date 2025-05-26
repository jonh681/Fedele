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
        $cursos[] = [
            'nombre_curso' => $row['nombre_curso'],
            'fecha_inicio' => $row['fecha_inicio'],
            'fecha_fin' => $row['fecha_fin'],
            'Lecciones_Completadas' => (int) $row['Lecciones_Completadas'],
        ];
    }

echo json_encode($cursos); // devuelve lista de cursos inscritos
?>
