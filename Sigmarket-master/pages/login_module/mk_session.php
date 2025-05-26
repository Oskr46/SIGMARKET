<?php include("../../config.php");
include("../../components/conexion.php");

session_start();

$email = $_POST['user'];
$password = $_POST['password'];

$crypt_pass = md5($password);

$conn = connectDB();

mysqli_set_charset($conn, "utf8");

if (!$conn) {
      die("Connection failed: " . mysqli_connect_error());
}

$consulta_email = mysqli_query($conn, "SELECT * FROM user WHERE emailUser = '$email' AND passwordUser = '$crypt_pass'");

if(!$consulta_email){echo "Algo está mal";}
    else{
    $numResults = $consulta_email->num_rows;
    }
if ($numResults != 0){
    $_SESSION['email'] = $email;
    echo "se encontro el usuario: ".$email; 
    header("location: ../../index.php");
    exit(); 
    }
    else{
        echo "No se encontraron coincidencias";
    }

?>

