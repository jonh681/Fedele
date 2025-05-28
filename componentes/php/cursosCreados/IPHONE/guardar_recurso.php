

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

        $tituloRecurso = trim($_POST['tituloRecurso'] ?? 'sin_titulo');
        $esLibro = $_POST['esLibro'];
        $numPaginas = $_POST['numPaginas'] ?? 1;
        $descripcion = trim($_POST['descripcion'] ?? '');
        $url = trim($_POST['url'] ?? '');
        $seccion = trim($_POST['seccion'] ?? 'sin_seccion');
        $curso = trim($_POST['curso'] ?? '');

        if (empty($tituloRecurso)) {
            die("❌ Título no especificado.");
        }

        $nombreTabla    = preg_replace('/[^A-Za-z0-9_]/', '_', $curso);
        $nombreSeccion  = preg_replace('/[^A-Za-z0-9_ -]/', ' ', $seccion);

        echo "
        🛠 Procesando inserción en tabla: $nombreTabla";

        // Verificar existencia de tabla
        $verificar = $conn->query("SHOW TABLES LIKE '$nombreTabla'");
        if (!$verificar || $verificar->num_rows === 0) {
            die("❌ La tabla '$nombreTabla' no existe.");
        }

        // Obtener siguiente id_leccion
        $stmt = $conn->prepare("SELECT MAX(id_leccion) as max_id FROM `$nombreTabla`");
        $stmt->execute();
        $resultado = $stmt->get_result()->fetch_assoc();
        $nuevoId = ($resultado['max_id'] ?? 0) + 1;

        echo "📌 ID siguiente para '$nombreSeccion': $nuevoId";

        // Insertar
        $stmtInsert = $conn->prepare("INSERT INTO `$nombreTabla` (nombre_seccion, nombre_leccion, id_leccion) VALUES (?, ?, ?)");
        $stmtInsert->bind_param("ssi", $nombreSeccion, $tituloRecurso, $nuevoId);

         if ($stmtInsert->execute()) {
            echo "✅ Lección '$tituloRecurso' insertado en sección '$nombreSeccion' con ID $nuevoId";
        } else {
            echo "❌ Error al insertar: " . $stmtInsert->error . "
";
        }

        if ($esLibro === "true"){
            echo "📚 Es un libro, guardando en carpeta de libro...";

            // $rutaLibro = __DIR__ . '/recursos/libros/' . $tituloRecurso;
            $rutaSeccion = __DIR__ . '/recursos/' . $nombreSeccion;
            $rutaRecurso = $rutaSeccion . '/' . $tituloRecurso;

            // Crear las carpetas si no existen
            if (!file_exists($rutaSeccion)) {
                mkdir($rutaSeccion, 0777, true);
            }
            if (!file_exists($rutaRecurso)) {
                mkdir($rutaRecurso, 0777, true);
            }

            for ($i = 1; $i <= $numPaginas; $i++) {
                $rutaHoja = $rutaRecurso . '/hoja' . $i;
                if (!file_exists($rutaHoja)) {
                    mkdir($rutaHoja, 0777, true);  // Crear la carpeta de cada hoja
                }
    
                // Guardar los datos de la hoja (por ejemplo, contenido o archivos)
                $infoHoja = "Contenido de la hoja $i";  // Esto es solo un ejemplo, puedes agregar datos reales
                file_put_contents($rutaHoja . "/hoja$i.txt", $infoHoja);  // Guardar un archivo de texto como ejemplo
    
                echo "📄 Hoja $i guardada en: $rutaHoja
";
            }
        } else{
            echo "📚 No es un libro, guardando de forma normal...
";

            $rutaSeccion = __DIR__ . '/recursos/' . $nombreSeccion;
            $rutaRecurso = $rutaSeccion . '/' . $tituloRecurso;

            // Crear las carpetas si no existen
            if (!file_exists($rutaSeccion)) {
                mkdir($rutaSeccion, 0777, true);
            }
            if (!file_exists($rutaRecurso)) {
                mkdir($rutaRecurso, 0777, true);
            }
            // Guardar archivo info.txt
            $info = "Título: $tituloRecurso
            Descripción: $descripcion";

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
        }

    }  else {
        echo "⚠️ Esta ruta solo acepta POST.";
    } 
    ?>
    