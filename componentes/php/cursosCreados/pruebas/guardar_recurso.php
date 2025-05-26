
    <?php
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    include(__DIR__ . "/../../conexion.php");
    $conn = getConexion();

    // Comprobar conexión
    if (!$conn || $conn->connect_error) {
        die("❌ Error de conexión a la base de datos: " . $conn->connect_error);
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        echo "📥 Datos recibidos:
";
        print_r($_POST);

        $curso       = trim($_POST['curso'] ?? '');
        $titulo      = trim($_POST['titulo'] ?? 'sin_titulo');
        $descripcion = trim($_POST['descripcion'] ?? '');
        $url         = trim($_POST['url'] ?? '');
        $seccion     = trim($_POST['seccion'] ?? 'sin_seccion');

        if (empty($curso)) {
            die("❌ Curso no especificado.
");
        }

        $nombreTabla    = preg_replace('/[^A-Za-z0-9_]/', '_', $curso);
        $nombreSeccion  = preg_replace('/[^A-Za-z0-9_ -]/', ' ', $seccion);

        echo "
🛠 Procesando inserción en tabla: $nombreTabla
";

        // Verificar existencia de tabla
        $verificar = $conn->query("SHOW TABLES LIKE '$nombreTabla'");
        if (!$verificar || $verificar->num_rows === 0) {
            die("❌ La tabla '$nombreTabla' no existe.
");
        }

        // Obtener siguiente id_leccion
        $stmt = $conn->prepare("SELECT MAX(id_leccion) as max_id FROM `$nombreTabla`");
        $stmt->execute();
        $resultado = $stmt->get_result()->fetch_assoc();
        $nuevoId = ($resultado['max_id'] ?? 0) + 1;

        echo "📌 ID siguiente para '$nombreSeccion': $nuevoId
";

        // Insertar
        $stmtInsert = $conn->prepare("INSERT INTO `$nombreTabla` (nombre_seccion, nombre_leccion, id_leccion) VALUES (?, ?, ?)");
        $stmtInsert->bind_param("ssi", $nombreSeccion, $titulo, $nuevoId);

        if ($stmtInsert->execute()) {
            echo "✅ Lección '$titulo' insertado en sección '$nombreSeccion' con ID $nuevoId
";
        } else {
            echo "❌ Error al insertar: " . $stmtInsert->error . "
";
        }

        $rutaSeccion = __DIR__ . '/recursos/' . $nombreSeccion;
        $rutaRecurso = $rutaSeccion . '/' . $titulo;

        // Crear las carpetas si no existen
        if (!file_exists($rutaSeccion)) {
            mkdir($rutaSeccion, 0777, true);
        }
        if (!file_exists($rutaRecurso)) {
            mkdir($rutaRecurso, 0777, true);
        }
        // Guardar archivo info.txt
        $info = "Título: $titulo
        Descripción: $descripcion
        ";
        if (!empty($url)) {
            $info .= "URL: $url";
        }
        file_put_contents("$rutaRecurso/info.txt", $info);

        // Guardar archivo subido
        if (isset($_FILES['archivo']) && $_FILES['archivo']['error'] === UPLOAD_ERR_OK) {
            $archivoDestino = "$rutaRecurso/" . basename($_FILES['archivo']['name']);
            move_uploaded_file($_FILES['archivo']['tmp_name'], $archivoDestino);
        }

        echo "✅ Recurso guardado exitosamente en: $rutaRecurso";

    } else {
        echo "⚠️ Esta ruta solo acepta POST.
";
    }
    ?>

    