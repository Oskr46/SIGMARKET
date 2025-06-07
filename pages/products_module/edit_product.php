<?php
// Iniciar la sesión
session_start();

include('../../components/conexion.php');

// Verificar permisos de administrador
if (!isset($_SESSION['tipo']) || $_SESSION['tipo'] != 1) {
    header("location: ../../index.php");
    exit();
}

// Obtener ID del producto a editar
$idProduct = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($idProduct <= 0) {
    header("location: prod_panel.php");
    exit();
}

// Obtener datos del producto
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

// Procesar el formulario de actualización
// Procesar el formulario de actualización
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Recoger y sanitizar datos
    $nameProduct = mysqli_real_escape_string($conn, $_POST['nameProduct']);
    $descriptionProduct = mysqli_real_escape_string($conn, $_POST['descriptionProduct']);
    $priceProduct = floatval($_POST['priceProduct']);
    $colorProduct = isset($_POST['colorProduct']) ? mysqli_real_escape_string($conn, $_POST['colorProduct']) : '';
    $labelProduct = isset($_POST['labelProduct']) ? mysqli_real_escape_string($conn, $_POST['labelProduct']) : '';
    $categoryProduct = isset($_POST['categoryProduct']) ? mysqli_real_escape_string($conn, $_POST['categoryProduct']) : '';
    $quantityProduct = intval($_POST['quantityProduct']);

    // Verificar si el nombre del producto ya existe para otro producto
    $check_product = "SELECT idProduct FROM products WHERE nameProduct = ? AND idProduct != ?";
    $stmt = mysqli_prepare($conn, $check_product);
    mysqli_stmt_bind_param($stmt, "si", $nameProduct, $idProduct);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_store_result($stmt);
    
    if (mysqli_stmt_num_rows($stmt) > 0) {
        // Nombre de producto ya existe, redirigir a página de error
        header("Location: product_exists_error.php");
        exit();
    }
    // Iniciar transacción
    mysqli_begin_transaction($conn);

    try {
        // Actualizar datos del producto
        $updateQuery = "UPDATE products SET 
                        nameProduct = ?,
                        descriptionProduct = ?,
                        priceProduct = ?,
                        colorProduct = ?,
                        labelProduct = ?,
                        categoryProduct = ?,
                        quantityProduct = ?
                        WHERE idProduct = ?";
        
        $stmt = mysqli_prepare($conn, $updateQuery);
        mysqli_stmt_bind_param($stmt, "ssdsssii", 
            $nameProduct,
            $descriptionProduct,
            $priceProduct,
            $colorProduct,
            $labelProduct,
            $categoryProduct,
            $quantityProduct,
            $idProduct);
        
        if (!mysqli_stmt_execute($stmt)) {
            throw new Exception("Error al actualizar el producto: " . mysqli_error($conn));
        }

        // Procesar nueva imagen si se subió
        if (!empty($_FILES['imagen']['name'])) {
            // Configuración de subida
            $upload_dir = '../../uploads/productos/';
            $max_file_size = 2 * 1024 * 1024; // 2MB
            $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
            $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];

            // Validar archivo
            $file_name = $_FILES['imagen']['name'];
            $file_tmp = $_FILES['imagen']['tmp_name'];
            $file_size = $_FILES['imagen']['size'];
            $file_error = $_FILES['imagen']['error'];
            $file_type = $_FILES['imagen']['type'];

            if ($file_error !== UPLOAD_ERR_OK) {
                throw new Exception("Error al subir el archivo: Código $file_error");
            }

            if ($file_size > $max_file_size) {
                throw new Exception("Error: El archivo es demasiado grande. Tamaño máximo permitido: 2MB");
            }

            $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
            if (!in_array($file_type, $allowed_types) || !in_array($file_ext, $allowed_extensions)) {
                throw new Exception("Error: Tipo de archivo no permitido. Formatos aceptados: JPG, PNG, GIF");
            }

            // Generar nombre único
            $unique_name = uniqid('product_', true) . '.' . $file_ext;
            $destination = $upload_dir . $unique_name;
            $relative_url = 'uploads/productos/' . $unique_name;

            // Mover archivo
            if (!move_uploaded_file($file_tmp, $destination)) {
                throw new Exception("Error al mover el archivo subido");
            }

            // Actualizar imagen en la base de datos
            if (!empty($producto['urlImage'])) {
                // Actualizar imagen existente
                $updateImageQuery = "UPDATE imageproduct SET urlImage = ? WHERE idProduct = ?";
                $stmt = mysqli_prepare($conn, $updateImageQuery);
                mysqli_stmt_bind_param($stmt, "si", $relative_url, $idProduct);
            } else {
                // Insertar nueva imagen
                $insertImageQuery = "INSERT INTO imageproduct (idProduct, urlImage) VALUES (?, ?)";
                $stmt = mysqli_prepare($conn, $insertImageQuery);
                mysqli_stmt_bind_param($stmt, "is", $idProduct, $relative_url);
            }

            if (!mysqli_stmt_execute($stmt)) {
                throw new Exception("Error al actualizar la imagen: " . mysqli_error($conn));
            }

            // Eliminar imagen anterior si existe
            if (!empty($producto['urlImage'])) {
                $old_image_path = '../../' . $producto['urlImage'];
                if (file_exists($old_image_path)) {
                    unlink($old_image_path);
                }
            }
        }

        // Confirmar transacción
        mysqli_commit($conn);
        header("Location: prod_panel.php?success=1");
        exit();
    } catch (Exception $e) {
        mysqli_rollback($conn);
        $error = $e->getMessage();
    }
}

