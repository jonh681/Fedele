<!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <title>Retroalimentación de Kangaroo</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet" />
        <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />
        <link rel="stylesheet" href="diseñoContenido.css">
    </head>
    <body>
        <div style="width: 100vw; background-color: #e9ecef; display: flex; text-align: center;">
            <div style="padding: 0.5%;">
                <a href="/fedele/componentes/admin/HomePageAdmin.html"><img src="/fedele/imagenes/fedele.png" width="120" alt=""></a>
            </div>
            <div style=" width: 100%;">
                <h1 class="text-center my-4">Bienvenido al curso: Retroalimentación de Kangaroo</h1>
            </div>
        </div>
        <div class="container mt-3">
    
        <div class="contentPrincipal mt-3">
            <div class="d-inline-flex gap-1" style="width:50%;">
                <div class="subtemas" style="width: 100%;">
                    <button id="toggleBtn1" class="btn btn-primary boton" type="button" data-bs-toggle="collapse" data-bs-target="#collapseExample1" aria-expanded="false" aria-controls="collapseExample1">
                        <i id="toggleIcon1" class="bi bi-chevron-right"></i>
                    </button>
                    <div class="editable-title" id="titleContainer1">
                        <span id="titleText1">Sección 1</span>
                        <i class="bi bi-pencil-fill edit-icon" id="editIcon1"></i>
                        <i class="bi bi-floppy save-icon ms-2 d-none" id="saveIcon<?= 1 ?>" style="cursor:pointer;" title="Guardar cambios"></i>
                    </div>
                </div>
            </div>
    
            <div class="collapse" id="collapseExample1">
                <div class="card card-body" style="border: none;">
                    <div class="lista-recursos mt-3" id="listaRecursos1"></div>
                    <div class="line-container">
                        <div class="line"></div>
                        <div class="button-wrapper">
                            <button class="custom-button" data-bs-toggle="modal" data-bs-target="#modalPrincipal1">
                                <i class="bi bi-plus"></i> Añadir una actividad o recurso
                            </button>
                        </div>
                        <div class="line"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="modalPrincipal1" tabindex="-1" aria-labelledby="modalPrincipalLabel1" aria-hidden="true">
            <div class="modal-dialog modal-xl modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="modalPrincipalLabel1">Añadir una actividad o recurso</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                    </div>
                    <div class="modal-body">
                        <div class="col-12">
                            <div class="list-group list-group-flush">
                                <button class="list-group-item list-group-item-action" data-bs-target="#modalRecursos1" data-bs-toggle="modal" data-bs-dismiss="modal">
                                Crear evaluaciones o recursos: <br>
                                En esta sección podrás subir Archivos, Áreas de texto y medios, carpetas, libros, URL, actividades, examenes.
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer"></div>
                </div>
            </div>
        </div>
    
        <div class="modal fade" id="modalRecursos1" tabindex="-1" aria-labelledby="modalRecursosLabel1" aria-hidden="true">
            <div class="modal-dialog modal-xl modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="modalRecursosLabel1">Crear Recurso o Actividad</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                    </div>
                    <div class="modal-body">
                        <ul class="nav nav-tabs" id="recursoActividadTabs1" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="recursos-tab1" data-bs-toggle="tab" data-bs-target="#recursos-pane1" type="button" role="tab" aria-controls="recursos-pane" aria-selected="true">Recursos</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="actividades-tab1" data-bs-toggle="tab" data-bs-target="#actividades-pane1" type="button" role="tab" aria-controls="actividades-pane" aria-selected="false">Actividades</button>
                            </li>
                        </ul>

                        <div class="tab-content mt-3" id="recursoActividadTabsContent1">
                            <!-- TAB RECURSOS -->
                            <div class="tab-pane fade show active" id="recursos-pane1" role="tabpane1" aria-labelledby="recursos-tab1" tabindex="0">
                                <div class="container text-center" style="color: rgb(3, 187, 133); font-size: large;">
                                    <div class="row">
                                        <div class="col recurso-item" id="archivos1" onclick="showInputs('file', this, 1)">
                                            <i class="bi bi-file-text"></i><br><span>Documentos</span>
                                        </div>
                                        <div class="col recurso-item" id="texto1" onclick="showInputs('text', this, 1)">
                                            <i class="bi bi-body-text"></i><br><span>Texto</span>
                                        </div>
                                        <div class="col recurso-item" id="media1" onclick="showInputs('media', this, 1)">
                                            <i class="bi bi-archive-fill"></i><br><span>Media</span>
                                        </div>
                                        <div class="col recurso-item" id="url1" onclick="showInputs('url', this, 1)">
                                            <i class="bi bi-globe"></i><br><span>URL</span>
                                        </div>
                                        <div class="col recurso-item" id="libro1" onclick="showInputs('libro', this, 1)">
                                            <i class="bi bi-book"></i><br><span>Libro</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="inputsRecursos mt-3" id="inputsContainer1"></div>

                            </div>
                            <!-- TAB ACTIVIDADES -->
                            <div class="tab-pane fade" id="actividades-pane1" role="tabpanel" aria-labelledby="actividades-tab1" tabindex="0">
                                <p>Hola desde Actividades1</p>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                    </div>
                </div>
            </div>
        </div>
        
        <div class="contentPrincipal mt-3">
            <div class="d-inline-flex gap-1" style="width:50%;">
                <div class="subtemas" style="width: 100%;">
                    <button id="toggleBtn2" class="btn btn-primary boton" type="button" data-bs-toggle="collapse" data-bs-target="#collapseExample2" aria-expanded="false" aria-controls="collapseExample2">
                        <i id="toggleIcon2" class="bi bi-chevron-right"></i>
                    </button>
                    <div class="editable-title" id="titleContainer2">
                        <span id="titleText2">Sección 2</span>
                        <i class="bi bi-pencil-fill edit-icon" id="editIcon2"></i>
                        <i class="bi bi-floppy save-icon ms-2 d-none" id="saveIcon<?= 2 ?>" style="cursor:pointer;" title="Guardar cambios"></i>
                    </div>
                </div>
            </div>
    
            <div class="collapse" id="collapseExample2">
                <div class="card card-body" style="border: none;">
                    <div class="lista-recursos mt-3" id="listaRecursos2"></div>
                    <div class="line-container">
                        <div class="line"></div>
                        <div class="button-wrapper">
                            <button class="custom-button" data-bs-toggle="modal" data-bs-target="#modalPrincipal2">
                                <i class="bi bi-plus"></i> Añadir una actividad o recurso
                            </button>
                        </div>
                        <div class="line"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="modalPrincipal2" tabindex="-1" aria-labelledby="modalPrincipalLabel2" aria-hidden="true">
            <div class="modal-dialog modal-xl modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="modalPrincipalLabel2">Añadir una actividad o recurso</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                    </div>
                    <div class="modal-body">
                        <div class="col-12">
                            <div class="list-group list-group-flush">
                                <button class="list-group-item list-group-item-action" data-bs-target="#modalRecursos2" data-bs-toggle="modal" data-bs-dismiss="modal">
                                Crear evaluaciones o recursos: <br>
                                En esta sección podrás subir Archivos, Áreas de texto y medios, carpetas, libros, URL, actividades, examenes.
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer"></div>
                </div>
            </div>
        </div>
    
        <div class="modal fade" id="modalRecursos2" tabindex="-1" aria-labelledby="modalRecursosLabel2" aria-hidden="true">
            <div class="modal-dialog modal-xl modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="modalRecursosLabel2">Crear Recurso o Actividad</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                    </div>
                    <div class="modal-body">
                        <ul class="nav nav-tabs" id="recursoActividadTabs2" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="recursos-tab2" data-bs-toggle="tab" data-bs-target="#recursos-pane2" type="button" role="tab" aria-controls="recursos-pane" aria-selected="true">Recursos</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="actividades-tab2" data-bs-toggle="tab" data-bs-target="#actividades-pane2" type="button" role="tab" aria-controls="actividades-pane" aria-selected="false">Actividades</button>
                            </li>
                        </ul>

                        <div class="tab-content mt-3" id="recursoActividadTabsContent2">
                            <!-- TAB RECURSOS -->
                            <div class="tab-pane fade show active" id="recursos-pane2" role="tabpane2" aria-labelledby="recursos-tab2" tabindex="0">
                                <div class="container text-center" style="color: rgb(3, 187, 133); font-size: large;">
                                    <div class="row">
                                        <div class="col recurso-item" id="archivos2" onclick="showInputs('file', this, 2)">
                                            <i class="bi bi-file-text"></i><br><span>Documentos</span>
                                        </div>
                                        <div class="col recurso-item" id="texto2" onclick="showInputs('text', this, 2)">
                                            <i class="bi bi-body-text"></i><br><span>Texto</span>
                                        </div>
                                        <div class="col recurso-item" id="media2" onclick="showInputs('media', this, 2)">
                                            <i class="bi bi-archive-fill"></i><br><span>Media</span>
                                        </div>
                                        <div class="col recurso-item" id="url2" onclick="showInputs('url', this, 2)">
                                            <i class="bi bi-globe"></i><br><span>URL</span>
                                        </div>
                                        <div class="col recurso-item" id="libro2" onclick="showInputs('libro', this, 2)">
                                            <i class="bi bi-book"></i><br><span>Libro</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="inputsRecursos mt-3" id="inputsContainer2"></div>

                            </div>
                            <!-- TAB ACTIVIDADES -->
                            <div class="tab-pane fade" id="actividades-pane2" role="tabpanel" aria-labelledby="actividades-tab2" tabindex="0">
                                <p>Hola desde Actividades2</p>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                    </div>
                </div>
            </div>
        </div>
        
        <div class="contentPrincipal mt-3">
            <div class="d-inline-flex gap-1" style="width:50%;">
                <div class="subtemas" style="width: 100%;">
                    <button id="toggleBtn3" class="btn btn-primary boton" type="button" data-bs-toggle="collapse" data-bs-target="#collapseExample3" aria-expanded="false" aria-controls="collapseExample3">
                        <i id="toggleIcon3" class="bi bi-chevron-right"></i>
                    </button>
                    <div class="editable-title" id="titleContainer3">
                        <span id="titleText3">Sección 3</span>
                        <i class="bi bi-pencil-fill edit-icon" id="editIcon3"></i>
                        <i class="bi bi-floppy save-icon ms-2 d-none" id="saveIcon<?= 3 ?>" style="cursor:pointer;" title="Guardar cambios"></i>
                    </div>
                </div>
            </div>
    
            <div class="collapse" id="collapseExample3">
                <div class="card card-body" style="border: none;">
                    <div class="lista-recursos mt-3" id="listaRecursos3"></div>
                    <div class="line-container">
                        <div class="line"></div>
                        <div class="button-wrapper">
                            <button class="custom-button" data-bs-toggle="modal" data-bs-target="#modalPrincipal3">
                                <i class="bi bi-plus"></i> Añadir una actividad o recurso
                            </button>
                        </div>
                        <div class="line"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="modalPrincipal3" tabindex="-1" aria-labelledby="modalPrincipalLabel3" aria-hidden="true">
            <div class="modal-dialog modal-xl modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="modalPrincipalLabel3">Añadir una actividad o recurso</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                    </div>
                    <div class="modal-body">
                        <div class="col-12">
                            <div class="list-group list-group-flush">
                                <button class="list-group-item list-group-item-action" data-bs-target="#modalRecursos3" data-bs-toggle="modal" data-bs-dismiss="modal">
                                Crear evaluaciones o recursos: <br>
                                En esta sección podrás subir Archivos, Áreas de texto y medios, carpetas, libros, URL, actividades, examenes.
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer"></div>
                </div>
            </div>
        </div>
    
        <div class="modal fade" id="modalRecursos3" tabindex="-1" aria-labelledby="modalRecursosLabel3" aria-hidden="true">
            <div class="modal-dialog modal-xl modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="modalRecursosLabel3">Crear Recurso o Actividad</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                    </div>
                    <div class="modal-body">
                        <ul class="nav nav-tabs" id="recursoActividadTabs3" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="recursos-tab3" data-bs-toggle="tab" data-bs-target="#recursos-pane3" type="button" role="tab" aria-controls="recursos-pane" aria-selected="true">Recursos</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="actividades-tab3" data-bs-toggle="tab" data-bs-target="#actividades-pane3" type="button" role="tab" aria-controls="actividades-pane" aria-selected="false">Actividades</button>
                            </li>
                        </ul>

                        <div class="tab-content mt-3" id="recursoActividadTabsContent3">
                            <!-- TAB RECURSOS -->
                            <div class="tab-pane fade show active" id="recursos-pane3" role="tabpane3" aria-labelledby="recursos-tab3" tabindex="0">
                                <div class="container text-center" style="color: rgb(3, 187, 133); font-size: large;">
                                    <div class="row">
                                        <div class="col recurso-item" id="archivos3" onclick="showInputs('file', this, 3)">
                                            <i class="bi bi-file-text"></i><br><span>Documentos</span>
                                        </div>
                                        <div class="col recurso-item" id="texto3" onclick="showInputs('text', this, 3)">
                                            <i class="bi bi-body-text"></i><br><span>Texto</span>
                                        </div>
                                        <div class="col recurso-item" id="media3" onclick="showInputs('media', this, 3)">
                                            <i class="bi bi-archive-fill"></i><br><span>Media</span>
                                        </div>
                                        <div class="col recurso-item" id="url3" onclick="showInputs('url', this, 3)">
                                            <i class="bi bi-globe"></i><br><span>URL</span>
                                        </div>
                                        <div class="col recurso-item" id="libro3" onclick="showInputs('libro', this, 3)">
                                            <i class="bi bi-book"></i><br><span>Libro</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="inputsRecursos mt-3" id="inputsContainer3"></div>

                            </div>
                            <!-- TAB ACTIVIDADES -->
                            <div class="tab-pane fade" id="actividades-pane3" role="tabpanel" aria-labelledby="actividades-tab3" tabindex="0">
                                <p>Hola desde Actividades3</p>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                    </div>
                </div>
            </div>
        </div>
        
        </div> <!-- end container -->
    
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"></script>
    
        <script>
            for (let i = 1; i <= <?php echo 3; ?>; i++) {
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
            for (let i = 1; i <= <?php echo 3; ?>; i++) {
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
                    <div class="text-end mt-3">
                        <button class="btn btn-success" id="btnGuardar${index}" onclick="guardarRecurso(${index})">Guardar Recurso</button>                      
                    </div>
                `;
            }

            function guardarRecurso(index) {
                const titulo = document.getElementById('titleInput' + index)?.value || '';
                let tituloLibro = document.getElementById('titleInputLibro' + index)?.value || '';
                const descripcion = document.getElementById('floatingTextarea' + index)?.value || '';
                const archivo = document.getElementById('fileInput' + index)?.files?.[0] || null;
                const url = document.getElementById('floatingInputGroup' + index)?.value || '';
                const seccionTitulo = document.getElementById('titleText' + index)?.textContent || 'Seccion_Desconocida';

                const tituloRecurso = tituloLibro !== '' ? tituloLibro : (titulo !== '' ? titulo : 'Sin título');
                const esLibro = tituloRecurso.toLowerCase().includes('libro'); 

                const numeroPaginasElement = document.getElementById('numeroPaginas' + index);
                const numPaginas = numeroPaginasElement ? parseInt(numeroPaginasElement.value) : 1;  // Valor por defecto 1 si no existe

                const paginas = [];

                // Recolectar información de cada página
                for (let i = 1; i <= numPaginas; i++) {
                    const texto = document.getElementById('textoPagina' + index + '_' + i)?.value || '';
                    const imagen = document.getElementById('imagenPagina' + index + '_' + i)?.files?.[0] || null;
                    const urlPagina = document.getElementById('urlPagina' + index + '_' + i)?.value || '';

                    // Crear objeto para cada página
                    const paginaInfo = {
                        texto,
                        imagen: imagen ? imagen.name : '',  // Solo enviar el nombre del archivo, no el archivo completo
                        url: urlPagina,
                    };

                    paginas.push(paginaInfo);
                }


                // Crear FormData para enviar todos los datos
                const formData = new FormData();
                
                formData.append('tituloRecurso', tituloRecurso);
                formData.append('descripcion', descripcion);
                formData.append('url', url);
                formData.append('seccion', seccionTitulo);
                formData.append('paginas', JSON.stringify(paginas));  // Convertir las páginas a una cadena JSON para enviarlas
                formData.append('esLibro', esLibro);  // Añadir esta información para saber si es un libro
                formData.append('numPaginas', numPaginas);  // Enviar el número de páginas

                console.log("➡ pathname completo:", window.location.pathname);

                const partes = window.location.pathname.split('/');
                console.log("➡ Partes divididas:", partes);

                const indexCurso = partes.findIndex(p => p.toLowerCase() === 'cursoscreados');
                console.log("➡ Índice de cursosCreados:", indexCurso);

                const nombreCurso = partes[indexCurso + 1] || 'curso_no_definido';
                console.log("📦 Nombre del curso detectado:", nombreCurso);
                formData.append('curso', nombreCurso);

                if (archivo) {
                    formData.append('archivo', archivo);
                }

                // Agregar las imágenes de las páginas (si existen)
                paginas.forEach((pagina, i) => {
                    if (pagina.imagen) {
                        formData.append('imagen' + i, document.getElementById('imagenPagina' + index + '_' + (i+1)).files[0]);
                    }
                });

                // Realizar la solicitud fetch para guardar los datos
                fetch('guardar_recurso.php', {
                    method: 'POST',
                    body: formData
                })
                .then(res => res.text())
                .then(data => {
                    console.log("✅ Respuesta del servidor:", data);

                    const lista = document.getElementById('listaRecursos' + index);
                    if (lista) {
                        const nuevo = document.createElement('div');
                        nuevo.className = 'mb-2 p-2 border rounded bg-light';
                        nuevo.innerHTML = `
                            <strong>${titulo}</strong><br>
                            ${archivo ? `<i class="bi bi-paperclip"></i> Archivo: ${archivo.name}<br>` : ''}
                            ${url ? `<i class="bi bi-link"></i> <a href="${url}" target="_blank">${url}</a><br>` : ''}
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
                        <div class="form-floating mb-3 mt-3">
                            <input type="text" class="form-control" id="titleInput${index}" placeholder="Título">
                            <label for="titleInput${index}">Título de la actividad</label>
                        </div>
                        <div class="mt-3">
                            <label for="fileInput${index}" class="form-label">Subir documento</label>
                            <input type="file" class="form-control" id="fileInput${index}">
                        </div>
                        <div class="form-floating mt-3">
                            <textarea class="form-control" id="floatingTextarea${index}" style="height: 100px"></textarea>
                            <label for="floatingTextarea${index}">Escribe una descripción</label>
                        </div>
                    `+ generarBotonGuardar(index);
                } else if (option === 'text') {
                    container.innerHTML = `
                        <div class="form-floating mb-3 mt-3">
                            <input type="text" class="form-control" id="titleInput${index}" placeholder="Título">
                            <label for="titleInput${index}">Título de la actividad</label>
                        </div>
                        <div class="form-floating mt-3">
                            <textarea class="form-control" id="floatingTextarea${index}" style="height: 100px"></textarea>
                            <label for="floatingTextarea${index}">Descripción</label>
                        </div>
                    ` + generarBotonGuardar(index);
                } else if (option === 'media') {
                    container.innerHTML = `
                        <div class="form-floating mb-3 mt-3">
                            <input type="text" class="form-control" id="titleInput${index}" placeholder="Título">
                            <label for="titleInput${index}">Título de la actividad</label>
                        </div>
                        <div class="mt-3">
                            <label for="fileInput${index}" class="form-label">Subir multimedia</label>
                            <input type="file" class="form-control" id="fileInput${index}">
                        </div>
                        <div class="form-floating mt-3">
                            <textarea class="form-control" id="floatingTextarea${index}" style="height: 100px"></textarea>
                            <label for="floatingTextarea${index}">Descripción</label>
                        </div>
                    `+ generarBotonGuardar(index);
                } else if (option === 'url') {
                    container.innerHTML = `
                        <div class="form-floating mb-3 mt-3">
                            <input type="text" class="form-control" id="titleInput${index}" placeholder="Título">
                            <label for="titleInput${index}">Título de la actividad</label>
                        </div>
                        <div class="input-group mb-3">
                            <span class="input-group-text"><i class="bi bi-link-45deg"></i></span>
                            <div class="form-floating">
                                <input type="text" class="form-control" id="floatingInputGroup${index}" placeholder="URL">
                                <label for="floatingInputGroup${index}">Ingresa una URL</label>
                            </div>
                        </div>
                        <div class="form-floating mt-3">
                            <textarea class="form-control" id="floatingTextarea${index}" style="height: 100px"></textarea>
                            <label for="floatingTextarea${index}">Descripción</label>
                        </div>
                    `+ generarBotonGuardar(index);
                } else if (option === 'libro') {
                    container.innerHTML = `
                        <div class="form-floating mb-3 mt-3">
                            <input type="text" class="form-control" id="titleInputLibro${index}" placeholder="Título del libro" 
                                oninput="agregarPalabraLibro(${index})">
                            <label for="titleInputLibro${index}">Título del libro</label>
                        </div>

                        <div class="form-floating mt-3">
                            <textarea class="form-control" id="floatingTextarea${index}" style="height: 100px"></textarea>
                            <label for="floatingTextarea${index}">Descripción</label>
                        </div>

                        <div class="form-floating mt-3">
                            <input type="number" class="form-control" id="numeroPaginas${index}" placeholder="Número de páginas" min="1" value="1">
                            <label for="numeroPaginas${index}">Número de páginas</label>
                        </div>

                        <div class="text-end mt-3 mb-3">
                            <button class="btn btn-primary" onclick="generarPaginasLibro(${index})">Generar contenido por página</button>
                        </div>

                        <div id="contenedorPaginas${index}"></div>
                    ` + generarBotonGuardar(index);
                }
            }

            function agregarPalabraLibro(index) {
                const titleInput = document.getElementById('titleInputLibro' + index);
                
                if (titleInput && !titleInput.value.toLowerCase().startsWith('libro ')) {
                    titleInput.value = 'Libro ' + titleInput.value;
                }
            }

            function generarPaginasLibro(index) {
                const num = parseInt(document.getElementById('numeroPaginas' + index).value);
                const contenedor = document.getElementById('contenedorPaginas' + index);
                contenedor.innerHTML = '';

                for (let i = 1; i <= num; i++) {
                    contenedor.innerHTML += `
                        <div class="card mb-3">
                            <div class="card-header">Página ${i}</div>
                            <div class="card-body">
                                <div class="form-floating mb-3">
                                    <textarea class="form-control" id="textoPagina${index}_${i}" style="height: 100px"></textarea>
                                    <label for="textoPagina${index}_${i}">Texto</label>
                                </div>

                                <div class="input-group mb-3">
                                    <span class="input-group-text">URL</span>
                                    <input type="url" class="form-control" id="urlPagina${index}_${i}" placeholder="http://...">
                                </div>

                                <div class="mb-3">
                                    <label for="imagenPagina${index}_${i}" class="form-label">Imagen</label>
                                    <input type="file" class="form-control" id="imagenPagina${index}_${i}">
                                </div>

                            </div>
                        </div>
                    `;
                }
            }

            function normalizarTexto(texto) {
                return texto
                    .normalize("NFD")                         // separar acentos
                    .replace(/[\u0300-\u036f]/g, "")          // quitar acentos
                    .replace(/\s+/g, "")                      // quitar espacios
                    .replace(/[^a-zA-Z0-9_-]/g, "")           // quitar otros símbolos
                    .toLowerCase();
            }

            function cargarRecursos(nombreSeccion, numSeccion) {
                console.log('cargarRecursos llamado con:', { nombreSeccion, numSeccion });
                fetch('listar_recursos.php')
                    .then(res => res.json())
                    .then(data => {
                        console.log('📦 Data que llega del servidor:', data);
                        const recursosArray = Array.isArray(data) ? data : Object.values(data);
                        console.log("🔍 Comparando con sección:", nombreSeccion);
                        console.log("🔍 Secciones en data:", recursosArray.map(r => r.seccion));
                        

                        const recursosFiltrados = recursosArray.filter(r => 
                            normalizarTexto(r.seccion) === normalizarTexto(nombreSeccion)
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
                                    <strong>${r.titulo}</strong><br>
                                    <small>${r.descripcion}</small><br>
                                    ${r.archivo ? `<i class="bi bi-paperclip"></i> <a href="${r.archivo}" target="_blank">${r.archivo.split('/').pop()}</a><br>` : ''}
                                    ${r.url ? `<i class="bi bi-link"></i> <a href="${r.url}" target="_blank">${r.url}</a><br>` : ''}
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
    