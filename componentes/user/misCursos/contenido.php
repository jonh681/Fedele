<?php
session_start();
$nombre = $_SESSION['nombre'];
?>

    <div class="contentCursos">
        <div>
            <h1>Academia Fedele</h1>
            <hr>
            <h4>¡Hola <?php echo htmlspecialchars($nombre); ?>! estos son los cursos disponibles para ti.</h4>
        </div>

        <div class="contenedorCursos row" style=" width: 73vh;  margin: 0% auto;">
        </div>
    </div>
