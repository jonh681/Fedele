

    <?php
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    include(__DIR__ . "/../../conexion.php");
    $conn = getConexion();

    // Función robusta para normalizar texto para uso en nombres de carpetas, BD, etc.
    function normalizarNombre($texto) {
        $texto = mb_convert_encoding($texto, 'UTF-8', 'auto');
        $texto = strtr($texto, [
            'á' => 'a', 'é' => 'e', 'í' => 'i',
            'ó' => 'o', 'ú' => 'u', 'Á' => 'A',
            'É' => 'E', 'Í' => 'I', 'Ó' => 'O',
            'Ú' => 'U', 'ñ' => 'n', 'Ñ' => 'N'
        ]);
        $texto = preg_replace('/\s+/', ' ', $texto);
        $texto = trim($texto);
        $texto = preg_replace('/[^A-Za-z0-9_-]/', '', $texto);
        return strtolower($texto);
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        echo "📥 Datos recibidos:
";
        print_r($_POST);

        // Obtener y normalizar título
        $tituloOriginal = trim($_POST['tituloRecurso'] ?? 'sin_titulo');
        $tituloRecurso = normalizarNombre($tituloOriginal); // Usado en BD y como carpeta

        $esLibro = $_POST['esLibro'];
        $numPaginas = $_POST['numPaginas'] ?? 1;
        $descripcion = trim($_POST['descripcion'] ?? '');
        $url = trim($_POST['url'] ?? '');

        // Validación y limpieza de sección
        if (!isset($_POST['seccion'])) {
            die("🚫 El campo 'seccion' no fue enviado.");
        }

        $seccionCruda = $_POST['seccion'];
        $seccionPreliminar = preg_replace('/\s+/', ' ', $seccionCruda);
        $seccionPreliminar = trim($seccionPreliminar);
        $nombreSeccion = normalizarNombre($seccionPreliminar);

        // Curso
        $cursoOriginal = trim($_POST['curso'] ?? '');
        $nombreTabla = normalizarNombre($cursoOriginal);

        echo "
🧾 Título original: '$tituloOriginal'";
        echo "
📁 Título normalizado: '$tituloRecurso'";
        echo "
📁 Sección normalizada: '$nombreSeccion'";
        echo "
📁 Tabla: '$nombreTabla'";

        if (empty($tituloRecurso)) {
            die("❌ Título no especificado.");
        }

        echo "
🛠 Procesando inserción en tabla: $nombreTabla";

        // Verificar tabla
        $verificar = $conn->query("SHOW TABLES LIKE '$nombreTabla'");
        if (!$verificar || $verificar->num_rows === 0) {
            die("❌ La tabla '$nombreTabla' no existe.");
        }

        // Obtener siguiente ID
        $stmt = $conn->prepare("SELECT MAX(id_leccion) as max_id FROM `$nombreTabla`");
        $stmt->execute();
        $resultado = $stmt->get_result()->fetch_assoc();
        $nuevoId = ($resultado['max_id'] ?? 0) + 1;

        echo "
📌 ID siguiente para '$nombreSeccion': $nuevoId";

        // Insertar lección con título normalizado
        $stmtInsert = $conn->prepare("INSERT INTO `$nombreTabla` (nombre_seccion, nombre_leccion, id_leccion, nombre_seccion_original, nombre_leccion_original) VALUES (?, ?, ?, ?, ?)");
        $stmtInsert->bind_param("ssiss", $nombreSeccion, $tituloRecurso, $nuevoId, $seccionPreliminar, $tituloOriginal);

        if ($stmtInsert->execute()) {
            echo "
✅ Lección '$tituloRecurso' insertada en sección '$nombreSeccion' con ID $nuevoId";
        } else {
            echo "
❌ Error al insertar: " . $stmtInsert->error;
        }
        
        if ($esLibro === "true"){
            echo "📚 Si es un libro, guardando libro...";
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
        }else{
            echo "📚 No es un libro, guardando de forma normal...";
            // Rutas con nombres normalizados
            $rutaSeccion = __DIR__ . '/recursos/' . $nombreSeccion;
            $rutaRecurso = $rutaSeccion . '/' . $tituloRecurso;

            if (!file_exists($rutaSeccion)) {
                mkdir($rutaSeccion, 0777, true);
                chmod($rutaSeccion, 0755);
            }
            if (!file_exists($rutaRecurso)) {
                mkdir($rutaRecurso, 0777, true);
                chmod($rutaRecurso, 0755);
            }

            // Guardar info.txt con título original
            $info = "Título: $tituloOriginal
Descripción: $descripcion
URL: $url";
            file_put_contents("$rutaRecurso/info.txt", $info);

            if (isset($_FILES['archivo']) && $_FILES['archivo']['error'] === UPLOAD_ERR_OK) {
                $archivoDestino = "$rutaRecurso/" . basename($_FILES['archivo']['name']);
                move_uploaded_file($_FILES['archivo']['tmp_name'], $archivoDestino);
            }

            echo "
✅ Recurso guardado exitosamente en: $rutaRecurso";
            }

    } else {
        echo "⚠️ Esta ruta solo acepta POST.";
    }
    ?>

    