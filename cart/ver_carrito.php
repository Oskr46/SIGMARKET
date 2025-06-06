<?php
session_start();
include('../components/conexion.php');
include('../components/header_footer.php');

if (!isset($_SESSION['idUser'])) {
    header("Location: ../pages/login_module/login_page.php");
    exit();
}

$idUser = $_SESSION['idUser'];
$conn = connectDB();
mysqli_set_charset($conn, "utf8");

// Obtener el carrito del usuario
$sql = "SELECT idCart FROM cart WHERE idUser = ? AND status = 'active' ORDER BY idCart DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $idUser);
$stmt->execute();
$result = $stmt->get_result();
$carrito = $result->fetch_assoc();
$stmt->close();

$cartItems = [];
$total = 0;

if (!$carrito) {
    $sql = "INSERT INTO cart (idUser, status) VALUES (?, 'active')";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $idUser);
    $stmt->execute();
    $idCart = $stmt->insert_id;
    $stmt->close();
} else {
    $idCart = $carrito['idCart'];
    // Consulta para obtener productos del carrito con imágenes
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
    
    foreach ($cartItems as $item) {
        $total += $item['cantidad'] * $item['priceProduct'];
    }
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
    <link rel="stylesheet" href="../styles/carrito.css">
    <title>SIGMARKET - Mi Carrito</title>

</head>
<body>
    <header class="header">
        <?php show_header(); ?>
    </header>

    <div class="container">
        <div class="cart-header">
            <h1>Mi Carrito de Compras</h1>
        </div>

        <?php if (empty($cartItems)): ?>
            <div class="empty-cart">
                <p>No tienes productos en tu carrito.</p>
                <?php $base_url = '/Sigmarket-jorge-carrito'; ?>
                <a href="<?php echo $base_url; ?>/pages/products_module/product_page.php" class="continue-shopping">Continuar comprando</a>
            </div>
        <?php else: ?>
            <div class="cart-items">
                <?php foreach ($cartItems as $item): ?>
                    <div class="product-card">
                        <div class="product-image-container">
                            <?php if (!empty($item['producto_img'])): ?>
                                <img src="<?php echo BASE_URL;?>res/img/products/<?= htmlspecialchars($item['producto_img']) ?>" 
                                     alt="<?= htmlspecialchars($item['nameProduct']) ?>" 
                                     class="product-image">
                            <?php else: ?>
                                <div class="product-image" style="background: #f0f0f0; display: flex; align-items: center; justify-content: center;">
                                    <span>Sin imagen</span>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="product-info">
                            <h3 class="product-title"><?= htmlspecialchars($item['nameProduct']) ?></h3>
                            <p class="product-description"><?= htmlspecialchars($item['descriptionProduct']) ?></p>
                            <p class="product-price">Precio unitario: $<?= number_format($item['priceProduct'], 2) ?></p>
                            <p class="product-quantity">Cantidad: <?= $item['cantidad'] ?></p>
                        </div>
                        
                        <div class="product-actions">
                            <form method="POST" action="actualizar_el_carrito.php" class="update-form">
                                <input type="hidden" name="idCart_product" value="<?= $item['idCart_product'] ?>">
                                <input type="number" name="cantidad" value="<?= $item['cantidad'] ?>" min="1" class="quantity-input">
                                <button type="submit" class="btn btn-update">Actualizar</button>
                            </form>
                            
                            <form method="POST" action="borrar_producto_carrito.php" class="delete-form">
                                <input type="hidden" name="idCart_product" value="<?= $item['idCart_product'] ?>">
                                <button type="submit" class="btn btn-delete">Eliminar</button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <div class="cart-summary">
                <p class="cart-total">Total: $<?= number_format($total, 2) ?></p>
                <a href="procesar_compra.php" class="btn-checkout">Procesar Compra</a>
                <?php $base_url = '/Sigmarket-jorge-carrito'; ?>
                <a href="<?php echo $base_url; ?>/pages/products_module/product_page.php" class="continue-shopping">Continuar comprando</a>
            </div>
        <?php endif; ?>
    </div>

    <footer class="footer">
        <?php show_footer(); ?>
    </footer>
</body>
</html>