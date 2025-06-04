<?php
// Iniciar la sesión
session_start();

// Verificar si el usuario es administrador (tipo 1)
if (!isset($_SESSION['tipo']) || $_SESSION['tipo'] != 1) {
    header("location: ../../../index.php");
    exit();
}

include('../../components/conexion.php');
include('../../components/header_footer.php');

// Obtener el nombre del producto que causó el error
$productName = isset($_GET['name']) ? htmlspecialchars(urldecode($_GET['name'])) : '';

// Configurar el tiempo de redirección (5 segundos)
$redirectTime = 5;
$redirectUrl = "prod_panel.php";
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Error: Producto Existente</title>
    <link rel="icon" href="<?php echo BASE_URL;?>/res/img/favicon_white.png"/>
    <link rel="stylesheet" href="<?php echo BASE_URL;?>/styles/global.css"/>
    <link rel="stylesheet" href="<?php echo BASE_URL;?>/styles/panels.css"/>
    <meta http-equiv="refresh" content="<?php echo $redirectTime; ?>;url=<?php echo $redirectUrl; ?>">
</head>
<body>
    <header class="header">
        <?php show_header(); ?>
    </header>
    
    <div class="error-container">
        <div class="error-content">
            <h1>Error: Producto ya existe</h1>
            <div class="error-icon">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/>
                </svg>
            </div>
            <p>El producto <strong>"<?php echo $productName; ?>"</strong> ya existe en el sistema.</p>
            <p>Por favor, elija un nombre diferente para el producto.</p>
            <p>Será redirigido automáticamente al panel de productos en <?php echo $redirectTime; ?> segundos.</p>
            <a href="prod_panel.php" class="back-btn">Volver ahora</a>
        </div>
    </div>

    <footer class="footer">
        <?php show_footer(); ?>
    </footer>
</body>
</html>