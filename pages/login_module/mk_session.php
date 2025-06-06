<?php 
include("../../config.php");
include("../../components/conexion.php");

session_start();

// Validar que se enviaron los datos
if (!isset($_POST['user']) || !isset($_POST['password'])) {
    header("Location: login_page.php?error=datos_faltantes");
    exit();
}

$email = $_POST['user'];
$password = $_POST['password'];

// Usar consultas preparadas para evitar SQL injection
$conn = connectDB();
mysqli_set_charset($conn, "utf8");

if (!$conn) {
    die("Error de conexión: " . mysqli_connect_error());
}

// Consulta preparada segura
$stmt = $conn->prepare("SELECT idUser, nameUser, emailUser FROM user WHERE emailUser = ? AND passwordUser = MD5(?)");
$stmt->bind_param("ss", $email, $password);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $usuario = $result->fetch_assoc();
    
    // Guardar datos importantes en sesión
    $_SESSION['idUser'] = $usuario['idUser'];
    $_SESSION['email'] = $usuario['emailUser'];
    $_SESSION['nombre'] = $usuario['nameUser'];
    
    header("Location: ../../index.php");
    exit();
} else {
    header("Location: login_page.php?error=credenciales_invalidas");
    exit();
}

?>
