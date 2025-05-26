<?php
session_start();


$nombre = $_SESSION['nombre'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-SgOJa3DmI69IUzQ2PVdRZhwQ+dy64/BUtbMJw1MZ8t5HZApcHrRKUc4W0kG879m7" crossorigin="anonymous">
    <link rel="stylesheet" href="header/css/diseñoheader.css">
    <link rel="stylesheet" href="misCursos/css/diseñoHome.css">
    <link rel="stylesheet" href="tablero/css/diseñoTablero.css">
    <link rel="stylesheet" href="misCursos/css/diseñoCursos.css">
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.17/index.global.min.js'></script>
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.17/locales/es.global.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">


</head>
<body style="overflow: hidden;">
    <div>
        <div class="header" id="header-container" style=" width: auto"></div>

        <div style="overflow: auto; height: 80vh; width: 100%;">
            <div class="tab-content" id="myTabContent">
                <div class="tab-pane fade show active"  id="tablero-tab-pane" role="tabpanel" aria-labelledby="tablero-tab" tabindex="0"></div>
                <div class="tab-pane fade"  id="home-tab-pane" role="tabpanel" aria-labelledby="home-tab" tabindex="0"></div>
                <div class="tab-pane fade " id="cursos-tab-pane" role="tabpanel" aria-labelledby="cursos-tab" tabindex="0"></div>
            </div>
        </div>

    </div>

    <!-- renderizacion y creacion de eventos del calendario -->
    <script>
        function cargarComponentes(id, archivo){
            fetch(archivo)
                .then(response => response.text())
                .then(data =>{
                    document.getElementById(id).innerHTML = data;
                    if (id === 'tablero-tab-pane') {
                        setTimeout(() => {
                            const calendarEl = document.getElementById('calendar');
                            if (calendarEl) {
                                fetch('/fedele/componentes/php/cursosDisponibles.php')
                                    .then(res => res.json())
                                    .then(cursos => {
                                        // Convertimos los cursos en eventos
                                        const eventos = cursos.map(curso => ({
                                            title: curso.titulo,
                                            start: curso.fechaInicio,
                                            end: curso.fechaFin
                                        }));

                                        // Renderizamos el calendario
                                        const calendar = new FullCalendar.Calendar(calendarEl, {
                                            locale: 'es',
                                            buttonText: {
                                                today: 'Hoy',
                                            },
                                            initialView: 'dayGridMonth',
                                            events: eventos,
                                            dateClick: function(info) {
                                                alert('Fecha: ' + info.dateStr);
                                            }
                                            
                                        });

                                        calendar.render();
                                    });
                            } else {
                                console.error("No se encontró el div con id 'calendar'");
                            }
                        }, 100);
                    }
                    
                    if (id === 'home-tab-pane'){
                        setTimeout(() => {
                            fetch('/fedele/componentes/php/cursosDisponibles.php')
                            .then(res => res.json())
                            .then(cursos => {
                                const contenedor = document.querySelector('.contenedorCursos');

                                if (!contenedor) {
                                    console.error("No se encontró '.contenedorCursos'");
                                    return;
                                }

                                contenedor.innerHTML = ''; // Limpiar contenido

                                if (cursos.length === 0) {
                                    contenedor.innerHTML = `
                                        <div class="alert alert-info text-center mt-4" role="alert">
                                            <i class="bi bi-info-circle-fill"></i> Por el momento no hay cursos disponibles.
                                        </div>
                                    `;
                                    return;
                                }

                                fetch('/fedele/componentes/php/get_inscripciones.php', { credentials: 'include' })
                                    .then(response => response.json())
                                    .then(cursosInscritos => {
                                        cursos.forEach(curso => {
                                            const estaInscrito = cursosInscritos.some(inscrito =>
                                                inscrito.nombre_curso.trim().toLowerCase() === curso.titulo.trim().toLowerCase()
                                            );
                                            const div = document.createElement('div');
                                            div.className = 'cursos p-3 border rounded mb-3';
                                            div.innerHTML = `
                                                <div class="card mb-3" style="width: 100%;">
                                                    <div class="row g-0">
                                                        <div class="col-md-4">
                                                            <img src="${curso.imagen || 'misCursos/imagenes/fedele.png'}" width="220" class="img-fluid rounded-start">
                                                        </div>
                                                        <div class="col-md-8" style="color:#234db8;">
                                                            <div class="card-body">
                                                                <h5 class="card-title">${ curso.titulo}</h5>
                                                                <p class="card-text">${curso.descripcion}</p>
                                                                <p class="card-text"><strong>Inicio: ${curso.fechaInicio}</strong></p>
                                                                <p class="card-text"><strong>Fin: ${curso.fechaFin}</strong></p>

                                                                ${
                                                                    estaInscrito
                                                                        ? '<span class="badge bg-success">Ya inscrito</span>'
                                                                        : `<button class="btn btn-primary inscribirme-btn"
                                                                                data-curso="${curso.titulo}"
                                                                                data-inicio="${curso.fechaInicio}"
                                                                                data-fin="${curso.fechaFin}">Inscribirme
                                                                            </button>`
                                                                }
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            `;
                                            contenedor.appendChild(div);
                                        });
                                    });


                                    document.addEventListener('click', function(e) {
                                        if (e.target && e.target.classList.contains('inscribirme-btn')) {
                                            const nombreCurso = e.target.getAttribute('data-curso');
                                            const fechaInicio = e.target.getAttribute('data-inicio');
                                            const fechaFin = e.target.getAttribute('data-fin');

                                            fetch('/fedele/componentes/php/inscribirse.php', {
                                                method: 'POST',
                                                headers: {
                                                    'Content-Type': 'application/x-www-form-urlencoded',
                                                },
                                                body: `curso=${encodeURIComponent(nombreCurso)}&inicio=${encodeURIComponent(fechaInicio)}&fin=${encodeURIComponent(fechaFin)}`

                                            })
                                            .then(response => response.text())
                                            .then(data => {
                                                const respuesta = data.trim();

                                                if (respuesta === "success") {
                                                    alert("✅ Inscripción exitosa");
                                                    window.location.href = "/fedele/componentes/user/homePageUser.php";
                                                } else {
                                                    alert(respuesta);
                                                    if (respuesta.includes("Ya estás inscrito")) {
                                                        e.target.innerText = 'Inscrito';
                                                        e.target.disabled = true;
                                                    }
                                                }
                                            })
                                            .catch(error => {
                                                console.error('Error al inscribirse:', error);
                                                alert("❌ Ocurrió un error al intentar inscribirse.");
                                            });
                                        }
                                    });
                            });
                        }, 100);
                    }

                    if (id === 'cursos-tab-pane') {
                        setTimeout(() => {
                            const contenedor = document.querySelector('.contenedorMisCursos');
                            if (!contenedor) {
                                console.error("No se encontró '.contenedorMisCursos'");
                                return;
                            }

                            fetch('/fedele/componentes/php/cursosDisponibles.php')
                                .then(res => res.json())
                                .then(cursos => {
                                    fetch('/fedele/componentes/php/get_inscripciones.php', { credentials: 'include' })
                                        .then(response => response.json())
                                        .then(cursosInscritos => {
                                            const inscritos = cursos.filter(curso =>
                                                cursosInscritos.some(inscrito =>
                                                    inscrito.nombre_curso.trim().toLowerCase() === curso.titulo.trim().toLowerCase()
                                                )
                                            );

                                            if (inscritos.length === 0) {
                                                contenedor.innerHTML = `
                                                    <div class="alert alert-warning text-center">
                                                        <i class="bi bi-info-circle"></i> Aún no estás inscrito en ningún curso.
                                                    </div>`;
                                                return;
                                            }

                                            contenedor.innerHTML = '';
                                            inscritos.forEach(curso => {
                                                const inscripcion = cursosInscritos.find(inscrito =>
                                                    inscrito.nombre_curso.trim().toLowerCase() === curso.titulo.trim().toLowerCase()
                                                );

                                                const hoy = new Date();
                                                const inicio = new Date(inscripcion.fecha_inicio + 'T12:00:00');
                                                const fin = new Date(inscripcion.fecha_fin);

                                                let estadoHTML = '';
                                                if (hoy < inicio) {
                                                    estadoHTML = `<button class="btn btn-warning" disabled>El curso aún no inicia 🕒 (${inicio.toLocaleDateString('es-MX')})</button>`;
                                                } else if (hoy >= inicio && hoy <= fin) {
                                                    estadoHTML = `<a href="/fedele/componentes/php/verCurso.php?curso=${encodeURIComponent(curso.titulo)}" class="btn btn-primary">Entrar</a>`;
                                                } else {
                                                    estadoHTML = `<button class="btn btn-secondary" disabled>El curso ya terminó ⌛</button>`;
                                                }

                                                const div = document.createElement('div');
                                                div.className = 'cursos p-3 border rounded mb-3';
                                                div.innerHTML = `
                                                    <div class="card mb-3" style="width: 100%;">
                                                        <div class="row g-0">
                                                            <div class="col-md-4 d-flex align-items-center justify-content-center">
                                                                <img src="${curso.imagen || 'misCursos/imagenes/fedele.png'}" width="170" class="img-fluid rounded-start p-2" style="max-height: 120px;">
                                                            </div>
                                                            <div class="col-md-8 d-flex align-items-center">
                                                                <div class="card-body text-center w-100">
                                                                    <h5 class="card-title mb-2">${curso.titulo}</h5>
                                                                    <p class="card-text mb-2">${curso.descripcion}</p>
                                                                    <span class="badge bg-success mb-2">Inscrito</span></br>
                                                                    ${estadoHTML}   
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                `;
                                                contenedor.appendChild(div);
                                            });
                                        });
                                });
                        }, 100);
                    }


                });
        }
        cargarComponentes('header-container', 'header/header.php')
        cargarComponentes('home-tab-pane', 'misCursos/contenido.php')
        cargarComponentes('tablero-tab-pane', '/fedele/componentes/user/tablero/tablero.php')
        cargarComponentes('cursos-tab-pane', 'misCursos/misCursos.html')
    </script>

    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js" integrity="sha384-k6d4wzSIapyDyv1kpU366/PK5hCdSbCRGRCMv+eplOQJWyd1fbcAu9OCUj5zNLiq" crossorigin="anonymous"></script>
</body>
</html>