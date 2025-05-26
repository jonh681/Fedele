<?php
session_start();
include("conexion.php");
$conn = getConexion();

$usuario = $_SESSION['usuario_id'] ?? null;
$curso = $_POST['curso'] ?? null;
$titulo = $_POST['titulo'] ?? null;

if ($usuario && $curso && $titulo) {
    // Actualizar progreso
    $stmt = $conn->prepare("
        UPDATE inscripciones
        SET 
            Lecciones_Completadas = Lecciones_Completadas + 1,
            Ultima_Leccion = ?
        WHERE usuario_id = ? AND nombre_curso = ?
    ");
    $stmt->bind_param("sis", $titulo, $usuario, $curso);
    $stmt->execute();
    $stmt->close();

    // Obtener nuevas lecciones completadas
    $stmt2 = $conn->prepare("SELECT Lecciones_Completadas FROM inscripciones WHERE usuario_id = ? AND nombre_curso = ?");
    $stmt2->bind_param("is", $usuario, $curso);
    $stmt2->execute();
    $stmt2->bind_result($leccionesCompletadas);
    $stmt2->fetch();
    $stmt2->close();

    // Obtener ID actual
    $stmt3 = $conn->prepare("SELECT id_leccion FROM `$curso` WHERE nombre_leccion = ?");
    $stmt3->bind_param("s", $titulo);
    $stmt3->execute();
    $stmt3->bind_result($idActual);
    $stmt3->fetch();
    $stmt3->close();

    $siguienteId = $idActual + 1;

    // Contar total de subcarpetas (lecciones)
    $nombreCursoLimpio = preg_replace('/[^a-zA-Z0-9_-]/', '_', $curso);
    $ruta = __DIR__ . "/cursosCreados/$nombreCursoLimpio/recursos";
    function contarLecciones($dir) {
        $total = 0;
        foreach (array_diff(scandir($dir), ['.', '..']) as $seccion) {
            $subRuta = "$dir/$seccion";
            if (is_dir($subRuta)) {
                foreach (array_diff(scandir($subRuta), ['.', '..']) as $sub) {
                    if (is_dir("$subRuta/$sub")) $total++;
                }
            }
        }
        return $total;
    }

    $totalLecciones = contarLecciones($ruta);
    $porcentaje = ($totalLecciones > 0) ? round(($leccionesCompletadas / $totalLecciones) * 100) : 0;

    echo json_encode([
        "lecciones" => $leccionesCompletadas,
        "total" => $totalLecciones,
        "porcentaje" => $porcentaje,
        "siguiente_id" => $siguienteId
    ]);
} else {
    echo json_encode(["error" => "Faltan datos"]);
}

?>
