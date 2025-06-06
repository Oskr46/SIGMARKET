<?php
// Conexión a la base de datos
include('../components/conexion.php');

// Procesar agregar al carrito
if (isset($_POST['add_to_cart'])) {
    $idUser = $_POST['idUser'];
    $idProduct = $_POST['idProduct'];
    
    // Verificar si el usuario existe
    $checkUser = $conn->prepare("SELECT idUser FROM user WHERE idUser = ?");
    $checkUser->bind_param("i", $idUser);
    $checkUser->execute();
    $checkUser->store_result();
    
    if ($checkUser->num_rows > 0) {
        // Insertar en el carrito
        $stmt = $conn->prepare("INSERT INTO cart (idUser, idProduct) VALUES (?, ?)");
        $stmt->bind_param("ii", $idUser, $idProduct);
        
        if ($stmt->execute()) {
            $message = "Producto agregado al carrito correctamente!";
        } else {
            $error = "Error al agregar el producto al carrito: " . $conn->error;
        }
        $stmt->close();
    } else {
        $error = "El usuario no existe";
    }
    $checkUser->close();
}

$sql = "SELECT * FROM products";
$result = $conn->query($sql);
$products = [];
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $products[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIGMarket - Tienda en Línea</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f5f5f5;
            color: #333;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        
        header {
            background-color: #2c3e50;
            color: white;
            padding: 20px 0;
            text-align: center;
            margin-bottom: 30px;
        }
        
        h1 {
            margin: 0;
        }
        
        .products-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 20px;
        }
        
        .product-card {
            background-color: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }
        
        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        .product-image {
            height: 200px;
            background-color: #eee;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #777;
        }
        
        .product-info {
            padding: 15px;
        }
        
        .product-title {
            font-size: 1.2em;
            margin: 0 0 10px;
        }
        
        .product-price {
            font-weight: bold;
            color: #e74c3c;
            font-size: 1.3em;
            margin: 10px 0;
        }
        
        .product-description {
            color: #666;
            font-size: 0.9em;
            margin-bottom: 15px;
        }
        
        .product-color {
            display: inline-block;
            width: 20px;
            height: 20px;
            border-radius: 50%;
            margin-right: 5px;
            vertical-align: middle;
        }
        
        .add-to-cart {
            background-color: #3498db;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 4px;
            cursor: pointer;
            width: 100%;
            font-weight: bold;
            transition: background-color 0.3s;
        }
        
        .add-to-cart:hover {
            background-color: #2980b9;
        }
        
        .message {
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 4px;
            text-align: center;
        }
        
        .success {
            background-color: #d4edda;
            color: #155724;
        }
        
        .error {
            background-color: #f8d7da;
            color: #721c24;
        }
        
        .user-form {
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 30px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        
        .form-group {
            margin-bottom: 15px;
        }
        
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        
        input[type="number"] {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }
    </style>
</head>
<body>
    <header>
        <div class="container">
            <h1>SIGMarket</h1>
        </div>
    </header>
    
    <div class="container">
        <?php if (isset($message)): ?>
            <div class="message success"><?php echo $message; ?></div>
        <?php endif; ?>
        
        <?php if (isset($error)): ?>
            <div class="message error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <div class="user-form">
            <form method="post">
                <div class="form-group">
                    <label for="idUser">ID de Usuario:</label>
                    <input type="number" id="idUser" name="idUser" required>
                </div>
            </form>
        </div>
        
        <div class="products-grid">
            <?php foreach ($products as $product): ?>
                <div class="product-card">
                    <div class="product-image">
                        <?php 
                        // Obtener imagen del producto
                        $imgQuery = $conn->prepare("SELECT urlImage FROM imageproduct WHERE idProduct = ?");
                        $imgQuery->bind_param("i", $product['idProduct']);
                        $imgQuery->execute();
                        $imgResult = $imgQuery->get_result();
                        
                        if ($imgResult->num_rows > 0) {
                            $imgData = $imgResult->fetch_assoc();
                            echo '<img src="'.$imgData['urlImage'].'" alt="'.$product['nameProduct'].'" style="max-width:100%; max-height:200px;">';
                        } else {
                            echo 'Imagen no disponible';
                        }
                        $imgQuery->close();
                        ?>
                    </div>
                    <div class="product-info">
                        <h3 class="product-title"><?php echo htmlspecialchars($product['nameProduct']); ?></h3>
                        <div class="product-price">$<?php echo number_format($product['priceProduct'], 0, ',', '.'); ?></div>
                        <div class="product-description"><?php echo htmlspecialchars($product['descriptionProduct']); ?></div>
                        <div>
                            <span>Color: </span>
                            <span class="product-color" style="background-color: <?php echo $product['colorProduct']; ?>"></span>
                        </div>
                        <div>Categoría: <?php echo htmlspecialchars($product['categoryProduct']); ?></div>
                        <div>Disponibles: <?php echo $product['quantityProduct']; ?></div>
                        
                        <form method="post" style="margin-top: 15px;">
                            <input type="hidden" name="idProduct" value="<?php echo $product['idProduct']; ?>">
                            <input type="hidden" name="idUser" id="hiddenUserId">
                            <button type="submit" name="add_to_cart" class="add-to-cart">Agregar al carrito</button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    
    <script>
        // Actualizar el campo oculto de ID de usuario cuando cambie el campo visible
        document.getElementById('idUser').addEventListener('change', function() {
            document.getElementById('hiddenUserId').value = this.value;
        });
    </script>
</body>
</html>
<?php
$conn->close();
?>

