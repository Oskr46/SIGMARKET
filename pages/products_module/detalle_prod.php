<?php 
include('../../components/header_footer.php');
include('../../components/conexion.php');
session_start();
$conn = connectDB();
mysqli_set_charset($conn, "utf8");

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Procesar agregar al carrito antes de mostrar la página
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_cart'])) {
    $idUser = $_POST['idUser'];
    $idProduct = $_POST['idProduct'];
    $quantity = $_POST['quantity'] ?? 1;
    $status = 1; // 1 = activo, 0 = inactivo
    
    // Verificar si el usuario existe
    $checkUser = $conn->prepare("SELECT idUser FROM user WHERE idUser = ?");
    $checkUser->bind_param("i", $idUser);
    $checkUser->execute();
    $checkUser->store_result();
    
    if ($checkUser->num_rows > 0) {
        // Verificar si el producto ya está en el carrito del usuario
        $checkCart = $conn->prepare("SELECT idCart, quantity FROM cart WHERE idUser = ? AND idProduct = ? AND status = 1");
        $checkCart->bind_param("ii", $idUser, $idProduct);
        $checkCart->execute();
        $cartResult = $checkCart->get_result();
        
        if ($cartResult->num_rows > 0) {
            // Actualizar cantidad si el producto ya está en el carrito
            $cartItem = $cartResult->fetch_assoc();
            $newQuantity = $cartItem['quantity'] + $quantity;
            $updateStmt = $conn->prepare("UPDATE cart SET quantity = ? WHERE idCart = ?");
            $updateStmt->bind_param("ii", $newQuantity, $cartItem['idCart']);
            
            if ($updateStmt->execute()) {
                $_SESSION['message'] = "Cantidad actualizada en el carrito correctamente!";
            } else {
                $_SESSION['error'] = "Error al actualizar el carrito: " . $conn->error;
            }
            $updateStmt->close();
        } else {
            // Insertar nuevo item en el carrito
            $insertStmt = $conn->prepare("INSERT INTO cart (idUser, idProduct, quantity, status) VALUES (?, ?, ?, ?)");
            $insertStmt->bind_param("iiii", $idUser, $idProduct, $quantity, $status);
            
            if ($insertStmt->execute()) {
                $_SESSION['message'] = "Producto agregado al carrito correctamente!";
            } else {
                $_SESSION['error'] = "Error al agregar el producto al carrito: " . $conn->error;
            }
            $insertStmt->close();
        }
        $checkCart->close();
    } else {
        $_SESSION['error'] = "Debes iniciar sesión para agregar productos al carrito";
    }
    $checkUser->close();
    
    // Redirigir para evitar reenvío del formulario
    header("Location: detalle_prod.php?id=" . $idProduct);
    exit();
}

// Obtener ID del producto desde la URL
$idProducto = $_GET['id'] ?? null;

if (!$idProducto) {
    header("Location: product_page.php");
    exit();
}

