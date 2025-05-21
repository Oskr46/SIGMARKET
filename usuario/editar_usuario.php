<?php
// Iniciar la sesión
session_start();

$tipo_usuario = $_SESSION['tipo'];   
$usuario = $_SESSION['user'];
$nombre = $_SESSION['nombre'];
$apellido = $_SESSION['apellido'];

include ('conexion.php');

// Verificar si las variables de sesión existen
if (!isset($_SESSION['nombre']) || !isset($_SESSION['apellido']) || !isset($_SESSION['tipo'])) {
    header("location: index.php");
}

$conn = connectDB();
//Estableciendo caracteres UTF8 para BD, importante para acentos y eñes en MySQL                            
mysqli_set_charset($conn, "utf8");

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

if($tipo_usuario!=1){
    session_destroy();
    header("location: index.php");
}

$email_usuario = $_POST['email'];

$q = "SELECT * from usuarios WHERE email = '$email_usuario'";
$consulta = mysqli_query($conn,$q);
$fila = mysqli_fetch_array($consulta);
$nombres_para_editar = $fila['nombres'];
$apellidos_para_editar = $fila['apellidos'];

?>