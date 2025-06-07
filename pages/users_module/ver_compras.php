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

// Obtener las compras del usuario
$sqlCompras = "SELECT 
                c.idCompra, 
                c.fechaCompra, 
                c.total,
                COUNT(dc.idDetalle) as cantidad_productos
               FROM compra c
               LEFT JOIN detalle_compra dc ON c.idCompra = dc.idCompra
               WHERE c.idUser = ?
               GROUP BY c.idCompra
               ORDER BY c.fechaCompra DESC";
$stmt = $conn->prepare($sqlCompras);
$stmt->bind_param("i", $idUser);
$stmt->execute();
$compras = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Obtener los detalles de una compra específica si se solicita
$detalleCompra = [];
if (isset($_GET['idCompra'])) {
    $idCompra = $_GET['idCompra'];
    
    $sqlDetalle = "SELECT 
                    dc.cantidad,
                    dc.precioUnitario,
                    p.idProduct,
                    p.nameProduct,
                    (SELECT urlImage FROM imageproduct WHERE idProduct = p.idProduct LIMIT 1) AS producto_img
                   FROM detalle_compra dc
                   INNER JOIN products p ON dc.idProduct = p.idProduct
                   WHERE dc.idCompra = ?";
    $stmt = $conn->prepare($sqlDetalle);
    $stmt->bind_param("i", $idCompra);
    $stmt->execute();
    $detalleCompra = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
}

disconnectDB($conn);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIGMARKET - Mis Compras</title>
    <link rel="icon" href="<?php echo BASE_URL?>res/img/favicon_white.png">
    <link rel="stylesheet" href="<?php echo BASE_URL?>styles/global.css">
    <link rel="stylesheet" href="<?php echo BASE_URL?>styles/compras.css">
    <style>
        .detalle-compra {
            margin-top: 30px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            padding: 20px;
            display: <?= !empty($detalleCompra) ? 'block' : 'none' ?>;
        }
    </style>
</head>
<body>
    <header class="header">
        <?php show_header(); ?>
    </header>

    <div class="container">
        <div class="breadcrumb">
            <a href="<?php echo BASE_URL?>">Principal</a> > <a href="<?php echo BASE_URL?>pages/user_module/mi_cuenta.php">Mi cuenta</a> > <strong>Mis compras</strong>
        </div>
        
        <h1 class="page-title">MIS COMPRAS</h1>
        
        <div class="compras-list">
            <?php if (empty($compras)): ?>
                <div class="empty-state">
                    <img src="<?php echo BASE_URL?>res/img/cart_icon.png" alt="Carrito vacío">
                    <p>Aún no has realizado ninguna compra</p>
                    <a href="<?php echo BASE_URL?>pages/products_module/product_page.php" class="btn-primary">Explorar productos</a>
                </div>
            <?php else: ?>
                <?php foreach ($compras as $compra): ?>
                    <div class="compra-item">
                        <div class="compra-info">
                            <div class="compra-numero">Código de Compra #<?= $compra['idCompra'] ?></div>
                            <div class="compra-fecha"><?= date('d/m/Y', strtotime($compra['fechaCompra'])) ?></div>
                            <div class="compra-productos"><?= $compra['cantidad_productos'] ?> producto<?= $compra['cantidad_productos'] > 1 ? 's' : '' ?></div>
                        </div>
                        <div class="compra-total">Total de la Compra: $<?= number_format($compra['total'], 2) ?></div>
                        <a href="?idCompra=<?= $compra['idCompra'] ?>" class="ver-detalle">Ver detalle</a>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        
        <div class="detalle-compra">
            <?php if (!empty($detalleCompra)): ?>
                <h3 class="detalle-title">Detalle de la compra #<?= $_GET['idCompra'] ?></h3>
                
                <?php foreach ($detalleCompra as $producto): ?>
                    <div class="producto-detalle">
                        <?php if (!empty($producto['producto_img'])): ?>
                            <img src="<?php echo BASE_URL . htmlspecialchars($producto['producto_img']) ?>" 
                                 alt="<?= htmlspecialchars($producto['nameProduct']) ?>" 
                                 class="producto-image">
                        <?php else: ?>
                            <div class="producto-image" style="background: #f0f0f0; display: flex; align-items: center; justify-content: center;">
                                <span>Sin imagen</span>
                            </div>
                        <?php endif; ?>
                        
                        <div class="producto-info">
                            <h4 class="producto-nombre"><?= htmlspecialchars($producto['nameProduct']) ?></h4>
                            <p class="producto-cantidad">Cantidad: <?= $producto['cantidad'] ?></p>
                            <p class="producto-precio">Precio unitario: $<?= number_format($producto['precioUnitario'], 2) ?></p>
                            <p class="producto-subtotal">Subtotal: $<?= number_format($producto['cantidad'] * $producto['precioUnitario'], 2) ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
                
                <a href="ver_compras.php" class="volver-compras">← Volver a todas mis compras</a>
            <?php endif; ?>
        </div>
    </div>

    <footer class="footer">
        <?php show_footer(); ?>
    </footer>
</body>
</html>