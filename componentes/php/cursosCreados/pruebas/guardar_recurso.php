
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
        echo "📥 Datos recibidos:";
        print_r($_POST);

        $tituloRecurso = trim($_POST['tituloRecurso'] ?? 'sin_titulo');
        $esLibro = $_POST['esLibro'];
        $numPaginas = $_POST['numPaginas'] ?? 1;
        $descripcion = trim($_POST['descripcion'] ?? '');
        $url = trim($_POST['url'] ?? '');
        $seccion = trim($_POST['seccion'] ?? 'sin_seccion');
        $curso = trim($_POST['curso'] ?? '');

        echo "📥 es libro?: $esLibro";
        var_dump($esLibro);


        if (empty($tituloRecurso)) {
            die("❌ Título no especificado.");
        }

        $nombreTabla    = preg_replace('/[^A-Za-z0-9_]/', '_', $curso);
        $nombreSeccion  = preg_replace('/[^A-Za-z0-9_ -]/', ' ', $seccion);

        echo "🛠 Procesando inserción en tabla: $nombreTabla";
        echo "tu back dice que el recurso es libro si o no? $esLibro";
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
            echo "❌ Error al insertar: " . $stmtInsert->error . "\n";
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
    
                 // Guardar el contenido de la hoja
                $infoHoja = "Contenido de la hoja $i";  // Esto es solo un ejemplo, puedes agregar datos reales
                if (!empty($url)) {
                    $infoHoja .= "\nURL: $url";  // Guardar el URL en el texto de la hoja
                }

                // Guardar el archivo de texto con la información de la hoja
                file_put_contents($rutaHoja . "/hoja$i.txt", $infoHoja);

                // Si hay archivos subidos (imágenes, documentos), guardarlos
                if (isset($_FILES["imagen$i"]) && $_FILES["imagen$i"]['error'] === UPLOAD_ERR_OK) {
                    // Ruta donde guardar el archivo
                    $archivoDestino = $rutaHoja . "/" . basename($_FILES["imagen$i"]['name']);
                    move_uploaded_file($_FILES["imagen$i"]['tmp_name'], $archivoDestino);
                    echo "📎 Imagen o archivo guardado en: $archivoDestino\n";
                }

                echo "📄 Hoja $i guardada en: $rutaHoja\n";
                
            }
        } else{
            echo "📚 No es un libro, guardando de forma normal...\n";

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
//         $rutaSeccion = __DIR__ . '/recursos/' . $nombreSeccion;
//         $rutaRecurso = $rutaSeccion . '/' . $tituloRecurso;

//         // Crear las carpetas si no existen
//         if (!file_exists($rutaSeccion)) {
//             mkdir($rutaSeccion, 0777, true);
//         }
//         if (!file_exists($rutaRecurso)) {
//             mkdir($rutaRecurso, 0777, true);
//         }
//         // Guardar archivo info.txt
//         $info = "Título: $tituloRecurso
//         Descripción: $descripcion
//         ";
//         if (!empty($url)) {
//             $info .= "URL: $url";
//         }
//         file_put_contents("$rutaRecurso/info.txt", $info);

//         // Guardar archivo subido
//         if (isset($_FILES['archivo']) && $_FILES['archivo']['error'] === UPLOAD_ERR_OK) {
//             $archivoDestino = "$rutaRecurso/" . basename($_FILES['archivo']['name']);
//             move_uploaded_file($_FILES['archivo']['tmp_name'], $archivoDestino);
//         }

//         echo "✅ Recurso guardado exitosamente en: $rutaRecurso";

//     } else {
//         echo "⚠️ Esta ruta solo acepta POST.
// ";
//     }
    ?>