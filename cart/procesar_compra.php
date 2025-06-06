<?php
session_start();
include('../components/conexion.php');
include('../components/header_footer.php');

// Verificar si el usuario está logueado
if (!isset($_SESSION['idUser'])) {
    header("Location: ../pages/login_module/login_page.php");
    exit();
}

$idUser = $_SESSION['idUser'];
$conn = connectDB();
mysqli_set_charset($conn, "utf8");

// Obtener el carrito más reciente del usuario
$sql = "SELECT idCart FROM cart WHERE idUser = ? ORDER BY idCart DESC LIMIT 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $idUser);
$stmt->execute();
$result = $stmt->get_result();
$carrito = $result->fetch_assoc();
$stmt->close();

$cartItems = [];
$total = 0;
$fechaCompra = date('Y-m-d '); // Fecha actual

if ($carrito) {
    $idCart = $carrito['idCart'];
    
    // Obtener los productos del carrito
    $cartQuery = "SELECT cp.idCart_product, cp.cantidad, p.priceProduct, 
                 p.idProduct, p.nameProduct, p.descriptionProduct,
                 (SELECT urlImage FROM imageproduct WHERE idProduct = p.idProduct LIMIT 1) AS producto_img
                 FROM cart_product cp 
                 INNER JOIN products p ON cp.idProduct = p.idProduct 
                 WHERE cp.idCart = ?";
    
    $stmt = $conn->prepare($cartQuery);
    $stmt->bind_param("i", $idCart);
    $stmt->execute();
    $cartItems = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    
    // Calcular el total
    foreach ($cartItems as $item) {
        $total += $item['cantidad'] * $item['priceProduct'];
    }
    
    // Aquí iría la lógica para procesar el pago realmente
    // Por ahora solo mostramos la confirmación
}

disconnectDB($conn);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../styles/global_login.css">
    <link rel="icon" href="../res/img/favicon_white.png" type="image/x-icon">
    <link rel="stylesheet" href="<?php echo BASE_URL?>styles/global.css"/>
    <link rel="stylesheet" href="../styles/procesar_compra.css">
    <title>SIGMARKET - Procesar Compra</title>
</head>
<body>
    <header class="header">
        <?php show_header(); ?>
    </header>

    <div class="container">
        <h1>Confirmación de Compra</h1>
        
        <div class="compra-info">
            <div class="fecha-total">
                <p><strong>Fecha de compra:</strong> <?= date('d/m/Y ', strtotime($fechaCompra)) ?></p>
                <p><strong>Total de la compra:</strong> $<?= number_format($total, 2) ?></p>
            </div>
            
            <h2>Productos comprados:</h2>
            <div class="productos-comprados">
                <?php foreach ($cartItems as $item): ?>
                    <div class="producto-card">
                        <?php if (!empty($item['producto_img'])): ?>
                            <img src="<?php echo BASE_URL;?>res/img/products/<?= htmlspecialchars($item['producto_img']) ?>" 
                                 alt="<?= htmlspecialchars($item['nameProduct']) ?>" 
                                 class="producto-image">
                        <?php else: ?>
                            <div class="producto-image" style="background: #f0f0f0; display: flex; align-items: center; justify-content: center;">
                                <span>Sin imagen</span>
                            </div>
                        <?php endif; ?>
                        <div class="producto-info">
                            <p><strong><?= htmlspecialchars($item['nameProduct']) ?></strong></p>
                            <p>Cantidad: <?= $item['cantidad'] ?></p>
                            <p>Precio unitario: $<?= number_format($item['priceProduct'], 2) ?></p>
                            <p>Subtotal: $<?= number_format($item['cantidad'] * $item['priceProduct'], 2) ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <div class="acciones-finales">
                <?php $base_url = '/Sigmarket-jorge-carrito'; ?>
                <a href="<?php echo $base_url; ?>/pages/products_module/product_page.php" class="btn-volver">Volver a Productos</a>
            </div>
        </div>
    </div>

    <footer class="footer">
        <?php show_footer(); ?>
    </footer>
</body>
</html>