<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['nombreCurso'] ?? 'curso_sin_nombre';

    // Sanitiza el nombre para que sea válido como archivo/carpeta
    $nombreLimpiado = preg_replace('/[^A-Za-z0-9_-]/', '_', $nombre);
    $nombreArchivo = $nombreLimpiado . '.php';

    $nombreCorto = $_POST['nombreCorto'] ?? 'sin_nombre_corto';
    $fechaInicio = $_POST['fechaInicio'] ?? 'sin_fecha_inicio';
    $fechaFin = $_POST['fechaFin'] ?? 'sin_fecha_fin';
    $descripcion = $_POST['descripcion'] ?? 'sin_descripcion';
    $secciones = $_POST['secciones'] ?? 1;
    $secciones = is_numeric($secciones) ? intval($secciones) : 1;

    $rutaDirectorio = __DIR__ . '/cursosCreados/' . $nombreLimpiado;
    $rutaArchivo = $rutaDirectorio . '/' . $nombreArchivo;
    $rutaCSS = $rutaDirectorio . '/diseñoContenido.css'; // Ruta para el archivo CSS
    $rutaPHP1 = $rutaDirectorio .'/guardar_titulo.php';
    $rutaPHP2 = $rutaDirectorio .'/obtener_titulo.php';
    $rutaTXT = $rutaDirectorio .'/titulos.txt';

    // Crear carpeta si no existe
    if (!file_exists($rutaDirectorio) && !mkdir($rutaDirectorio, 0755, true)) {
        echo "Error al crear el directorio.";
        exit;
    }

    // Comienza el contenido HTML
    $contenidoHTML = "<!DOCTYPE html>
<html lang=\"es\">
<head>
    <meta charset=\"UTF-8\">
    <title>$nombre</title>
    <link href=\"https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css\" rel=\"stylesheet\" />
    <link href=\"https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css\" rel=\"stylesheet\" />
    <link rel=\"stylesheet\" href=\"diseñoContenido.css\">
</head>
<body>
    <h1 class=\"text-center my-4\">Bienvenido al curso: $nombre</h1>
    <div class=\"container\">
";

    // Secciones generadas dinámicamente
for ($i = 1; $i <= $secciones; $i++) {
    $contenidoHTML .= "
    <div class=\"contentPrincipal\">
        <p class=\"d-inline-flex gap-1\">
            <div class=\"subtemas\">
                <button id=\"toggleBtn$i\" class=\"btn btn-primary boton\" type=\"button\" data-bs-toggle=\"collapse\" data-bs-target=\"#collapseExample$i\" aria-expanded=\"false\" aria-controls=\"collapseExample$i\">
                    <i id=\"toggleIcon$i\" class=\"bi bi-chevron-right\"></i>
                </button>
                <div class=\"editable-title\" id=\"titleContainer$i\">
                    <span id=\"titleText$i\">Sección $i</span>
                    <i class=\"bi bi-pencil-fill edit-icon\" id=\"editIcon$i\"></i>
                </div>
            </div>
        </p>

        <div class=\"collapse\" id=\"collapseExample$i\">
            <div class=\"card card-body\" style=\"border: none;\">
                <div class=\"line-container\">
                    <div class=\"line\"></div>
                    <div class=\"button-wrapper\">
                        <button class=\"custom-button\" data-bs-toggle=\"modal\" data-bs-target=\"#exampleModal\">
                            <i class=\"bi bi-plus\"></i>
                            Añadir una actividad o recurso
                        </button>
                    </div>
                    <div class=\"line\"></div>
                </div>
            </div>
        </div>
    </div>
    ";
}

