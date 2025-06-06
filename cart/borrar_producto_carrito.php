<?php
session_start();
include('../components/conexion.php');

// Verificar si el usuario está logueado
if (!isset($_SESSION['idUser'])) {
    header("Location: ../pages/login_module/login_page.php");
    exit();
}

// Verificar si se recibió el ID del producto del carrito a eliminar
if (isset($_POST['idCart_product'])) {
    $idCartProduct = $_POST['idCart_product'];
    $conn = connectDB();
    mysqli_set_charset($conn, "utf8");

    // Preparar la consulta para eliminar el producto del carrito
    $sql = "DELETE FROM cart_product WHERE idCart_product = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $idCartProduct);
    
    if ($stmt->execute()) {
        // Éxito al eliminar
        $_SESSION['mensaje'] = "Producto eliminado del carrito correctamente.";
    } else {
        // Error al eliminar
        $_SESSION['error'] = "Error al eliminar el producto del carrito.";
    }
    
    $stmt->close();
    disconnectDB($conn);
} else {
    $_SESSION['error'] = "No se especificó qué producto eliminar.";
}

// Redireccionar de vuelta al carrito
header("Location: ver_carrito.php");
exit();
?>