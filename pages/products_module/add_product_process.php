<?php
// Iniciar la sesión
session_start();

// Verificar si el usuario es administrador (tipo 1)
if (!isset($_SESSION['tipo']) || $_SESSION['tipo'] != 1) {
    header("location: ../../../index.php");
    exit();
}

include('../../components/conexion.php');

// Configuración de la subida de archivos
$upload_dir = '../../uploads/productos/';
$max_file_size = 2 * 1024 * 1024; // 2MB
$allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
$allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];

// Crear directorio si no existe
if (!file_exists($upload_dir)) {
    mkdir($upload_dir, 0755, true);
}

// Validar y procesar el formulario
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Validar campos requeridos
    $required_fields = ['nameProduct', 'descriptionProduct', 'priceProduct', 'imagen', 'categoryProduct'];
    foreach ($required_fields as $field) {
        if (empty($_POST[$field]) && $field != 'imagen') {
            die("Error: El campo $field es requerido");
        }
        if ($field == 'imagen' && empty($_FILES['imagen']['name'])) {
            die("Error: La imagen es requerida");
        }
    }

    // Procesar la imagen
    $file_name = $_FILES['imagen']['name'];
    $file_tmp = $_FILES['imagen']['tmp_name'];
    $file_size = $_FILES['imagen']['size'];
    $file_error = $_FILES['imagen']['error'];
    $file_type = $_FILES['imagen']['type'];

    // Validar errores de subida
    if ($file_error !== UPLOAD_ERR_OK) {
        die("Error al subir el archivo: Código $file_error");
    }

    // Validar tamaño del archivo
    if ($file_size > $max_file_size) {
        die("Error: El archivo es demasiado grande. Tamaño máximo permitido: 2MB");
    }

    // Validar tipo de archivo
    $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
    if (!in_array($file_type, $allowed_types) || !in_array($file_ext, $allowed_extensions)) {
        die("Error: Tipo de archivo no permitido. Formatos aceptados: JPG, PNG, GIF");
    }

    // Generar nombre único para el archivo
    $unique_name = uniqid('product_', true) . '.' . $file_ext;
    $destination = $upload_dir . $unique_name;
    $relative_url = 'uploads/productos/' . $unique_name;

    $conn = connectDB();
    
    // Verificar si el producto ya existe
    $nameProduct = mysqli_real_escape_string($conn, $_POST['nameProduct']);
    $checkQuery = "SELECT idProduct FROM products WHERE nameProduct = ?";
    $checkStmt = mysqli_prepare($conn, $checkQuery);
    mysqli_stmt_bind_param($checkStmt, "s", $nameProduct);
    mysqli_stmt_execute($checkStmt);
    mysqli_stmt_store_result($checkStmt);
    
    if (mysqli_stmt_num_rows($checkStmt) > 0) {
        // Producto ya existe, redirigir a página de error
        header("Location: product_exists_error.php?name=" . urlencode($nameProduct));
        exit();
    }
    
    // Verificar que la categoría exista
    $categoryId = intval($_POST['categoryProduct']);
    $checkCategoryQuery = "SELECT idCategory FROM category WHERE idCategory = ?";
    $checkCategoryStmt = mysqli_prepare($conn, $checkCategoryQuery);
    mysqli_stmt_bind_param($checkCategoryStmt, "i", $categoryId);
    mysqli_stmt_execute($checkCategoryStmt);
    mysqli_stmt_store_result($checkCategoryStmt);
    
    if (mysqli_stmt_num_rows($checkCategoryStmt) == 0) {
        die("Error: La categoría seleccionada no es válida");
    }
    
    // Mover el archivo subido
    if (move_uploaded_file($file_tmp, $destination)) {
        // Sanitizar datos del formulario
        $descriptionProduct = mysqli_real_escape_string($conn, $_POST['descriptionProduct']);
        $priceProduct = floatval($_POST['priceProduct']);
        $colorProduct = isset($_POST['colorProduct']) ? mysqli_real_escape_string($conn, $_POST['colorProduct']) : '';
        $labelProduct = isset($_POST['labelProduct']) ? mysqli_real_escape_string($conn, $_POST['labelProduct']) : '';

        // Iniciar transacción para asegurar la integridad de los datos
        mysqli_begin_transaction($conn);

        try {
            // Insertar en la tabla products
            $sql = "INSERT INTO products (nameProduct, descriptionProduct, priceProduct, colorProduct, labelProduct, categoryProduct) 
                    VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = mysqli_prepare($conn, $sql);
            
            if (!$stmt) {
                throw new Exception("Error en la preparación de la consulta: " . mysqli_error($conn));
            }
            
            // Cambiado a "ssdssi" (el último parámetro es entero para categoryProduct)
            mysqli_stmt_bind_param($stmt, "ssdssi", 
                $nameProduct, 
                $descriptionProduct, 
                $priceProduct, 
                $colorProduct, 
                $labelProduct,
                $categoryId);
            
            if (!mysqli_stmt_execute($stmt)) {
                throw new Exception("Error al guardar en la base de datos: " . mysqli_error($conn));
            }

            // Obtener el ID del producto recién insertado
            $productId = mysqli_insert_id($conn);

            // Insertar en la tabla imageproduct
            $sql_image = "INSERT INTO imageproduct (idProduct, urlImage) VALUES (?, ?)";
            $stmt_image = mysqli_prepare($conn, $sql_image);
            
            if (!$stmt_image) {
                throw new Exception("Error en la preparación de la consulta de imagen: " . mysqli_error($conn));
            }
            
            mysqli_stmt_bind_param($stmt_image, "is", $productId, $relative_url);
            
            if (!mysqli_stmt_execute($stmt_image)) {
                throw new Exception("Error al guardar la imagen en la base de datos: " . mysqli_error($conn));
            }

            // Confirmar la transacción si todo fue exitoso
            mysqli_commit($conn);

            // Redirigir con mensaje de éxito
            header("Location: prod_panel.php?success=1");
            exit();

        } catch (Exception $e) {
            // Revertir la transacción en caso de error
            mysqli_rollback($conn);
            
            // Eliminar la imagen si hubo error
            if (file_exists($destination)) {
                unlink($destination);
            }
            
            die($e->getMessage());
        }
    } else {
        die("Error al mover el archivo subido");
    }
} else {
    header("Location: add_product.php");
    exit();
}

// Cerrar conexión
mysqli_close($conn);
?>