<?php
session_start();
require_once '../components/conexion.php';

// Verificar si el usuario está logueado
if (!isset($_SESSION['idUser'])) {
    header("Location: ../pages/login_module/login_page.php");
    exit();
}

// Verificar que se recibieron los datos necesarios
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['idCart_product']) && isset($_POST['cantidad'])) {
    $conn = connectDB();
    
    // Obtener y sanitizar los datos
    $idCartProduct = intval($_POST['idCart_product']);
    $nuevaCantidad = intval($_POST['cantidad']);
    $idUser = $_SESSION['idUser'];
    
    // Validar que la cantidad sea mayor a 0
    if ($nuevaCantidad <= 0) {
        $_SESSION['error'] = "La cantidad debe ser mayor a cero";
        header("Location: ver_carrito.php");
        exit();
    }
    
    try {
        // Verificar que el producto pertenece al usuario (seguridad)
        $verificarQuery = "SELECT cp.idCart_product 
                          FROM cart_product cp
                          JOIN cart c ON cp.idCart = c.idCart
                          WHERE cp.idCart_product = ? AND c.idUser = ?";
        $stmt = $conn->prepare($verificarQuery);
        $stmt->bind_param("ii", $idCartProduct, $idUser);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 0) {
            $_SESSION['error'] = "No tienes permiso para modificar este producto";
            header("Location: ver_carrito.php");
            exit();
        }
        
        // Actualizar la cantidad
        $updateQuery = "UPDATE cart_product SET cantidad = ? WHERE idCart_product = ?";
        $stmt = $conn->prepare($updateQuery);
        $stmt->bind_param("ii", $nuevaCantidad, $idCartProduct);
        
        if ($stmt->execute()) {
            $_SESSION['success'] = "Cantidad actualizada correctamente";
        } else {
            $_SESSION['error'] = "Error al actualizar la cantidad";
        }
        
        $stmt->close();
    } catch (Exception $e) {
        $_SESSION['error'] = "Error en la base de datos: " . $e->getMessage();
    } finally {
        disconnectDB($conn);
    }
} else {
    $_SESSION['error'] = "Datos incompletos";
}

header("Location: ver_carrito.php");
exit();
?>
