<?php
session_start();


$nombre = $_SESSION['nombre'];
?>
<div class="content">
    <h1 class="text-center my-4">¡Hola <?php echo htmlspecialchars($nombre); ?>! Bienvenido</h1>
    <hr>
    <h4 class="text-center mt-4">📅 Calendario de Cursos</h4>
    <!-- <div class="resumen">
        <h2>Línea del tiempo</h2>
        <div class="contentTiempo">
            <div class="filtrosTiempo">
                <div class="form-floating">
                    <select class="form-select" id="floatingSelect1" aria-label="Floating label select example">
                        <option selected>Selecciona una opcion</option>
                        <option value="#">Todos</option>
                        <option value="#">Vencidos</option>
                        <hr>
                        <option value="#" disabled>Fecha de entrega</option>
                        <option value="#">Próximos 7 días</option>
                        <option value="#">Próximos 30 días</option>
                        <option value="#">Próximos 3 meses</option>
                        <option value="#">Próximos 6 meses</option>
                    </select>
                    <label for="floatingSelect1">Tiempo</label>   
                </div>

                <div class="form-floating">
                    <select class="form-select" id="floatingSelect2" aria-label="Floating label select example">
                        <option selected>Selecciona una opcion</option>
                        <option value="#">Ordenar por fechas</option>
                        <option value="#">Ordenar por cursos</option>
                    </select>
                    <label for="floatingSelect2">Orden</label>   
                </div>
            </div>
            <div class="buscar">
                <div class="form-floating mb-3">
                    <input type="text" class="form-control" id="floatingInput">
                    <label for="floatingInput">Buscar por nombre o tipo de actividad</label>
                </div>
            </div>
        </div>
        <hr>
        <div class="resultados">
            <img src="img/activities.svg" width="60" alt=""> <br>
            <label for="">Sin cursos en progreso</label>
        </div>
    </div> -->

    <!-- calendario -->
    <div class="calendario">
        <div id="calendar" style="font-color:#234db8;"></div>
    </div>
</div>