// Modal HTML fuera del ciclo, pero dentro del contenido
$contenidoHTML .= "
<div class=\"modal fade\" id=\"exampleModal\" tabindex=\"-1\" aria-labelledby=\"exampleModalLabel\" aria-hidden=\"true\">
    <div class=\"modal-dialog modal-xl\">
        <div class=\"modal-content\">
            <div class=\"modal-header\">
                <h1 class=\"modal-title fs-5\" id=\"exampleModalLabel\">Añadir una actividad o recurso</h1>
                <button type=\"button\" class=\"btn-close\" data-bs-dismiss=\"modal\" aria-label=\"Close\"></button>
            </div>
            <div class=\"modal-body\">
                <div class=\"col-4\" style=\"width: auto;\">
                    <div class=\"list-group list-group-flush\" id=\"list-tab\" role=\"tablist\" style=\"width: auto;\">
                        <a class=\"list-group-item list-group-item-action\" id=\"list-home-list\" data-bs-toggle=\"offcanvas\" data-bs-target=\"#offcanvasTopActividades\" href=\"#list-home\" role=\"button\" aria-controls=\"list-home\">
                            Actividades: <br>
                            En esta sección podrás subir exámenes, talleres, glosarios, entre otras actividades.
                        </a>
                        <a class=\"list-group-item list-group-item-action\" id=\"list-profile-list\" data-bs-toggle=\"offcanvas\" data-bs-target=\"#offcanvasTopRecursos\" href=\"#list-profile\" role=\"button\" aria-controls=\"list-profile\">
                            Recurso: <br>
                            En esta sección podrás subir Archivos, Áreas de texto y medios, carpetas, libros, URL, entre otros recursos.
                        </a>
                    </div>
                </div>
            </div>
            <div class=\"modal-footer\">
            </div>
        </div>    
    </div>

    <div class=\"offcanvas offcanvas-top\" tabindex=\"-1\" id=\"offcanvasTopActividades\" aria-labelledby=\"offcanvasTopLabel\">
        <div class=\"offcanvas-header\">
            <h5 class=\"offcanvas-title\" id=\"offcanvasTopLabel\">Crear Actividad</h5>
            <button type=\"button\" class=\"btn-close\" data-bs-dismiss=\"offcanvas\" aria-label=\"Close\"></button>
        </div>
        <div class=\"offcanvas-body\">
            <div class=\"container text-center\" style=\"color: rgb(255, 0, 126); font-size: large;\">
                <div class=\"row\">
                    <div class=\"col recurso-item-pink\" >
                        <i class=\"bi bi-file-text\"></i>
                        <span>examenes</span>
                    </div>
                    <div class=\"col recurso-item-pink\" >
                        <i class=\"bi bi-journal\"></i>
                        <span>Tareas</span>
                    </div>
                    <div class=\"col recurso-item-pink\" >
                        <i class=\"bi bi-person-video3\"></i>
                        <span>Talleres</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class=\"offcanvas offcanvas-top\" tabindex=\"-1\" id=\"offcanvasTopRecursos\" aria-labelledby=\"offcanvasTopLabel\">
        <div class=\"offcanvas-header\">
            <h5 class=\"offcanvas-title\" id=\"offcanvasTopLabel\">Crear Recurso</h5>
            <button type=\"button\" class=\"btn-close\" data-bs-dismiss=\"offcanvas\" aria-label=\"Close\"></button>
        </div>
        <div class=\"offcanvas-body\">
            <div class=\"container text-center\" style=\"color: rgb(3, 187, 133); font-size: large;\">
                <div class=\"row\">
                    <div class=\"col recurso-item\">
                        <i class=\"bi bi-file-text\"></i>
                        <span>Archivos</span>
                    </div>
                    <div class=\"col recurso-item\">
                        <i class=\"bi bi-body-text\"></i>
                        <span>Texto</span>
                    </div>
                    <div class=\"col recurso-item\">
                        <i class=\"bi bi-archive-fill\"></i>
                        <span>Media</span>
                    </div>
                    <div class=\"col recurso-item\">
                        <i class=\"bi bi-globe\"></i>
                        <span>URL</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
