

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

            $paginas = json_decode($_POST['paginas'], true);

            if (!$paginas) {
                die("❌ Error al decodificar las páginas.");
            }

            $exito = true;
            // Procesar cada página
            foreach ($paginas as $index => $pagina) {
                $texto = $pagina['texto'];
                $imagen = $pagina['imagen'];  // El nombre de la imagen
                $urlPagina = $pagina['url'];


                // Guardar los datos de la página en el sistema de archivos o base de datos
                $rutaSeccion = __DIR__ . '/recursos/' . $nombreSeccion;
                $rutaRecurso = $rutaSeccion . '/' . $tituloRecurso;

                // Crear las carpetas si no existen
                if (!file_exists($rutaSeccion)) {
                    mkdir($rutaSeccion, 0777, true);
                    chmod($rutaRecurso, 0755);
                }
                if (!file_exists($rutaRecurso)) {
                    mkdir($rutaRecurso, 0777, true);
                    chmod($rutaRecurso, 0755);
                }

                $info = "Título: $tituloRecurso
Descripción: $descripcion
URL: $url";

                file_put_contents("$rutaRecurso/info.txt", $info);

                // Crear una carpeta para cada página
                $rutaPagina = $rutaRecurso . '/hoja' . ($index + 1); // Crear una carpeta por cada página
                if (!file_exists($rutaPagina)) {
                    mkdir($rutaPagina, 0777, true);  // Crear la carpeta de la página
                    chmod($rutaRecurso, 0755);

                }

                // Guardar el contenido de la página
                $infoPagina = "Texto: $texto
URL: $urlPagina";
                file_put_contents($rutaPagina . "/pagina$index.txt", $infoPagina);  // Guardar el archivo de texto con la información de la página

                // Si hay archivos subidos (imágenes, documentos), guardarlos
                if (isset($_FILES["imagen$index"]) && $_FILES["imagen$index"]['error'] === UPLOAD_ERR_OK) {
                    // Ruta donde guardar el archivo
                    $archivoDestino = $rutaPagina . "/" . basename($_FILES["imagen$index"]['name']);
                    move_uploaded_file($_FILES["imagen$index"]['tmp_name'], $archivoDestino);
                }
            }
        } else{
            echo "📚 No es un libro, guardando de forma normal...
";

            $rutaSeccion = __DIR__ . '/recursos/' . $nombreSeccion;
            $rutaRecurso = $rutaSeccion . '/' . $tituloRecurso;

            // Crear las carpetas si no existen
            if (!file_exists($rutaSeccion)) {
                mkdir($rutaSeccion, 0777, true);
                chmod($rutaRecurso, 0755);
            }
            if (!file_exists($rutaRecurso)) {
                mkdir($rutaRecurso, 0777, true);
                chmod($rutaRecurso, 0755);
            }
            // Guardar archivo info.txt
            $info = "Título: $tituloRecurso
Descripción: $descripcion
URL: $url";

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
    