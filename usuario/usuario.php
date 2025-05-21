<?php
// Iniciar la sesión
session_start();

// Verificar si las variables de sesión existen
if (!isset($_SESSION['nombre']) || !isset($_SESSION['apellido']) || !isset($_SESSION['tipo'])) {
    header("location: index.php");
}

$tipo_usuario = $_SESSION['tipo'];   
$usuario = $_SESSION['user'];
$nombre = $_SESSION['nombre'];
$apellido = $_SESSION['apellido'];

if($tipo_usuario!=2){
    session_destroy();
    header("location: index.php");
}
?>



<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de usuario</title>
</head>
<body>
    <h1>Panel de usuario</h1>
    
    <p>Bienvenido: <?php echo $nombre ." ". $apellido; ?></p>
    
    <p><a href="cerrar_sesion.php">Cerrar sesión</a></p>
</body>
</html>