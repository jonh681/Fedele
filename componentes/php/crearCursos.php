<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include("conexion.php");
$conn = getConexion(); //

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['nombreCurso'] ?? 'curso_sin_nombre';

    // Sanitiza el nombre para que sea válido como archivo/carpeta
    $nombreLimpiado = preg_replace('/[^A-Za-z0-9_-]/', '_', $nombre);
    $nombreArchivo = $nombreLimpiado . '.php';

    $sqlCrearTabla = "
    CREATE TABLE IF NOT EXISTS `$nombreLimpiado` (
        `nombre_seccion` VARCHAR(255) NOT NULL,
        `nombre_leccion` VARCHAR(255) NOT NULL,
        `id_leccion` INT NOT NULL,
        PRIMARY KEY (`nombre_seccion`, `id_leccion`)
    );
    ";

    if ($conn->query($sqlCrearTabla) === TRUE) {
        // Tabla creada correctamente
    } else {
        die("Error al crear la tabla de lecciones: " . $conn->error);
    }


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
    $rutaPHP3 = $rutaDirectorio .'/guardar_recurso.php';
    $rutaPHP4 = $rutaDirectorio .'/listar_recursos.php';
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
        <div style=\"width: 100vw; background-color: #e9ecef; display: flex; text-align: center;\">
            <div style=\"padding: 0.5%;\">
                <a href=\"/fedele/componentes/admin/HomePageAdmin.html\"><img src=\"/fedele/imagenes/fedele.png\" width=\"120\" alt=\"\"></a>
            </div>
            <div style=\" width: 100%;\">
                <h1 class=\"text-center my-4\">Bienvenido al curso: $nombre</h1>
            </div>
        </div>
        <div class=\"container mt-3\">
    ";
    
    for ($i = 1; $i <= $secciones; $i++) {
        $contenidoHTML .= "
        <div class=\"contentPrincipal mt-3\">
            <div class=\"d-inline-flex gap-1\" style=\"width:50%;\">
                <div class=\"subtemas\" style=\"width: 100%;\">
                    <button id=\"toggleBtn$i\" class=\"btn btn-primary boton\" type=\"button\" data-bs-toggle=\"collapse\" data-bs-target=\"#collapseExample$i\" aria-expanded=\"false\" aria-controls=\"collapseExample$i\">
                        <i id=\"toggleIcon$i\" class=\"bi bi-chevron-right\"></i>
                    </button>
                    <div class=\"editable-title\" id=\"titleContainer$i\">
                        <span id=\"titleText$i\">Sección $i</span>
                        <i class=\"bi bi-pencil-fill edit-icon\" id=\"editIcon$i\"></i>
                        <i class=\"bi bi-floppy save-icon ms-2 d-none\" id=\"saveIcon<?= $i ?>\" style=\"cursor:pointer;\" title=\"Guardar cambios\"></i>
                    </div>
                </div>
            </div>
    
            <div class=\"collapse\" id=\"collapseExample$i\">
                <div class=\"card card-body\" style=\"border: none;\">
                    <div class=\"lista-recursos mt-3\" id=\"listaRecursos$i\"></div>
                    <div class=\"line-container\">
                        <div class=\"line\"></div>
                        <div class=\"button-wrapper\">
                            <button class=\"custom-button\" data-bs-toggle=\"modal\" data-bs-target=\"#modalPrincipal$i\">
                                <i class=\"bi bi-plus\"></i> Añadir una actividad o recurso
                            </button>
                        </div>
                        <div class=\"line\"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class=\"modal fade\" id=\"modalPrincipal$i\" tabindex=\"-1\" aria-labelledby=\"modalPrincipalLabel$i\" aria-hidden=\"true\">
            <div class=\"modal-dialog modal-xl modal-dialog-centered\">
                <div class=\"modal-content\">
                    <div class=\"modal-header\">
                        <h1 class=\"modal-title fs-5\" id=\"modalPrincipalLabel$i\">Añadir una actividad o recurso</h1>
                        <button type=\"button\" class=\"btn-close\" data-bs-dismiss=\"modal\" aria-label=\"Cerrar\"></button>
                    </div>
                    <div class=\"modal-body\">
                        <div class=\"col-12\">
                            <div class=\"list-group list-group-flush\">
                                <button class=\"list-group-item list-group-item-action\" data-bs-target=\"#modalRecursos$i\" data-bs-toggle=\"modal\" data-bs-dismiss=\"modal\">
                                Crear evaluaciones o recursos: <br>
                                En esta sección podrás subir Archivos, Áreas de texto y medios, carpetas, libros, URL, actividades, examenes.
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class=\"modal-footer\"></div>
                </div>
            </div>
        </div>
    
        <div class=\"modal fade\" id=\"modalRecursos$i\" tabindex=\"-1\" aria-labelledby=\"modalRecursosLabel$i\" aria-hidden=\"true\">
            <div class=\"modal-dialog modal-xl modal-dialog-centered\">
                <div class=\"modal-content\">
                    <div class=\"modal-header\">
                        <h1 class=\"modal-title fs-5\" id=\"modalRecursosLabel$i\">Crear Recurso o Actividad</h1>
                        <button type=\"button\" class=\"btn-close\" data-bs-dismiss=\"modal\" aria-label=\"Cerrar\"></button>
                    </div>
                    <div class=\"modal-body\">
                        <ul class=\"nav nav-tabs\" id=\"recursoActividadTabs$i\" role=\"tablist\">
                            <li class=\"nav-item\" role=\"presentation\">
                                <button class=\"nav-link active\" id=\"recursos-tab$i\" data-bs-toggle=\"tab\" data-bs-target=\"#recursos-pane$i\" type=\"button\" role=\"tab\" aria-controls=\"recursos-pane\" aria-selected=\"true\">Recursos</button>
                            </li>
                            <li class=\"nav-item\" role=\"presentation\">
                                <button class=\"nav-link\" id=\"actividades-tab$i\" data-bs-toggle=\"tab\" data-bs-target=\"#actividades-pane$i\" type=\"button\" role=\"tab\" aria-controls=\"actividades-pane\" aria-selected=\"false\">Actividades</button>
                            </li>
                        </ul>

                        <div class=\"tab-content mt-3\" id=\"recursoActividadTabsContent$i\">
                            <!-- TAB RECURSOS -->
                            <div class=\"tab-pane fade show active\" id=\"recursos-pane$i\" role=\"tabpane$i\" aria-labelledby=\"recursos-tab$i\" tabindex=\"0\">
                                <div class=\"container text-center\" style=\"color: rgb(3, 187, 133); font-size: large;\">
                                    <div class=\"row\">
                                        <div class=\"col recurso-item\" id=\"archivos$i\" onclick=\"showInputs('file', this, $i)\">
                                            <i class=\"bi bi-file-text\"></i><br><span>Documentos</span>
                                        </div>
                                        <div class=\"col recurso-item\" id=\"texto$i\" onclick=\"showInputs('text', this, $i)\">
                                            <i class=\"bi bi-body-text\"></i><br><span>Texto</span>
                                        </div>
                                        <div class=\"col recurso-item\" id=\"media$i\" onclick=\"showInputs('media', this, $i)\">
                                            <i class=\"bi bi-archive-fill\"></i><br><span>Media</span>
                                        </div>
                                        <div class=\"col recurso-item\" id=\"url$i\" onclick=\"showInputs('url', this, $i)\">
                                            <i class=\"bi bi-globe\"></i><br><span>URL</span>
                                        </div>
                                        <div class=\"col recurso-item\" id=\"url$i\" onclick=\"showInputs('libro', this, $i)\">
                                            <i class=\"bi bi-book\"></i><br><span>Libro</span>
                                        </div>
                                    </div>
                                </div>

                                <div class=\"inputsRecursos mt-3\" id=\"inputsContainer$i\"></div>

                            </div>
                            <!-- TAB ACTIVIDADES -->
                            <div class=\"tab-pane fade\" id=\"actividades-pane$i\" role=\"tabpanel\" aria-labelledby=\"actividades-tab$i\" tabindex=\"0\">
                                <p>Hola desde Actividades$i</p>
                            </div>
                        </div>
                    </div>
                    <div class=\"modal-footer\">
                    </div>
                </div>
            </div>
        </div>
        ";
    }
    
    $contenidoHTML .= "
        </div> <!-- end container -->
    
        <script src=\"https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js\"></script>
    
        <script>
            for (let i = 1; i <= <?php echo $secciones; ?>; i++) {
                const titleContainer = document.getElementById('titleContainer' + i);
                const titleText = document.getElementById('titleText' + i);
                const editIcon = document.getElementById('editIcon' + i);

                if (titleContainer && titleText && editIcon) {
                    // Crear y agregar el ícono de guardar
                    const saveIcon = document.createElement('i');
                    saveIcon.id = 'saveIcon' + i;
                    saveIcon.className = 'bi bi-save-fill ms-2 d-none';
                    saveIcon.style.cursor = 'pointer';
                    saveIcon.title = 'Guardar y recargar';
                    titleContainer.appendChild(saveIcon);

                    fetch('obtener_titulo.php?id=' + i)
                        .then(response => response.text())
                        .then(data => {
                            const updatedTitleText = document.getElementById('titleText' + i);
                            if (updatedTitleText) {
                                updatedTitleText.textContent = data;
                                updatedTitleText.setAttribute('data-seccion', data.trim());

                                // ✅ Aquí llamamos cargarRecursos solo cuando ya tenemos el nombre de la sección correcto
                                cargarRecursos(data.trim(), i);
                            } else {
                                console.warn('⚠ No se encontró titleText' + i + ' al actualizar');
                            }
                        })
                        .catch(error => console.error('Error al obtener el título actualizado:', error));

                    editIcon.addEventListener('click', () => {
                        if (titleContainer.contains(titleText)) {
                            const input = document.createElement('input');
                            input.type = 'text';
                            input.value = titleText.textContent;
                            input.className = 'edit-input';
                            input.id = 'titleInput' + i;

                            titleContainer.replaceChild(input, titleText);
                            input.focus();

                            editIcon.classList.add('d-none');
                            saveIcon.classList.remove('d-none');

                            // Guardar al salir del input o presionar Enter
                            const save = () => {
                                const newText = input.value.trim() || 'Sin título';
                                titleText.textContent = newText;

                                // Guardar en servidor
                                const xhr = new XMLHttpRequest();
                                xhr.open('POST', 'guardar_titulo.php', true);
                                xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                                xhr.send('id=' + i + '&titulo=' + encodeURIComponent(newText));

                                // Solicitar el título actualizado
                                fetch('obtener_titulo.php?id=' + i)
                                    .then(response => response.text())
                                    .then(data => {
                                        titleText.textContent = data;
                                        titleText.setAttribute('data-seccion', data.trim()); // ✅ Añadir también desde el inicio
                                    })
                                    .catch(error => console.error('Error al obtener el título:', error));
                            };

                            input.addEventListener('blur', save);
                            input.addEventListener('keydown', (e) => {
                                if (e.key === 'Enter') {
                                    save();
                                }
                            });

                            // Acciones del botón guardar: solo recargar
                            saveIcon.addEventListener('click', () => {
                                location.reload();
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

            function generarBotonGuardar(index) {
                return `
                    <div class=\"text-end mt-3\">
                        <button class=\"btn btn-success\" id=\"btnGuardar\${index}\" onclick=\"guardarRecurso(\${index})\">Guardar Recurso</button>                      
                    </div>
                `;
            }

            function guardarRecurso(index) {
                const titulo = document.getElementById('titleInput' + index)?.value || 'Sin título';
                const descripcion = document.getElementById('floatingTextarea' + index)?.value || '';
                const archivo = document.getElementById('fileInput' + index)?.files?.[0] || null;
                const url = document.getElementById('floatingInputGroup' + index)?.value || '';
                const seccionTitulo = document.getElementById('titleText' + index)?.textContent || 'Seccion_Desconocida';

                const formData = new FormData();
                formData.append('titulo', titulo);
                formData.append('descripcion', descripcion);
                formData.append('url', url);
                formData.append('seccion', seccionTitulo);

                console.log(\"➡ pathname completo:\", window.location.pathname);

                const partes = window.location.pathname.split('/');
                console.log(\"➡ Partes divididas:\", partes);

                const indexCurso = partes.findIndex(p => p.toLowerCase() === 'cursoscreados');
                console.log(\"➡ Índice de cursosCreados:\", indexCurso);

                const nombreCurso = partes[indexCurso + 1] || 'curso_no_definido';
                console.log(\"📦 Nombre del curso detectado:\", nombreCurso);
                formData.append('curso', nombreCurso);

                if (archivo) {
                    formData.append('archivo', archivo);
                }

                fetch('guardar_recurso.php', {
                    method: 'POST',
                    body: formData
                })
                .then(res => res.text())
                .then(data => {
                    const lista = document.getElementById('listaRecursos' + index);
                    if (lista) {
                        const nuevo = document.createElement('div');
                        nuevo.className = 'mb-2 p-2 border rounded bg-light';
                        nuevo.innerHTML = `
                            <strong>\${titulo}</strong><br>
                            \${archivo ? `<i class=\"bi bi-paperclip\"></i> Archivo: \${archivo.name}<br>` : ''}
                            \${url ? `<i class=\"bi bi-link\"></i> <a href=\"\${url}\" target=\"_blank\">\${url}</a><br>` : ''}
                        `;
                        lista.appendChild(nuevo);
                    }

                    // ✅ Crear mensaje bonito de éxito
                    const mensaje = document.createElement('div');
                    mensaje.className = 'alert alert-success mt-2';
                    mensaje.innerText = '✅ Recurso guardado exitosamente';
                    lista.appendChild(mensaje);

                    // ❗ Eliminar el mensaje automáticamente después de 3 segundos
                    setTimeout(() => {
                        mensaje.remove();
                    }, 3000);

                    // Limpiar inputs
                    const inputsContainer = document.getElementById('inputsContainer' + index);
                    if (inputsContainer) {
                        inputsContainer.innerHTML = '';
                    }

                    const offcanvas = bootstrap.Offcanvas.getInstance(document.getElementById('offcanvasTopRecursos' + index));
                    if (offcanvas) offcanvas.hide();
                })
                .catch(err => console.error('❌ Error al guardar:', err));
            }

            function showInputs(option, selectedElement, index) {
                let container = document.getElementById('inputsContainer' + index);
                if (container) {
                    container.innerHTML = '';
                }
                selectedElement.parentElement.querySelectorAll('.recurso-item').forEach(op => op.classList.remove('selected'));
                selectedElement.classList.add('selected');
    
                if (option === 'file') {
                    container.innerHTML = `
                        <div class=\"form-floating mb-3 mt-3\">
                            <input type=\"text\" class=\"form-control\" id=\"titleInput\${index}\" placeholder=\"Título\">
                            <label for=\"titleInput\${index}\">Título de la actividad</label>
                        </div>
                        <div class=\"mt-3\">
                            <label for=\"fileInput\${index}\" class=\"form-label\">Subir documento</label>
                            <input type=\"file\" class=\"form-control\" id=\"fileInput\${index}\">
                        </div>
                        <div class=\"form-floating mt-3\">
                            <textarea class=\"form-control\" id=\"floatingTextarea\${index}\" style=\"height: 100px\"></textarea>
                            <label for=\"floatingTextarea\${index}\">Escribe una descripción</label>
                        </div>
                    `+ generarBotonGuardar(index);
                } else if (option === 'text') {
                    container.innerHTML = `
                        <div class=\"form-floating mb-3 mt-3\">
                            <input type=\"text\" class=\"form-control\" id=\"titleInput\${index}\" placeholder=\"Título\">
                            <label for=\"titleInput\${index}\">Título de la actividad</label>
                        </div>
                        <div class=\"form-floating mt-3\">
                            <textarea class=\"form-control\" id=\"floatingTextarea\${index}\" style=\"height: 100px\"></textarea>
                            <label for=\"floatingTextarea\${index}\">Descripción</label>
                        </div>
                    ` + generarBotonGuardar(index);
                } else if (option === 'media') {
                    container.innerHTML = `
                        <div class=\"form-floating mb-3 mt-3\">
                            <input type=\"text\" class=\"form-control\" id=\"titleInput\${index}\" placeholder=\"Título\">
                            <label for=\"titleInput\${index}\">Título de la actividad</label>
                        </div>
                        <div class=\"mt-3\">
                            <label for=\"fileInput\${index}\" class=\"form-label\">Subir multimedia</label>
                            <input type=\"file\" class=\"form-control\" id=\"fileInput\${index}\">
                        </div>
                        <div class=\"form-floating mt-3\">
                            <textarea class=\"form-control\" id=\"floatingTextarea\${index}\" style=\"height: 100px\"></textarea>
                            <label for=\"floatingTextarea\${index}\">Descripción</label>
                        </div>
                    `+ generarBotonGuardar(index);
                } else if (option === 'url') {
                    container.innerHTML = `
                        <div class=\"form-floating mb-3 mt-3\">
                            <input type=\"text\" class=\"form-control\" id=\"titleInput\${index}\" placeholder=\"Título\">
                            <label for=\"titleInput\${index}\">Título de la actividad</label>
                        </div>
                        <div class=\"input-group mb-3\">
                            <span class=\"input-group-text\"><i class=\"bi bi-link-45deg\"></i></span>
                            <div class=\"form-floating\">
                                <input type=\"text\" class=\"form-control\" id=\"floatingInputGroup\${index}\" placeholder=\"URL\">
                                <label for=\"floatingInputGroup\${index}\">Ingresa una URL</label>
                            </div>
                        </div>
                        <div class=\"form-floating mt-3\">
                            <textarea class=\"form-control\" id=\"floatingTextarea\${index}\" style=\"height: 100px\"></textarea>
                            <label for=\"floatingTextarea\${index}\">Descripción</label>
                        </div>
                    `+ generarBotonGuardar(index);
                } else if (option === 'libro') {
                    container.innerHTML = `
                        <div class=\"form-floating mb-3 mt-3\">
                            <input type=\"text\" class=\"form-control\" id=\"titleInput\${index}\" placeholder=\"Título del libro\">
                            <label for=\"titleInput\${index}\">Título del libro</label>
                        </div>

                        <div class=\"form-floating mb-3\">
                            <input type=\"number\" class=\"form-control\" id=\"numeroPaginas\${index}\" placeholder=\"Número de páginas\" min=\"1\" value=\"1\">
                            <label for=\"numeroPaginas\${index}\">Número de páginas</label>
                        </div>

                        <div class=\"text-end mb-3\">
                            <button class=\"btn btn-primary\" onclick=\"generarPaginasLibro(\${index})\">Generar contenido por página</button>
                        </div>

                        <div id=\"contenedorPaginas\${index}\"></div>
                    ` + generarBotonGuardar(index);
                }
            }

            function generarPaginasLibro(index) {
                const num = parseInt(document.getElementById('numeroPaginas' + index).value);
                const contenedor = document.getElementById('contenedorPaginas' + index);
                contenedor.innerHTML = '';

                for (let i = 1; i <= num; i++) {
                    contenedor.innerHTML += `
                        <div class=\"card mb-3\">
                            <div class=\"card-header\">Página \${i}</div>
                            <div class=\"card-body\">
                                <div class=\"form-floating mb-3\">
                                    <textarea class=\"form-control\" id=\"textoPagina\${index}_\${i}\" style=\"height: 100px\"></textarea>
                                    <label for=\"textoPagina\${index}_\${i}\">Texto</label>
                                </div>

                                <div class=\"mb-3\">
                                    <label for=\"imagenPagina\${index}_\${i}\" class=\"form-label\">Imagen</label>
                                    <input type=\"file\" class=\"form-control\" id=\"imagenPagina\${index}_\${i}\">
                                </div>

                                <div class=\"input-group mb-3\">
                                    <span class=\"input-group-text\">URL</span>
                                    <input type=\"url\" class=\"form-control\" id=\"urlPagina\${index}_\${i}\" placeholder=\"http://...\">
                                </div>

                                <div class=\"input-group\">
                                    <span class=\"input-group-text\">YouTube</span>
                                    <input type=\"url\" class=\"form-control\" id=\"youtubePagina\${index}_\${i}\" placeholder=\"https://youtu.be/...\">
                                </div>
                            </div>
                        </div>
                    `;
                }
            }

           function cargarRecursos(nombreSeccion, numSeccion) {

                console.log('cargarRecursos llamado con:', { nombreSeccion, numSeccion });
                fetch('listar_recursos.php')
                    .then(res => res.json())
                    .then(data => {
                        console.log('📦 Data que llega del servidor:', data);
                        const recursosArray = Array.isArray(data) ? data : Object.values(data);

                        const recursosFiltrados = recursosArray.filter(r => 
                            r.seccion.trim().toLowerCase() === nombreSeccion.trim().toLowerCase()
                        );

                        recursosFiltrados.sort((a, b) => a.fecha - b.fecha);

                        console.log('🎯 Recursos filtrados para la sección', numSeccion, ':', recursosFiltrados);

                        const lista = document.getElementById('listaRecursos' + numSeccion);
                        if (!lista) return;

                        lista.innerHTML = '';
                        if (recursosFiltrados.length === 0) {
                            const mensaje = document.createElement('div');
                            mensaje.className = 'alert alert-info';
                            mensaje.innerText = 'No hay recursos aún.';
                            lista.appendChild(mensaje);
                        } else {
                            recursosFiltrados.forEach(r => {
                                const item = document.createElement('div');
                                item.className = 'mb-2 p-2 border rounded bg-light';
                                item.innerHTML = `
                                    <strong>\${r.titulo}</strong><br>
                                    <small>\${r.descripcion}</small><br>
                                    \${r.archivo ? `<i class=\"bi bi-paperclip\"></i> <a href=\"\${r.archivo}\" target=\"_blank\">\${r.archivo.split('/').pop()}</a><br>` : ''}
                                    \${r.url ? `<i class=\"bi bi-link\"></i> <a href=\"\${r.url}\" target=\"_blank\">\${r.url}</a><br>` : ''}
                                `;
                                lista.appendChild(item);
                            });
                        }
                    })
                    .catch(err => console.error('❌ Error al cargar recursos:', err));
            }
        </script>
    
    </body>
    </html>
    ";


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

    .recurso-item {
        cursor: pointer;
    }
    
    .recurso-item.selected {
        background-color: #17a2b8; /* Cambiar el color de fondo para indicar selección */
        color: white;
        font-weight: bold;
    }

    [id^=\"offcanvasTopRecursos\"] {
        --bs-offcanvas-height: 50vh;
    }


    .inputsRecursos{
        margin: 0 auto;
        width: 35%;
    }

    .lista-recursos div {
        font-size: 0.95rem;
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

    $contenidoPHP3 = "

    <?php
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    include(__DIR__ . \"/../../conexion.php\");
    \$conn = getConexion();

    // Comprobar conexión
    if (!\$conn || \$conn->connect_error) {
        die(\"❌ Error de conexión a la base de datos: \" . \$conn->connect_error);
    }

    if (\$_SERVER['REQUEST_METHOD'] === 'POST') {
        echo \"📥 Datos recibidos:
    \";
        print_r(\$_POST);

        \$curso       = trim(\$_POST['curso'] ?? '');
        \$titulo      = trim(\$_POST['titulo'] ?? 'sin_titulo');
        \$descripcion = trim(\$_POST['descripcion'] ?? '');
        \$url         = trim(\$_POST['url'] ?? '');
        \$seccion     = trim(\$_POST['seccion'] ?? 'sin_seccion');

        if (empty(\$curso)) {
            die(\"❌ Curso no especificado.
    \");
        }

        \$nombreTabla    = preg_replace('/[^A-Za-z0-9_]/', '_', \$curso);
        \$nombreSeccion  = preg_replace('/[^A-Za-z0-9_ -]/', ' ', \$seccion);

        echo \"
        🛠 Procesando inserción en tabla: \$nombreTabla
        \";

        // Verificar existencia de tabla
        \$verificar = \$conn->query(\"SHOW TABLES LIKE '\$nombreTabla'\");
        if (!\$verificar || \$verificar->num_rows === 0) {
            die(\"❌ La tabla '\$nombreTabla' no existe.
    \");
        }

        // Obtener siguiente id_leccion
        \$stmt = \$conn->prepare(\"SELECT MAX(id_leccion) as max_id FROM `\$nombreTabla`\");
        \$stmt->execute();
        \$resultado = \$stmt->get_result()->fetch_assoc();
        \$nuevoId = (\$resultado['max_id'] ?? 0) + 1;

        echo \"📌 ID siguiente para '\$nombreSeccion': \$nuevoId
    \";

        // Insertar
        \$stmtInsert = \$conn->prepare(\"INSERT INTO `\$nombreTabla` (nombre_seccion, nombre_leccion, id_leccion) VALUES (?, ?, ?)\");
        \$stmtInsert->bind_param(\"ssi\", \$nombreSeccion, \$titulo, \$nuevoId);

        if (\$stmtInsert->execute()) {
            echo \"✅ Lección '\$titulo' insertado en sección '\$nombreSeccion' con ID \$nuevoId
    \";
        } else {
            echo \"❌ Error al insertar: \" . \$stmtInsert->error . \"
    \";
        }

        \$rutaSeccion = __DIR__ . '/recursos/' . \$nombreSeccion;
        \$rutaRecurso = \$rutaSeccion . '/' . \$titulo;

        // Crear las carpetas si no existen
        if (!file_exists(\$rutaSeccion)) {
            mkdir(\$rutaSeccion, 0777, true);
        }
        if (!file_exists(\$rutaRecurso)) {
            mkdir(\$rutaRecurso, 0777, true);
        }
        // Guardar archivo info.txt
        \$info = \"Título: \$titulo
        Descripción: \$descripcion
        \";
        if (!empty(\$url)) {
            \$info .= \"URL: \$url\";
        }
        file_put_contents(\"\$rutaRecurso/info.txt\", \$info);

        // Guardar archivo subido
        if (isset(\$_FILES['archivo']) && \$_FILES['archivo']['error'] === UPLOAD_ERR_OK) {
            \$archivoDestino = \"\$rutaRecurso/\" . basename(\$_FILES['archivo']['name']);
            move_uploaded_file(\$_FILES['archivo']['tmp_name'], \$archivoDestino);
        }

        echo \"✅ Recurso guardado exitosamente en: \$rutaRecurso\";

    } else {
        echo \"⚠️ Esta ruta solo acepta POST.
    \";
    }
    ?>
    ";

    $contenidoPHP4 = "
    <?php
    header('Content-Type: application/json');
    ini_set('display_errors', 1);
    error_reporting(E_ALL);

    \$directorioBase = 'recursos/';
    \$recursos = [];

    if (is_dir(\$directorioBase)) {
        \$secciones = array_diff(scandir(\$directorioBase), ['.', '..']);
        natcasesort(\$secciones); // Ordena alfabéticamente de forma natural
        \$secciones = array_values(\$secciones);
        \$seccionIndex = 1;

        foreach (\$secciones as \$seccion) {
            \$rutaSeccion = \$directorioBase . \$seccion . '/';

            if (is_dir(\$rutaSeccion)) {
                \$recursosSeccion = array_diff(scandir(\$rutaSeccion), ['.', '..']);

                foreach (\$recursosSeccion as \$recursoDir) {
                    \$rutaRecurso = \$rutaSeccion . \$recursoDir . '/';

                    if (is_dir(\$rutaRecurso)) {
                        \$infoFile = \$rutaRecurso . 'info.txt';
                        \$titulo = \$recursoDir;
                        \$descripcion = '';
                        \$url = '';
                        \$archivo = '';
                        \$fecha = filemtime(\$rutaRecurso);

                        if (file_exists(\$infoFile)) {
                            \$contenido = file_get_contents(\$infoFile);
                            preg_match('/Título: (.*)/', \$contenido, \$matchTitulo);
                            preg_match('/Descripción: (.*)/', \$contenido, \$matchDesc);
                            preg_match('/URL: (.*)/', \$contenido, \$matchURL);

                            \$titulo = \$matchTitulo[1] ?? \$recursoDir;
                            \$descripcion = \$matchDesc[1] ?? '';
                            \$url = \$matchURL[1] ?? '';
                        }

                        \$archivos = array_diff(scandir(\$rutaRecurso), ['.', '..', 'info.txt']);
                        if (!empty(\$archivos)) {
                            \$archivoNombre = reset(\$archivos);
                            \$archivo = 'recursos/' . \$seccion . '/' . \$recursoDir . '/' . \$archivoNombre;
                        }

                        // SOLO AQUÍ SE AGREGA EL RECURSO
                        \$recursos[] = [
                            'titulo' => trim(\$titulo),
                            'descripcion' => trim(\$descripcion),
                            'url' => \$url,
                            'archivo' => \$archivo,
                            'seccion' => \$seccion,
                            'carpeta' => \$recursoDir,
                            'fecha' => \$fecha
                        ];
                    }
                }
            }

            \$seccionIndex++;
        }

        // Ordenar por fecha DESC
        usort(\$recursos, function (\$a, \$b) {
            return \$b['fecha'] - \$a['fecha'];
        });
    }

    echo json_encode(\$recursos);
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
        'PHP3' => [$rutaPHP3, $contenidoPHP3],
        'PHP4'=> [$rutaPHP4, $contenidoPHP4],
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
