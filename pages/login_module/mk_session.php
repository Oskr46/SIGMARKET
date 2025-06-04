<?php 
include("../../config.php");
include("../../components/conexion.php");

session_start();

// Verifica si se envió el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['user'] ?? '';
    $password = $_POST['password'] ?? '';

    $crypt_pass = md5($password);
    $conn = connectDB();

    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }

    // 1. Verificar si el email existe
    $check_email = mysqli_query($conn, "SELECT * FROM user WHERE emailUser = '$email'");
    
    if (mysqli_num_rows($check_email) === 0) {
        // Email no existe
        $_SESSION['login_error'] = 'email_not_found';
        $_SESSION['attempted_email'] = $email; // Guardar el email intentado
        header("Location: loginincorrect_page.php");
        exit();
    } else {
        // 2. Verificar contraseña
        $check_login = mysqli_query($conn, "SELECT * FROM user WHERE emailUser = '$email' AND passwordUser = '$crypt_pass'");
        
        if (mysqli_num_rows($check_login) === 0) {
            // Contraseña incorrecta
            $_SESSION['login_error'] = 'wrong_password';
            $_SESSION['attempted_email'] = $email;
            header("Location: loginincorrect_page.php");
            exit();
        } else {
            // Login exitoso
            $_SESSION['email'] = $email;
            header("Location: ../../index.php");
            exit();
        }
    }
} else {
    // Si no es POST, redirigir
    header("Location: login_page.php");
    exit();
}
<<<<<<< HEAD

$consulta = mysqli_query($conn, "SELECT * FROM user WHERE emailUser = '$email' AND passwordUser = '$crypt_pass'");

if(!$consulta){echo "Algo está mal";}
    else{
    $numResults = $consulta->num_rows;
    }
if ($numResults != 0){
    mysqli_set_charset($conn, "utf8");
    $resultado = mysqli_query($conn, "SELECT * FROM user WHERE emailUser = '$email'");
   
    if($resultado){
        //Ahora valida que la consuta haya traido registros
        if( mysqli_num_rows( $resultado ) > 0){
      
          //Mientras mysqli_fetch_array traiga algo, lo agregamos a una variable temporal
          while($fila = mysqli_fetch_array( $resultado ) ){
      
            //Ahora $fila tiene la primera fila de la consulta, pongamos que tienes
            //un campo en tu DB llamado NOMBRE, así accederías
            //echo $fila['Id'];
            
            $nombre = $fila['nameUser'];
            $apellido = $fila['sNameUser'];
            $tipo_usuario=$fila['adminBool'];
  
            echo $usuario;
            echo "<html> <br> <br></html>";
            echo $nombre;
            echo "<html> <br> <br></html>";
                        
          }
      
        } 
  //Recuerda liberar la memoria del resultado, 
  mysqli_free_result( $resultado );
    }
    mysqli_close($conn);

    date_default_timezone_set('America/Caracas');
    $_SESSION['tipo'] = $tipo_usuario;   
    $_SESSION['email'] = $email;
    $_SESSION['name'] = $nombre;
    $_SESSION['sName'] = $apellido;
    $_SESSION['ultimo_acceso'] = date("Y-m-d H:i:s"); 
    echo "se encontro el usuario: ".$email; 
    header("location: ../../index.php");
    exit(); 
    }
    else{
        echo "No se encontraron coincidencias";
    }

?>

=======
?>
>>>>>>> Jd_Login
