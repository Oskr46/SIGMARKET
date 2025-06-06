<?php
session_start();
include('../../components/conexion.php');
include('../../components/header_footer.php');

if (!isset($_SESSION['id'])) {
    header("Location: ../login_module/login_page.php");
    exit();
}

$idUser = $_SESSION['id'];
$conn = connectDB();
mysqli_set_charset($conn, "utf8");

// Procesar actualización de cantidad
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_quantity'])) {
    $idCart = $_POST['idCart'];
    $newQuantity = (int)$_POST['quantity'];
    
    // Validar que la cantidad sea al menos 1
    if ($newQuantity < 1) {
        $newQuantity = 1;
    }
    
    $updateSql = "UPDATE cart SET quantity = ? WHERE idCart = ? AND idUser = ?";
    $stmt = $conn->prepare($updateSql);
    $stmt->bind_param("iii", $newQuantity, $idCart, $idUser);
    $stmt->execute();
    $stmt->close();
    
    // Redirigir para evitar reenvío del formulario
    header("Location: ".$_SERVER['PHP_SELF']);
    exit();
}

// Obtener productos del carrito
$sql = "SELECT 
            c.idCart,
            p.idProduct,
            p.nameProduct,
            p.priceProduct,
            c.quantity,
            (SELECT urlImage FROM imageproduct WHERE idProduct = p.idProduct LIMIT 1) AS producto_img
        FROM cart c
        INNER JOIN products p ON c.idProduct = p.idProduct
        WHERE c.idUser = ? AND c.status = 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $idUser);
$stmt->execute();
$cartItems = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Calcular totales
$subtotal = 0;
$delivery = 15.00; // Costo de envío fijo
foreach ($cartItems as $item) {
    $subtotal += $item['quantity'] * $item['priceProduct'];
}
$total = $subtotal + $delivery;

disconnectDB($conn);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIGMARKET - Carrito</title>
    <link rel="icon" href="<?php echo BASE_URL?>res/img/favicon_white.png">
    <link rel="stylesheet" href="<?php echo BASE_URL?>styles/global.css">
    <link rel="stylesheet" href="<?php echo BASE_URL?>styles/view_cart.css">
</head>
<body>
    <header class="header">
        <?php show_header(); ?>
    </header>

    <div class="container">
        <div class="breadcrumb">
            <a href="<?php echo BASE_URL?>">Principal</a> > <strong>Carrito</strong>
        </div>
        
        <h1 class="cart-title">TU CARRITO</h1>
        
        <div class="cart-content">
            <div class="cart-items">
                <?php if (empty($cartItems)): ?>
                    <p>No tienes productos en tu carrito.</p>
                <?php else: ?>
                    <?php foreach ($cartItems as $item): ?>
                        <div class="product-card">
                            <?php if (!empty($item['producto_img'])): ?>
                                <img src="<?php echo BASE_URL . htmlspecialchars($item['producto_img']) ?>" 
                                     alt="<?= htmlspecialchars($item['nameProduct']) ?>" 
                                     class="product-image">
                            <?php else: ?>
                                <div class="product-image" style="background: #f0f0f0; display: flex; align-items: center; justify-content: center;">
                                    <span>Sin imagen</span>
                                </div>
                            <?php endif; ?>
                            
                            <div class="product-details">
                                <h3 class="product-name"><?= htmlspecialchars($item['nameProduct']) ?></h3>
                                <p class="product-price">Precio unitario: $<?= number_format($item['priceProduct'], 2) ?></p>
                                <p class="product-price">Cantidad: <?= htmlspecialchars($item['quantity']) ?></p>
                                
                                <form method="POST" action="" class="quantity-form">
                                    <input type="hidden" name="idCart" value="<?= $item['idCart'] ?>">
                                    <div class="quantity-control">
                                        <button type="button" class="quantity-btn minus" data-id="<?= $item['idCart'] ?>">-</button>
                                        <input type="number" name="quantity" class="quantity-input" 
                                               value="<?= htmlspecialchars($item['quantity']) ?>" min="1">
                                        <button type="button" class="quantity-btn plus" data-id="<?= $item['idCart'] ?>">+</button>
                                        <button type="submit" name="update_quantity" class="update-btn">Actualizar</button>
                                    </div>
                                </form>
                                
                                <p class="product-attributes">Precio Total: $<?= number_format($item['quantity'] * $item['priceProduct'], 2) ?></p>
                                
                                <form method="POST" action="borrar_producto_carrito.php" style="margin-top: 10px;">
                                    <input type="hidden" name="idCart" value="<?= $item['idCart'] ?>">
                                    <input type="hidden" name="idProduct" value="<?= $item['idProduct'] ?>">
                                    <button type="submit" style="background: none; border: none; color: #666; cursor: pointer; font-size: 14px;">
                                        Eliminar
                                    </button>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
                
                <a href="#" class="wishlist-link">Wishlist</a>
            </div>
            
            <div class="cart-summary">
                <h3 class="summary-title">Resumen del pedido</h3>
                
                <div class="summary-row">
                    <span>Subtotal</span>
                    <span>$<?= number_format($subtotal, 2) ?></span>
                </div>
                
                <div class="summary-row">
                    <span>Delivery gratis</span>
                    <span>$<?= number_format($delivery, 2) ?></span>
                </div>
                
                <div class="summary-total">
                    <span>Total</span>
                    <span>$<?= number_format($total, 2) ?></span>
                </div>
                
                <a href="procesar_compra.php" class="checkout-btn">Comprar →</a>
            </div>
        </div>
    </div>

    <footer class="footer">
        <?php show_footer(); ?>
    </footer>

    <script>
        // Manejar los botones de incremento/decremento
        document.querySelectorAll('.quantity-btn').forEach(button => {
            button.addEventListener('click', function() {
                const form = this.closest('.quantity-form');
                const input = form.querySelector('.quantity-input');
                let value = parseInt(input.value);
                
                if (this.classList.contains('plus')) {
                    value++;
                } else if (this.classList.contains('minus')) {
                    value = Math.max(1, value - 1);
                }
                
                input.value = value;
            });
        });
        
        // Actualizar automáticamente al cambiar la cantidad (opcional)
        document.querySelectorAll('.quantity-input').forEach(input => {
            input.addEventListener('change', function() {
                if (this.value < 1) {
                    this.value = 1;
                }
            });
        });
    </script>
</body>
</html>