<?php
session_start();
require 'conexion.php';
$conn = getConexion();

if (isset($_POST['curso']) && isset($_SESSION['usuario_id'])) {
    $curso = $_POST['curso'];
    $usuario_id = intval($_SESSION['usuario_id']);

    // Verificar si ya está inscrito
    $check = $conn->prepare("SELECT * FROM inscripciones WHERE usuario_id = ? AND nombre_curso = ?");
    $check->bind_param("is", $usuario_id, $curso);
    $check->execute();
    $result = $check->get_result();

    $inicio = $_POST['inicio'] ?? null;
    $fin = $_POST['fin'] ?? null;

    if ($result->num_rows === 0) {
        $stmt = $conn->prepare("INSERT INTO inscripciones (usuario_id, nombre_curso, fecha_inicio, fecha_fin) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("isss", $usuario_id, $curso, $inicio, $fin);
        if ($stmt->execute()) {
            echo "success"; // <- clave para JS
        } else {
            echo "❌ Error al inscribirse: " . $stmt->error;
        }
        $stmt->close();
    } else {
        echo "⚠️ Ya estás inscrito en este curso.";
    }

    $check->close();
    $conn->close();
} else {
    echo "❌ Sesión no iniciada o datos faltantes.";
}
?>
