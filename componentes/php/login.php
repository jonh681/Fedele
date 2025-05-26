<?php

session_start();

include("conexion.php");


$conexion = getConexion();

$usuario = $_POST['usuario'] ?? '';
$password = $_POST['contraseña'] ?? '';

// Consulta
$consulta = "SELECT * FROM fedele.usuario WHERE Correo_Electronico = '$usuario' AND Contraseña = '$password'"; 
$resultado = mysqli_query($conexion, $consulta);
$filas = mysqli_num_rows($resultado);

if($filas){
    $usuarioDatos = mysqli_fetch_assoc($resultado); // Traer datos del usuario
    $nombre = $usuarioDatos['Nombre']; // Nombre real
    $_SESSION['nombre'] = $nombre;
    $_SESSION['usuario_id'] = $usuarioDatos['ID'];
    $puesto = $usuarioDatos['Puesto']; // Puesto: admin o usuario

    // Dependiendo el puesto, redireccionamos
    if($puesto === 'Administrador'){
        echo "<script>alert('Bienvenido administrador $nombre'); location='/fedele/componentes/admin/homePageAdmin.html';</script>";
    } elseif($puesto === 'Representante medico'){
        echo "<script>alert('Bienvenido $nombre'); location='/fedele/componentes/user/homePageUser.php';</script>";
    } else {
        // Por si el puesto está mal escrito
        echo "<script>alert('Error: puesto no reconocido.'); location='/fedele/index.html';</script>";
    }

} else {
    echo "<script>alert('Usuario o contraseña incorrectos'); location='/fedele/index.html';</script>";
}
?>
