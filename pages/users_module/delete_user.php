<?php
// Iniciar la sesión
session_start();

include('../../components/conexion.php');

// Verificar permisos de administrador
if (!isset($_SESSION['tipo']) || $_SESSION['tipo'] != 1) {
    header("location: ../../index.php");
    exit();
}

// Obtener ID del usuario a eliminar
$idUser = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($idUser <= 0) {
    header("location: users_panel.php");
    exit();
}

// Obtener información del usuario
$conn = connectDB();
mysqli_set_charset($conn, "utf8");

$query = "SELECT emailUser FROM user WHERE idUser = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $idUser);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$usuario = mysqli_fetch_assoc($result);

// No permitir eliminarse a sí mismo
if ($usuario['emailUser'] == $_SESSION['email']) {
    header("Location: users_panel.php?error=self_delete");
    exit();
}

// Procesar eliminación
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    // Iniciar transacción
    mysqli_begin_transaction($conn);

    try {
        // Eliminar el usuario
        $deleteQuery = "DELETE FROM users WHERE idUser = ?";
        $stmt = mysqli_prepare($conn, $deleteQuery);
        mysqli_stmt_bind_param($stmt, "i", $idUser);
        
        if (!mysqli_stmt_execute($stmt)) {
            throw new Exception("Error al eliminar el usuario");
        }

        // Confirmar transacción
        mysqli_commit($conn);
        header("Location: users_panel.php?deleted=1");
        exit();
    } catch (Exception $e) {
        mysqli_rollback($conn);
        header("Location: users_panel.php?error=1");
        exit();
    }
}

// Si no es una solicitud GET válida, redirigir
header("Location: users_panel.php");
exit();
?>