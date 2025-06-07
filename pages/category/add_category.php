<?php
session_start();
include ('../../components/conexion.php');

// Verificar sesión y permisos
if (!isset($_SESSION['name']) || !isset($_SESSION['sName']) || !isset($_SESSION['tipo']) || !isset($_SESSION['email'])) {
    header("location: index.php");
}

$tipo_usuario = $_SESSION['tipo'];   
if($tipo_usuario!=1){
    session_destroy();
    header("location: ../../index.php");
}

$conn = connectDB();
// Procesar formulario
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombreCategoria = mysqli_real_escape_string($conn, $_POST['nameCategory']);
    
    $query = "INSERT INTO category (nameCategory) VALUES ('$nombreCategoria')";
    if(mysqli_query($conn, $query)) {
        header("location: category_panel.php?success=1");
    } else {
        $error = "Error al agregar categoría: " . mysqli_error($conn);
    }
}

include('../../components/header_footer.php');
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar Categoría</title>
    <link rel= "icon" href= "<?php echo BASE_URL;?>/res/img/favicon_white.png"/>
    <link rel= "stylesheet" href= "<?php echo BASE_URL;?>/styles/global.css"/>
    <link rel= "stylesheet" href= "<?php echo BASE_URL;?>/styles/panels.css"/>
</head>
<body>
    <header class="header">
        <?php show_header(); ?>
    </header>
    
    <div class="breadcrumb">
        <span>Inicio > Centro de Cuentas > Panel de Administración > Categorías > Agregar</span>
    </div>

    <div class="account-container">
        <aside class="account-sidebar">
            <h3>Menú de Administración</h3>
            <ul>
                <li><a class="module" href="../panels/admin_panel.php">Dashboard</a></li>
                <li><a class="module" href="../products_module/products_panel.php">Productos</a></li>
                <li><a class="module" href="categories_panel.php">Categorías</a></li>
                <li><a class="module" href="../users_module/users_panel.php">Usuarios</a></li>
            </ul>
        </aside>

        <main class="account-content">
            <div class="products-header">
                <h1>AGREGAR NUEVA CATEGORÍA</h1>
                <a href="category_panel.php" class="add-product-btn">
                    <span>← Volver</span>
                </a>
            </div>
            
            <section class="account-section">
                <?php if(isset($error)): ?>
                    <div class="error-message"><?php echo $error; ?></div>
                <?php endif; ?>
                
                <form method="POST" class="category-form">
                    <div class="form-group">
                        <label for="nameCategory">Nombre de la Categoría:</label>
                        <input type="text" id="nameCategory" name="nameCategory" required>
                    </div>
                    
                    <div class="form-actions">
                        <button type="submit" class="edit-btn">Guardar Categoría</button>
                        <a href="category_panel.php" class="delete-btn">Cancelar</a>
                    </div>
                </form>
            </section>
        </main>
    </div>

    <footer class="footer">
        <?php show_footer();?>
    </footer>
</body>
</html>