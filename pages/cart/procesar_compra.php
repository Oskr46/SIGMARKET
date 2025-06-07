<?php
session_start();
include('../../components/conexion.php');
include('../../components/header_footer.php');

// Verificar si el usuario está logueado
if (!isset($_SESSION['id'])) {
    header("Location: ../../pages/login_module/login_page.php");
    exit();
}

$idUser = $_SESSION['id'];
$conn = connectDB();
mysqli_set_charset($conn, "utf8");

// Obtener los productos del carrito activo del usuario (status = 1)
$sql = "SELECT 
            c.idCart,
            c.idProduct,
            c.quantity,
            p.priceProduct,
            p.nameProduct,
            p.descriptionProduct,
            (SELECT urlImage FROM imageproduct WHERE idProduct = p.idProduct LIMIT 1) AS producto_img
        FROM cart c
        INNER JOIN products p ON c.idProduct = p.idProduct
        WHERE c.idUser = ? AND c.status = 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $idUser);
$stmt->execute();
$cartItems = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

$total = 0;
$fechaCompra = date('Y-m-d H:i:s'); // Fecha y hora actual

if (!empty($cartItems)) {
    // Calcular el total
    foreach ($cartItems as $item) {
        $total += $item['quantity'] * $item['priceProduct'];
    }
    
    // Obtener el idCart (todos los items tienen el mismo idCart)
    $idCart = $cartItems[0]['idCart'];
    
    // Insertar la compra en la tabla compra
    $insertCompra = "INSERT INTO compra (idUser, idCart, fechaCompra, total) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($insertCompra);
    $stmt->bind_param("iisd", $idUser, $idCart, $fechaCompra, $total);
    
    if ($stmt->execute()) {
        $idCompra = $stmt->insert_id; // Obtenemos el ID de la compra recién insertada
        
        // Insertar los detalles de la compra (productos comprados)
        foreach ($cartItems as $item) {
            $insertDetalle = "INSERT INTO detalle_compra (idCompra, idProduct, cantidad, precioUnitario) 
                             VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($insertDetalle);
            $stmt->bind_param("iiid", $idCompra, $item['idProduct'], $item['quantity'], $item['priceProduct']);
            $stmt->execute();
            $stmt->close();
        }
        
        // Actualizar el estado del carrito a "comprado" (status = 0)
        $updateCart = "UPDATE cart SET status = 0 WHERE idCart = ?";
        $stmt = $conn->prepare($updateCart);
        $stmt->bind_param("i", $idCart);
        $stmt->execute();
        $stmt->close();
    } else {
        // Manejar error en la inserción
        die("Error al procesar la compra: " . $conn->error);
    }
}

disconnectDB($conn);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?php echo BASE_URL?>styles/global_login.css">
    <link rel="icon" href="<?php echo BASE_URL?>res/img/favicon_white.png" type="image/x-icon">
    <link rel="stylesheet" href="<?php echo BASE_URL?>styles/global.css"/>
    <link rel="stylesheet" href="<?php echo BASE_URL?>styles/procesar_compra.css">
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
                <p><strong>Fecha de compra:</strong> <?= date('d/m/Y H:i', strtotime($fechaCompra)) ?></p>
                <p><strong>Total de la compra:</strong> $<?= number_format($total, 2) ?></p>
                <?php if(isset($idCompra)): ?>
                    <p><strong>Número de compra:</strong> #<?= $idCompra ?></p>
                <?php endif; ?>
            </div>
            
            <h2>Productos comprados:</h2>
            <div class="productos-comprados">
                <?php if (empty($cartItems)): ?>
                    <p>No hay productos en tu carrito.</p>
                <?php else: ?>
                    <?php foreach ($cartItems as $item): ?>
                        <div class="producto-card">
                            <?php if (!empty($item['producto_img'])): ?>
                                <img src="<?php echo BASE_URL;?><?= htmlspecialchars($item['producto_img']) ?>" 
                                     alt="<?= htmlspecialchars($item['nameProduct']) ?>" 
                                     class="producto-image">
                            <?php else: ?>
                                <div class="producto-image" style="background: #f0f0f0; display: flex; align-items: center; justify-content: center;">
                                    <span>Sin imagen</span>
                                </div>
                            <?php endif; ?>
                            <div class="producto-info">
                                <p><strong><?= htmlspecialchars($item['nameProduct']) ?></strong></p>
                                <p>Cantidad: <?= $item['quantity'] ?></p>
                                <p>Precio unitario: $<?= number_format($item['priceProduct'], 2) ?></p>
                                <p>Subtotal: $<?= number_format($item['quantity'] * $item['priceProduct'], 2) ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            
            <div class="acciones-finales">
                <a href="<?php echo BASE_URL; ?>pages/products_module/product_page.php" class="btn-volver">Volver a Productos</a>
                <?php if(!empty($cartItems)): ?>
                    <a href="<?php echo BASE_URL; ?>pages/users_module/ver_compras.php" class="btn-volver">Ver mis compras</a>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <footer class="footer">
        <?php show_footer(); ?>
    </footer>
</body>
</html>