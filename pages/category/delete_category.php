<?php
session_start();
include ('../../components/conexion.php');

// Verificar sesión y permisos
if (!isset($_SESSION['name']) || !isset($_SESSION['sName']) || !isset($_SESSION['tipo']) || !isset($_SESSION['email'])) {
    header("location: index.php");
}

$tipo_usuario = $_SESSION['tipo'];   
if($tipo_usuario!=1){
    session_destroy();
    header("location: ../../index.php");
}

$conn = connectDB();

// Obtener ID de la categoría a eliminar
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Verificar si la categoría existe
$checkQuery = "SELECT * FROM category WHERE idCategory = $id";
$checkResult = mysqli_query($conn, $checkQuery);

if(mysqli_num_rows($checkResult) == 0) {
    header("location: category_panel.php?error=2");
    exit;
}

// Verificar si hay productos asociados a esta categoría
$productsQuery = "SELECT COUNT(*) as total FROM products WHERE categoryProduct = $id";
$productsResult = mysqli_query($conn, $productsQuery);
$productsData = mysqli_fetch_assoc($productsResult);

if($productsData['total'] > 0) {
    header("location: category_panel.php?error=3");
    exit;
}

// Eliminar la categoría
$deleteQuery = "DELETE FROM category WHERE idCategory = $id";
if(mysqli_query($conn, $deleteQuery)) {
    header("location: category_panel.php?success=3");
} else {
    header("location: category_panel.php?error=4");
}
?>