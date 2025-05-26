<?php
function getConexion() {
    $host = 'localhost';  // O '127.0.0.1'
    $dbname = 'fedele';  // Nombre de tu base de datos
    $username = 'root';  // Usuario de MySQL
    $password = '';  // Contraseña de MySQL, si la tienes
    $port = '3307';  // Si estás usando otro puerto como 3307


    $conexion = mysqli_connect($host, $username, $password, $dbname, $port);
    if (!$conexion) {
        die("Conexión fallida: " . mysqli_connect_error());
    }
    return $conexion;
}
?>

