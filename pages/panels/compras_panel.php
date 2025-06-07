<?php
session_start();
include('../../components/conexion.php');
include('../../components/header_footer.php');

// Verificar si es administrador
if (!isset($_SESSION['tipo']) || $_SESSION['tipo'] != 1) {
    header("Location: ../../index.php");
    exit();
}

$conn = connectDB();
mysqli_set_charset($conn, "utf8");

// Obtener todas las compras
$sqlCompras = "SELECT 
                c.idCompra, 
                c.fechaCompra, 
                c.total,
                u.nameUser as nombre_usuario,
                u.sNameUser as apellido_usuario,
                u.emailUser as email_usuario,
                COUNT(dc.idDetalle) as cantidad_productos
               FROM compra c
               LEFT JOIN detalle_compra dc ON c.idCompra = dc.idCompra
               LEFT JOIN user u ON c.idUser = u.idUser
               GROUP BY c.idCompra
               ORDER BY c.fechaCompra DESC";
$compras = mysqli_query($conn, $sqlCompras)->fetch_all(MYSQLI_ASSOC);

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

// Eliminar una compra si se solicita
if (isset($_GET['eliminar']) && isset($_GET['idCompra'])) {
    $idCompra = $_GET['idCompra'];
    
    // Primero eliminamos los detalles
    $sqlEliminarDetalles = "DELETE FROM detalle_compra WHERE idCompra = ?";
    $stmt = $conn->prepare($sqlEliminarDetalles);
    $stmt->bind_param("i", $idCompra);
    $stmt->execute();
    $stmt->close();
    
    // Luego eliminamos la compra
    $sqlEliminarCompra = "DELETE FROM compra WHERE idCompra = ?";
    $stmt = $conn->prepare($sqlEliminarCompra);
    $stmt->bind_param("i", $idCompra);
    
    if ($stmt->execute()) {
        header("Location: compras_panel.php?success=1");
        exit();
    } else {
        header("Location: compras_panel.php?error=1");
        exit();
    }
}

disconnectDB($conn);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIGMARKET - Administrar Compras</title>
    <link rel="icon" href="<?php echo BASE_URL?>res/img/favicon_white.png">
    <link rel="stylesheet" href="<?php echo BASE_URL?>styles/global.css">
    <link rel="stylesheet" href="<?php echo BASE_URL?>styles/panels.css">
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
        
        .compra-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px;
            border-bottom: 1px solid #eee;
        }
        
        .compra-info {
            flex: 1;
        }
        
        .compra-usuario {
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .compra-email {
            color: #666;
            font-size: 0.9em;
        }
        
        .compra-acciones {
            display: flex;
            gap: 10px;
        }
        
        .producto-detalle {
            display: flex;
            gap: 20px;
            margin-bottom: 20px;
            padding-bottom: 20px;
            border-bottom: 1px solid #eee;
        }
        
        .producto-image {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 4px;
        }
        
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
            position: relative;
        }
        
        .alert-success {
            background-color: #dff0d8;
            color: #3c763d;
            border: 1px solid #d6e9c6;
        }
        
        .alert-error {
            background-color: #f2dede;
            color: #a94442;
            border: 1px solid #ebccd1;
        }
        
        .close-btn {
            position: absolute;
            right: 15px;
            top: 15px;
            cursor: pointer;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <header class="header">
        <?php show_header(); ?>
    </header>

    <div class="breadcrumb">
        <span>Inicio > Centro de Cuentas > Panel de Administración > Historial de Compras</span>
    </div>

    <div class="account-container">
        <aside class="account-sidebar">
            <h3>Menú de Administración</h3>
            <ul>
                <li><a class="module" href="../panels/admin_panel.php">Dashboard</a></li>
                <li><a class="module" href="../products_module/prod_panel.php">Productos</a></li>
                <li><a class="module" href="../users_module/users_panel.php">Usuarios</a></li>
                <li><a class="module" href="../category/category_panel.php">Categorías</a></li>
                <li class="active">Historial de Compras</li>
            </ul>
        </aside>

        <main class="account-content">
            <div class="products-header">
                <h1>HISTORIAL DE COMPRAS</h1>
            </div>
            
            <!-- Mensajes de éxito/error -->
            <?php if(isset($_GET['success']) && $_GET['success'] == 1): ?>
                <div class="alert alert-success">
                    Compra eliminada exitosamente
                    <span class="close-btn" onclick="this.parentElement.style.display='none'">&times;</span>
                </div>
            <?php endif; ?>
            
            <?php if(isset($_GET['error']) && $_GET['error'] == 1): ?>
                <div class="alert alert-error">
                    Error al eliminar la compra
                    <span class="close-btn" onclick="this.parentElement.style.display='none'">&times;</span>
                </div>
            <?php endif; ?>
            
            <section class="account-section">
                <?php if (empty($compras)): ?>
                    <div class="no-products">
                        <p>No hay compras registradas en el sistema</p>
                    </div>
                <?php else: ?>
                    <div class="compras-list">
                        <?php foreach ($compras as $compra): ?>
                            <div class="compra-item">
                                <div class="compra-info">
                                    <div class="compra-usuario">
                                        <?= htmlspecialchars($compra['nombre_usuario'] . ' ' . $compra['apellido_usuario']) ?>
                                    </div>
                                    <div class="compra-email">
                                        <?= htmlspecialchars($compra['email_usuario']) ?>
                                    </div>
                                    <div class="compra-numero">Compra #<?= $compra['idCompra'] ?></div>
                                    <div class="compra-fecha"><?= date('d/m/Y H:i', strtotime($compra['fechaCompra'])) ?></div>
                                    <div class="compra-productos"><?= $compra['cantidad_productos'] ?> producto<?= $compra['cantidad_productos'] > 1 ? 's' : '' ?></div>
                                </div>
                                <div class="compra-total">Total: $<?= number_format($compra['total'], 2) ?></div>
                                <div class="compra-acciones">
                                    <a href="?idCompra=<?= $compra['idCompra'] ?>" class="edit-btn">Ver</a>
                                    <a href="?eliminar=1&idCompra=<?= $compra['idCompra'] ?>" class="delete-btn" onclick="return confirm('¿Estás seguro de eliminar esta compra? Esta acción no se puede deshacer.')">Eliminar</a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </section>
            
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
                    
                    <a href="compras_panel.php" class="volver-compras">← Volver al historial de compras</a>
                <?php endif; ?>
            </div>
            
            <div class="logout-section">
                <a class="close_session" href="../login_module/close_session.php">Cerrar sesión</a>
            </div>
        </main>
    </div>

    <footer class="footer">
        <?php show_footer(); ?>
    </footer>
</body>
</html>