";

    $contenidoHTML .= "
    </div> <!-- end container -->
     <script>
        for (let i = 1; i <= <?php echo $secciones; ?>; i++) {
            const titleContainer = document.getElementById('titleContainer' + i);
            const titleText = document.getElementById('titleText' + i);
            const editIcon = document.getElementById('editIcon' + i);

            // Verifica si el contenedor y los elementos existen
            if (titleContainer && titleText && editIcon) {
                // Solicitar el título desde el servidor cuando la página cargue
                fetch('obtener_titulo.php?id=' + i)
                    .then(response => response.text())
                    .then(data => {
                        titleText.textContent = data;  // Actualiza el texto con el valor recibido del servidor
                    })
                    .catch(error => console.error('Error al obtener el título:', error));

                editIcon.addEventListener('click', () => {
                    // Verifica si ya hay un input, si no hay, crea uno nuevo
                    if (titleContainer.contains(titleText)) {
                        // Crear input con el texto actual
                        const input = document.createElement('input');
                        input.type = 'text';
                        input.value = titleText.textContent;
                        input.className = 'edit-input';

                        // Reemplazar el texto por el input
                        titleContainer.replaceChild(input, titleText);
                        input.focus();

                        // Función para guardar el cambio
                        const save = () => {
                            const newText = input.value.trim() || 'Sin título';
                            titleText.textContent = newText; // Actualiza el texto del título

                            // Enviar el nuevo título al servidor con AJAX
                            const xhr = new XMLHttpRequest();
                            xhr.open('POST', 'guardar_titulo.php', true);
                            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                            xhr.send('id=' + i + '&titulo=' + encodeURIComponent(newText));

                            xhr.onload = function () {
                                if (xhr.status === 200) {
                                    console.log('Título guardado correctamente');
                                } else {
                                    console.log('Error al guardar el título');
                                }
                            };

                            // Solicitar el título actualizado después de guardar
                            fetch('obtener_titulo.php?id=' + i)
                                .then(response => response.text())
                                .then(data => {
                                    titleText.textContent = data;  // Actualiza el texto con el valor recibido del servidor
                                })
                                .catch(error => console.error('Error al obtener el título actualizado:', error));
                        };

                        // Guardar al presionar Enter o cuando se pierda el foco
                        input.addEventListener('blur', save);
                        input.addEventListener('keydown', (e) => {
                            if (e.key === 'Enter') {
                                save();
                            }
                        });
                    }
                });
            }
        }


        for (let i = 1; i <= <?php echo $secciones; ?>; i++) {
            const button = document.getElementById('toggleBtn' + i);
            const icon = document.getElementById('toggleIcon' + i);
            const content = document.getElementById('collapseExample' + i);

            button.addEventListener('click', () => {
                // Cambiar ícono
                if (icon.classList.contains('bi-chevron-right')) {
                    icon.classList.remove('bi-chevron-right');
                    icon.classList.add('bi-chevron-down');
                } else {
                    icon.classList.remove('bi-chevron-down');
                    icon.classList.add('bi-chevron-right');
                }
            });
        }
    </script>
    <script src=\"https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js\"></script>
</body>
</html>";

    // Contenido para el archivo CSS
$contenidoCSS = "
    .contentPrincipal{
        border: 1px solid rgb(227, 226, 226);
        padding: 1%;
        border-radius: 10px;
    }
    .subtemas{
        display: flex;
        gap: 3%;
    }
    
    .subtemas p{
        margin-top: 2%;
    }
    .boton{
        width: 60px;
        height: 60px;
        border-radius: 50%;
        background-color: #e9f1fb;
        font-size: 1.5rem;
        color: #0d6efd;
        transition: background-color 0.3s, color 0.3s;
    }
    
    .line-container {
        display: flex;
        align-items: center;
        justify-content: center;
        margin-top: 50px;
    }
    
    .line {
        flex-grow: 1;
        height: 1px;
        border-top: 1px dotted #ccc;
    }
    
    .custom-button {
        background-color: #e0edfb;
        color: #0d3e6e;
        font-weight: bold;
        border-radius: 20px;
        padding: 6px 16px;
        border: none;
        display: flex;
        align-items: center;
        gap: 6px;
        font-size: 0.9rem;
    }
    
    .custom-button:hover {
        background-color: #c8def7;
        color: #0b2e52;
    }
    
    .button-wrapper {
        padding: 0 10px;
    }
    .editable-title {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 1.2rem;
        font-weight: bold;
    }
    
    .edit-input {
        font-size: 1.2rem;
        font-weight: bold;
        padding: 2px 4px;
    }
    
    .edit-icon {
        cursor: pointer;
        color: #333;
    }
    
    .recurso-item {
        padding: 20px;
        border-radius: 10px;
        transition: background-color 0.3s ease, transform 0.2s ease;
    }
    
    .recurso-item:hover {
        background-color: rgba(3, 187, 133, 0.1);
        cursor: pointer;
        transform: translateY(-3px);
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    }
    
    .recurso-item-pink {
        padding: 20px;
        border-radius: 10px;
        transition: background-color 0.3s ease, transform 0.2s ease;
    }
    
    .recurso-item-pink:hover {
        background-color: rgba(253, 95, 156, 0.1);
        cursor: pointer;
        transform: translateY(-3px);
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    }
    
    }
