<?php
// Iniciar la sesión
session_start();

include('../../components/conexion.php');

// Verificar permisos de administrador
if (!isset($_SESSION['tipo']) || $_SESSION['tipo'] != 1) {
    header("location: ../../index.php");
    exit();
}

// Obtener ID del producto a eliminar
$idProduct = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($idProduct <= 0) {
    header("location: prod_panel.php");
    exit();
}

// Obtener información del producto para eliminar su imagen
$conn = connectDB();
mysqli_set_charset($conn, "utf8");

$query = "SELECT p.*, i.urlImage 
          FROM products p
          LEFT JOIN imageproduct i ON p.idProduct = i.idProduct
          WHERE p.idProduct = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $idProduct);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$producto = mysqli_fetch_assoc($result);

if (!$producto) {
    header("location: prod_panel.php");
    exit();
}

// Procesar eliminación
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    // Iniciar transacción
    mysqli_begin_transaction($conn);

    try {
        // Eliminar imagen asociada si existe
        if (!empty($producto['urlImage'])) {
            // Primero eliminar el registro de la imagen
            $deleteImageQuery = "DELETE FROM imageproduct WHERE idProduct = ?";
            $stmt = mysqli_prepare($conn, $deleteImageQuery);
            mysqli_stmt_bind_param($stmt, "i", $idProduct);
            
            if (!mysqli_stmt_execute($stmt)) {
                throw new Exception("Error al eliminar la imagen de la base de datos");
            }

            // Luego eliminar el archivo físico
            $image_path = '../../' . $producto['urlImage'];
            if (file_exists($image_path)) {
                unlink($image_path);
            }
        }

        // Eliminar el producto
        $deleteProductQuery = "DELETE FROM products WHERE idProduct = ?";
        $stmt = mysqli_prepare($conn, $deleteProductQuery);
        mysqli_stmt_bind_param($stmt, "i", $idProduct);
        
        if (!mysqli_stmt_execute($stmt)) {
            throw new Exception("Error al eliminar el producto");
        }

        // Confirmar transacción
        mysqli_commit($conn);
        header("Location: prod_panel.php?deleted=1");
        exit();
    } catch (Exception $e) {
        mysqli_rollback($conn);
        header("Location: prod_panel.php?error=1");
        exit();
    }
}

// Si no es una solicitud GET válida, redirigir
header("Location: prod_panel.php");
exit();
?>