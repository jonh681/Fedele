<?php
session_start();


$nombre = $_SESSION['nombre'];
?>
<nav class="navbar navbar-expand-lg  bg-body-tertiary">
    <div class="container-fluid">
        <img src="/fedele/imagenes/fedele.png" alt="" width="120">
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent"
            aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse justify-content-between" id="navbarSupportedContent">
            <ul class="navbar-nav nav nav-underline nav-tabs me-auto mb-2 mb-lg-0" id="myTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <a class="nav-link" aria-current="page" id="home-tab" data-bs-toggle="tab" data-bs-target="#home-tab-pane" role="tab" aria-controls="home-tab-pane" aria-selected="false" href="#">Cursos disponibles</a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link active"id="tablero-tab" data-bs-toggle="tab" data-bs-target="#tablero-tab-pane" role="tab" aria-controls="tablero-tab-pane" aria-selected="true"  href="#">Tablero</a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link" id="cursos-tab" data-bs-toggle="tab" data-bs-target="#cursos-tab-pane" role="tab" aria-controls="cursos-tab-pane" aria-selected="false" href="#">Mis cursos</a>
                </li>
            </ul>

            <div class="dropdown ms-auto"> 
                <button class="btn btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown"
                    aria-expanded="false">
                    👤 <?php echo htmlspecialchars($nombre); ?>
                </button>
                <ul class="menu dropdown-menu">
                    <li><a class="dropdown-item text-center" href="#">⚙️ CONFIGURACIÓN</a></li>
                    <li><a class="dropdown-item text-center" href="/fedele/componentes/php/logout.php">🚪 CERRAR SESIÓN</a></li>
                </ul>
            </div>
        </div>
        
    </div>
</nav>