// Obtener información del producto
$producto_query = mysqli_prepare($conn, 
    "SELECT p.*, i.urlImage 
     FROM products p
     LEFT JOIN imageproduct i ON p.idProduct = i.idProduct
     WHERE p.idProduct = ?");
mysqli_stmt_bind_param($producto_query, "i", $idProducto);
mysqli_stmt_execute($producto_query);
$producto = mysqli_fetch_assoc(mysqli_stmt_get_result($producto_query));

if (!$producto) {
    header("Location: product_page.php");
    exit();
}

// Obtener imágenes adicionales del producto (si existen)
$imagenes_query = mysqli_query($conn, 
    "SELECT urlImage FROM imageproduct 
     WHERE idProduct = $idProducto AND urlImage != '{$producto['urlImage']}'");
$imagenes_adicionales = [];
while ($row = mysqli_fetch_assoc($imagenes_query)) {
    $imagenes_adicionales[] = $row['urlImage'];
}

// Obtener productos recomendados (ejemplo)
$recomendados_query = mysqli_query($conn, 
    "SELECT p.*, i.urlImage 
     FROM products p
     LEFT JOIN imageproduct i ON p.idProduct = i.idProduct
     WHERE p.categoryProduct = '{$producto['categoryProduct']}' 
     AND p.idProduct != {$producto['idProduct']}
     LIMIT 4");
$productos_recomendados = mysqli_fetch_all($recomendados_query, MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($producto['nameProduct']) ?> - SIGMARKET</title>
    <link rel="icon" href="<?php echo BASE_URL?>res/img/favicon_white.png"/>
    <link rel="stylesheet" href="<?php echo BASE_URL?>styles/detail_prod.css"/>
    <link rel="stylesheet" href="<?php echo BASE_URL?>styles/global.css"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"/>
    <script>
        function updateQuantity(change) {
            const input = document.querySelector('.quantity-input');
            let newValue = parseInt(input.value) + change;
            if (newValue < 1) newValue = 1;
            if (newValue > 10) newValue = 10;
            input.value = newValue;
        }
    </script>
</head>
<body>
    <header class="header">
        <?php show_header(); ?>
    </header>
    
    <main class="product-detail-container">        
        <?php if (isset($_SESSION['message'])): ?>
            <div class="alert success">
                <?= $_SESSION['message'] ?>
                <?php unset($_SESSION['message']); ?>
            </div>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert error">
                <?= $_SESSION['error'] ?>
                <?php unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>
        
        <div class="product-detail">
            <div class="product-images">
                <div class="main-image">
                    <?php if (!empty($producto['urlImage'])): ?>
                        <img src="<?php echo BASE_URL . htmlspecialchars($producto['urlImage']) ?>" 
                             alt="<?= htmlspecialchars($producto['nameProduct']) ?>">
                    <?php else: ?>
                        <div class="no-image">Sin imagen</div>
                    <?php endif; ?>
                </div>
                <?php if (!empty($imagenes_adicionales)): ?>
                <div class="thumbnail-gallery">
                    <?php foreach ($imagenes_adicionales as $imagen): ?>
                        <div class="thumbnail">
                            <img src="<?php echo BASE_URL . htmlspecialchars($imagen) ?>" 
                                 alt="Vista adicional de <?= htmlspecialchars($producto['nameProduct']) ?>">
                        </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
            
            <div class="product-info">
                <div class="product-header">
                    <h1><?= htmlspecialchars($producto['nameProduct']) ?></h1>
                    <div class="rating">
                        <span class="stars">⭐⭐⭐⭐⭐</span>
                        <span class="rating-value">4.5/5</span>
                    </div>
                </div>
                
                <div class="price-section">
                    <span class="price">$<?= number_format($producto['priceProduct'], 2) ?></span>
                </div>
                
                <div class="availability">
                    <span class="stock <?= $producto['quantityProduct'] > 0 ? 'in-stock' : 'out-of-stock' ?>">
                        <?= $producto['quantityProduct'] > 0 ? 'En stock' : 'Agotado' ?>
                    </span>
                </div>
                
                <div class="description">
                    <p><?= !empty($producto['descriptionProduct']) ? nl2br(htmlspecialchars($producto['descriptionProduct'])) : 'No hay descripción disponible.' ?></p>
                </div>
                
                <form method="post" action="">
                    <div class="quantity-section">
                        <h3>Cantidad</h3>
                        <div class="quantity-selector">
                            <button type="button" class="quantity-btn minus" onclick="updateQuantity(-1)">-</button>
                            <input type="number" name="quantity" class="quantity-input" value="1" min="1" max="10">
                            <button type="button" class="quantity-btn plus" onclick="updateQuantity(1)">+</button>
                        </div>
                    </div>
                    
                    <div class="actions">
                        <input type="hidden" name="idProduct" value="<?= $producto['idProduct'] ?>">
                        <input type="hidden" name="idUser" value="<?= $_SESSION['id'] ?? '' ?>">
                        <button type="submit" name="add_to_cart" class="add-to-cart" <?= !isset($_SESSION['id']) ? 'disabled title="Debes iniciar sesión para agregar al carrito"' : '' ?>>
                            <i class="fas fa-shopping-cart"></i> Agregar al carrito
                        </button>
                        <?php if (!isset($_SESSION['id'])): ?>
                            <p class="login-required">Inicia sesión para agregar productos al carrito</p>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
        </div>
        
        <div class="product-tabs">
            <button class="tab-button active" data-tab="details">Detalles del Producto</button>
            <button class="tab-button" data-tab="faqs">FAQs</button>
        </div>
        
        <div class="tab-content active" id="details">
            <p>Información detallada sobre el producto, materiales, cuidados, etc.</p>
        </div>
        
        <div class="tab-content" id="faqs">
            <p>Preguntas frecuentes sobre este producto.</p>
        </div>
        
        <?php if (!empty($productos_recomendados)): ?>
        <section class="recommendations">
            <h2>RECOMENDACIONES</h2>
            <div class="recommended-products">
                <?php foreach ($productos_recomendados as $recomendado): ?>
                <a href="detalle_prod.php?id=<?= $recomendado['idProduct'] ?>" class="recommended-product">
                    <div class="recommended-image-container">
                        <img src="<?php echo BASE_URL . htmlspecialchars($recomendado['urlImage']) ?>" 
                             alt="<?= htmlspecialchars($recomendado['nameProduct']) ?>" 
                             class="recommended-image">
                    </div>
                </a>
                <?php endforeach; ?>
            </div>
        </section>
        <?php endif; ?>
    </main>

    <footer class="footer">
        <?php show_footer(); ?>
    </footer>

</body>
</html>