$categories = [];
$categoryQuery = "SELECT idCategory, nameCategory FROM category ORDER BY nameCategory";
$categoryResult = mysqli_query($conn, $categoryQuery);

if ($categoryResult) {
    while ($row = mysqli_fetch_assoc($categoryResult)) {
        $categories[$row['idCategory']] = $row['nameCategory'];
    }
}

include('../../components/header_footer.php');
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Producto - SIGMARKET</title>
    <link rel="icon" href="<?php echo BASE_URL;?>/res/img/favicon_white.png"/>
    <link rel="stylesheet" href="<?php echo BASE_URL;?>/styles/global.css"/>
    <link rel="stylesheet" href="<?php echo BASE_URL;?>/styles/panels.css"/>
</head>
<body>
    <header class="header">
        <?php show_header(); ?>
    </header>
    
    <div class="breadcrumb">
        <span>Inicio > Centro de Cuentas > Panel de Administración > Productos > Editar</span>
    </div>

    <div class="account-container">
        <aside class="account-sidebar">
            <h3>Menú de Administración</h3>
            <ul>
                <li><a class="module" href="../panels/admin_panel.php">Dashboard</a></li>
                <li><a class="module" href="prod_panel.php">Productos</a></li>
                <li><a class="module" href="../users_module/users_panel.php">Usuarios</a></li>
                <li><a class="module" href="../category/category_panel.php">Categorías</a></li>
                <li><a class="module" href="../panels/compras_panel.php">Ver Historial de Compras</a></li>
            </ul>
        </aside>

        <main class="account-content">
            <div class="form-header">
                <h1>EDITAR PRODUCTO</h1>
                <a href="prod_panel.php" class="btn btn-secondary">← Volver</a>
            </div>
            
            <?php if (isset($error)): ?>
                <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <form method="POST" enctype="multipart/form-data" class="product-form">
                <div class="form-row">
                    <div class="form-group">
                        <label for="nameProduct">Nombre del Producto</label>
                        <input type="text" id="nameProduct" name="nameProduct" 
                               value="<?php echo htmlspecialchars($producto['nameProduct']); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="priceProduct">Precio ($)</label>
                        <input type="number" id="priceProduct" name="priceProduct" step="0.01" min="0"
                               value="<?php echo htmlspecialchars($producto['priceProduct']); ?>" required>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="quantityProduct">Cantidad Disponible</label>
                        <input type="number" id="quantityProduct" name="quantityProduct" min="0"
                               value="<?php echo htmlspecialchars($producto['quantityProduct']); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="colorProduct">Color (Código HEX)</label>
                        <input type="color" id="colorProduct" name="colorProduct" 
                               value="<?php echo !empty($producto['colorProduct']) ? htmlspecialchars($producto['colorProduct']) : '#ffffff'; ?>">
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="descriptionProduct">Descripción</label>
                    <textarea id="descriptionProduct" name="descriptionProduct" rows="4"><?php echo htmlspecialchars($producto['descriptionProduct']); ?></textarea>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="labelProduct">Etiqueta</label>
                        <input type="text" id="labelProduct" name="labelProduct" 
                               value="<?php echo htmlspecialchars($producto['labelProduct']); ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="categoryProduct" class="form-label required-field">Categoría</label>
                        <select id="categoryProduct" name="categoryProduct" class="form-control" required>
                            <option value="">Seleccione una categoría</option>
                            <?php foreach ($categories as $id => $name): ?>
                                <option value="<?php echo $id; ?>" <?php echo ($producto['categoryProduct'] == $id) ? 'selected': ''?>><?php echo htmlspecialchars($name);?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="imagen">Imagen del Producto</label>
                    <input type="file" id="imagen" name="imagen" accept="image/*">
                    
                    <?php if (!empty($producto['urlImage'])): ?>
                        <div class="current-image">
                            <p>Imagen actual:</p>
                            <img src="<?php echo BASE_URL . $producto['urlImage']; ?>" 
                                 alt="Imagen actual del producto" style="max-width: 200px; margin-top: 10px;">
                        </div>
                    <?php endif; ?>
                </div>
                
                <button type="submit" class="btn btn-primary">Actualizar Producto</button>
            </form>
        </main>
    </div>

    <footer class="footer">
        <?php show_footer(); ?>
    </footer>
</body>
</html>