";

    $contenidoPHP1 = "
    <?php
    if (\$_SERVER['REQUEST_METHOD'] === 'POST') {
        \$id = \$_POST['id'];  // ID del título
        \$titulo = \$_POST['titulo'];  // Nuevo título

        \$filename = 'titulos.txt';  // Archivo donde guardar los títulos

        // Leer el archivo y obtener todo su contenido
        \$fileContent = file_get_contents(\$filename);

        // Buscar si el ID ya existe en el archivo
        \$pattern = \"/ID: \$id, Título: (.+)/\";
        if (preg_match(\$pattern, \$fileContent, \$matches)) {
            // Si el ID ya existe, reemplazar el título con el nuevo valor
            \$newData = preg_replace(\$pattern, \"ID: \$id, Título: \$titulo\", \$fileContent);
        } else {
            // Si el ID no existe, agregar la nueva entrada
            \$newData = \$fileContent . \"ID: \$id, Título: \$titulo\\n\";
        }

        // Guardar el archivo con los nuevos datos (reemplazando o agregando)
        if (file_put_contents(\$filename, \$newData) !== false) {
            echo 'Título guardado correctamente';
        } else {
            http_response_code(500);
            echo \"Error al guardar el título.\";
        }
    }
    ?>
    ";


    $contenidoPHP2 = "
        <?php
        if (isset(\$_GET['id'])) {
            \$id = \$_GET['id'];  // ID del título que queremos recuperar

            // Aquí deberías buscar el título en la base de datos o en un archivo
            // Ejemplo con un archivo simple
            \$filename = 'titulos.txt';  // Archivo donde guardamos los títulos
            \$fileContent = file_get_contents(\$filename);

            // Buscar el título por ID (esto es solo un ejemplo, deberías adaptarlo a tu base de datos)
            preg_match(\"/ID: \$id, Título: (.+)/\", \$fileContent, \$matches);

            if (isset(\$matches[1])) {
                echo \$matches[1];  // Enviar el título encontrado al cliente
            } else {
                echo 'Sin título';  // Si no se encuentra, se devuelve un valor por defecto
            }
        }
        ?>
    ";

    $contenidoTXT = "
    ";

    // Guardar archivos
    $archivos = [
        'HTML' => [$rutaArchivo, $contenidoHTML],
        'CSS' => [$rutaCSS, $contenidoCSS],
        'PHP1' => [$rutaPHP1, $contenidoPHP1],
        'PHP2' => [$rutaPHP2, $contenidoPHP2],
        'TXT' => [$rutaTXT, $contenidoTXT]
    ];

    foreach ($archivos as $nombre => $archivo) {
        if (file_put_contents($archivo[0], $archivo[1]) === false) {
            http_response_code(500);
            echo "Error al crear el archivo $nombre.";
            exit;
        }
    }

    // Subida de imagen
    if (isset($_FILES['imagenCurso']) && $_FILES['imagenCurso']['error'] === UPLOAD_ERR_OK) {
        // Validación de imagen
        $tmpName = $_FILES['imagenCurso']['tmp_name'];
        $extension = strtolower(pathinfo($_FILES['imagenCurso']['name'], PATHINFO_EXTENSION));
        $tiposPermitidos = ['jpg', 'jpeg', 'png', 'gif'];
        $maxTamano = 2 * 1024 * 1024; // 2 MB

        if (!in_array($extension, $tiposPermitidos)) {
            echo "Error: solo se permiten imágenes JPG, JPEG, PNG, GIF.";
            exit;
        }

        if ($_FILES['imagenCurso']['size'] > $maxTamano) {
            echo "Error: la imagen excede el tamaño permitido.";
            exit;
        }

        $nombreImagenFinal = $nombreLimpiado . '.' . $extension;
        $rutaImagen = $rutaDirectorio . '/' . $nombreImagenFinal;
        if (!move_uploaded_file($tmpName, $rutaImagen)) {
            echo "Error al mover la imagen.";
            exit;
        }
    }

    // Guardar información adicional
    $contenidoInfo = "Nombre corto: $nombreCorto\n";
    $contenidoInfo .= "Fecha de inicio: $fechaInicio\n";
    $contenidoInfo .= "Fecha de fin: $fechaFin\n";
    $contenidoInfo .= "Descripción: $descripcion\n";
    $contenidoInfo .= "Secciones: $secciones\n";

    $rutaInfo = $rutaDirectorio . '/' . $nombreLimpiado . '.txt';
    if (file_put_contents($rutaInfo, $contenidoInfo) === false) {
        http_response_code(500);
        echo "Error al guardar la información adicional.";
        exit;
    }

    echo $nombreArchivo; // Devuelve el nombre del archivo HTML generado
}
?>
