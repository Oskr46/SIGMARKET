<?php
include ('../../components/conexion.php');

ini_set('display_errors', 1);
error_reporting(E_ALL);

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

$required_columns = ['emailUser', 'passwordUser', 'nameUser', 'sNameUser'];

// Construye la consulta dinámicamente
$columns = implode(', ', $required_columns);
$placeholders = implode(', ', array_fill(0, count($required_columns), '?'));

$sql = "INSERT INTO user (emailUser, passwordUser, nameUser, sNameUser, adminBool) 
        VALUES (?, ?, ?, ?, 0)";

$stmt = mysqli_prepare($conn, $sql);

// Verifica si hubo error en prepare
if ($stmt === false) {
    die("Error al preparar la consulta: " . mysqli_error($conn));
}

// 6. Bind parameters (CORREGIDO el nombre de la función)
if (!mysqli_stmt_bind_param($stmt, str_repeat('s', count($required_columns)), 
    $email, $password_crypted, $name, $sName)) {
    die("Error al bindear parámetros: " . mysqli_stmt_error($stmt));
}

// 7. Ejecutar
if (mysqli_stmt_execute($stmt)) {
    if (mysqli_stmt_affected_rows($stmt) > 0) {
        header("Location: register_confirm.php?email_reg=" . urlencode($email));
        exit();
    } else {
        die("No se insertaron filas. ¿La tabla está vacía?");
    }
} else {
    die("Error al ejecutar: " . mysqli_stmt_error($stmt));
}


// 7. Cerrar recursos
mysqli_stmt_close($stmt);
mysqli_close($conn);
?>