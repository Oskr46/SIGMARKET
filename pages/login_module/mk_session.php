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
            while($fila = mysqli_fetch_array( $check_login ) ){
      
            //Ahora $fila tiene la primera fila de la consulta, pongamos que tienes
            //un campo en tu DB llamado NOMBRE, así accederías
            //echo $fila['Id'];
            
            $idUser = $fila['idUser'];
            $nombre = $fila['nameUser'];
            $apellido = $fila['sNameUser'];
            $tipo_usuario=$fila['adminBool'];
                        
          }
          mysqli_free_result( $check_login );
            // Login exitoso
            date_default_timezone_set('America/Caracas');
            $_SESSION['id'] = $idUser;
            $_SESSION['tipo'] = $tipo_usuario;   
            $_SESSION['email'] = $email;
            $_SESSION['name'] = $nombre;
            $_SESSION['sName'] = $apellido;
            $_SESSION['ultimo_acceso'] = date("Y-m-d H:i:s");
            header("Location: ../../index.php");
            exit();
        }
    
    }
} else {
    // Si no es POST, redirigir
    header("Location: login_page.php");
    exit();
}
?>
