<?php
// Iniciar la sesión
session_start();

// Verificar si las variables de sesión existen y el tipo de usuario es admin (1)
if (!isset($_SESSION['name']) || !isset($_SESSION['sName']) || !isset($_SESSION['tipo']) || $_SESSION['tipo'] != 1) {
    header("location: ../../index.php");
    exit();
}

$tipo_usuario = $_SESSION['tipo'];   
$usuario = $_SESSION['email'];
$nombre = $_SESSION['name'];
$apellido = $_SESSION['sName'];

include('../../components/header_footer.php');
include('../../components/conexion.php');
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar Producto - Panel de Administración</title>
    <link rel="icon" href="<?php echo BASE_URL;?>/res/img/favicon_white.png"/>
    <link rel="stylesheet" href="<?php echo BASE_URL;?>/styles/global.css"/>
    <link rel="stylesheet" href="<?php echo BASE_URL;?>/styles/panels.css"/>
    <style>

    </style>
</head>
<body>
    <header class="header">
        <?php show_header(); ?>
    </header>
    
    <div class="breadcrumb">
        <span>Inicio > Panel de Administración > Productos > Agregar Nuevo</span>
    </div>

    <div class="account-container">
        <aside class="account-sidebar">
            <h3>Menú de Administración</h3>
            <ul>
                <li><a class="module" href="../panels/admin_panel.php">Dashboard</a></li>
                <li class="active"><a class="module" href="prod_panel.php">Productos</a></li>
                <li><a class="module" href="../users_module/users_panel.php">Usuarios</a></li>
            </ul>
        </aside>

        <main class="account-content">
            <div class="form-header">
                <h1 class="form-title">AGREGAR NUEVO PRODUCTO</h1>
                <a href="prod_panel.php" class="btn btn-secondary">Volver a Productos</a>
            </div>
            
            <p class="welcome-admin">Bienvenido: <?php echo $nombre . " " . $apellido; ?></p>
            
            <div class="product-form-container">
                <form id="productForm" action="add_product_process.php" method="POST" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="nameProduct" class="form-label required-field">Nombre del Producto</label>
                        <input type="text" id="nameProduct" name="nameProduct" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="descriptionProduct" class="form-label required-field">Descripción</label>
                        <textarea id="descriptionProduct" name="descriptionProduct" class="form-control" required></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="priceProduct" class="form-label required-field">Precio</label>
                        <input type="number" id="priceProduct" name="priceProduct" class="form-control" step="0.01" min="0" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="colorProduct">Color (Código HEX)</label>
                        <input type="color" id="colorProduct" name="colorProduct" 
                               value="<?php echo !empty($producto['colorProduct']) ? htmlspecialchars($producto['colorProduct']) : '#ffffff'; ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="labelProduct" class="form-label">Etiqueta</label>
                        <input type="text" id="labelProduct" name="labelProduct" class="form-control">
                    </div>
                    
                    <div class="form-group">
                        <label for="categoryProduct" class="form-label">Categoría</label>
                        <input type="text" id="categoryProduct" name="categoryProduct" class="form-control">
                    </div>
                    
                    <div class="form-group">
                        <label for="imagen" class="form-label required-field">Imagen del Producto</label>
                        <input type="file" id="imagen" name="imagen" class="form-control" accept="image/jpeg, image/png, image/gif" required>
                        <p class="file-info">Formatos aceptados: JPG, PNG, GIF. Tamaño máximo: 2MB</p>
                    </div>
                    
                    <div class="form-actions">
                        <button type="reset" class="btn btn-secondary">Limpiar Formulario</button>
                        <button type="submit" class="btn btn-primary">Guardar Producto</button>
                    </div>
                </form>
            </div>
        </main>
    </div>

    <footer class="footer">
        <?php show_footer(); ?>
    </footer>
</body>
</html>