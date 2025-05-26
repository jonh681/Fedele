<?php
session_start();
session_destroy(); // Cerramos sesión
header("Location: /fedele/index.html"); // Volvemos al login
exit();
?>
