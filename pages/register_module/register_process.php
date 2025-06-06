<?php
include ('../../components/conexion.php');

$email = $_POST['email'];
$password = $_POST['password'];
$name = $_POST['name'];
$sName = $_POST['sName'];

$password_crypted = md5($password);

$conn = connectDB();
//Estableciendo caracteres UTF8 para BD, importante para acentos y eñes en MySQL                            
mysqli_set_charset($conn, "utf8");
                              
                              
// Check connection
if (!$conn) {
      die("Connection failed: " . mysqli_connect_error());
}

//Comprobación que la dirección de email no esté registrada

$consulta_email = mysqli_query($conn, "SELECT * FROM user WHERE emailUser = '$email'");

if(!$consulta_email){echo "Algo está mal";}
    else{
    $numResults = $consulta_email->num_rows;
    }
if ($numResults != 0) {
    header("location:already_email.php?email_reg=$email");
    exit(); 
    }

$sql = "INSERT INTO user (emailUser, passwordUser, nameUser, sNameUser) 
VALUES ('$email', '$password_crypted', '$name', '$sName')";

if (mysqli_query($conn, $sql)) {
    echo "Nuevo registro creado exitosamente";
} else {
    echo "Error: " . $sql . "<br>" . mysqli_error($conn);
}

mysqli_close($conn);

header("location:register_confirm.php?email_reg=$email");

exit(); 

?>