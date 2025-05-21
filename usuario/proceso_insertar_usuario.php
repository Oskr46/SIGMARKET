<?php
// Iniciar la sesión
session_start();

include ('conexion.php');

// Verificar si las variables de sesión existen
if (!isset($_SESSION['nombre']) || !isset($_SESSION['apellido']) || !isset($_SESSION['tipo'])) {
    header("location: index.php");
}

$tipo_usuario = $_SESSION['tipo'];   
$usuario = $_SESSION['user'];
$nombre = $_SESSION['nombre'];
$apellido = $_SESSION['apellido'];

if($tipo_usuario!=1){
    session_destroy();
    header("location: index.php");
}

$email_nuevo_usuario = $_POST['email_nuevo_usuario'];
$password_nuevo_usuario = $_POST['password_nuevo_usuario'];
$nombres_nuevo_usuario = $_POST['nombres_nuevo_usuario'];
$apellidos_nuevo_usuario = $_POST['apellidos_nuevo_usuario'];
$tipo_usuario_nuevo_usuario = $_POST['tipo_usuario_nuevo_usuario'];
$password_encriptado_nuevo_usuario = md5($password_nuevo_usuario);

$conn = connectDB();
//Estableciendo caracteres UTF8 para BD, importante para acentos y eñes en MySQL                            
mysqli_set_charset($conn, "utf8");
                              
                              
// Check connection
if (!$conn) {
      die("Connection failed: " . mysqli_connect_error());
}

//Comprobación que la dirección de email no esté registrada

$consulta_email = mysqli_query($conn, "SELECT * FROM usuarios WHERE email = '$email_nuevo_usuario'");

if(!$consulta_email){echo "Algo está mal";}
    else{
    $numResults = $consulta_email->num_rows;
    }
if ($numResults != 0) {
    header("location:email_ya_registrado.php?email=$email_nuevo_usuario");
    exit(); 
    }

$sql = "INSERT INTO usuarios (email, password, tipo, nombres, apellidos) 
VALUES ('$email_nuevo_usuario', '$password_encriptado_nuevo_usuario', '$tipo_usuario_nuevo_usuario', '$nombres_nuevo_usuario', '$apellidos_nuevo_usuario')";

if (mysqli_query($conn, $sql)) {
    echo "Nuevo registro creado exitosamente";
} else {
    echo "Error: " . $sql . "<br>" . mysqli_error($conn);
}

mysqli_close($conn);

header("location:confirmacion_insertar_usuario.php?email=$email_nuevo_usuario");

exit(); 

?>