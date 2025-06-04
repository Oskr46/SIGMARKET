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
?>