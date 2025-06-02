<?php
// Iniciar la sesión
session_start();

include ('../../components/conexion.php');

// Verificar si las variables de sesión existen
if (!isset($_SESSION['name']) || !isset($_SESSION['sName']) || !isset($_SESSION['tipo'])  || !isset($_SESSION['email'])) {
    header("location: index.php");
}

$tipo_usuario = $_SESSION['tipo'];   
$usuario = $_SESSION['email'];
$nombre = $_SESSION['name'];
$apellido = $_SESSION['sName'];

if($tipo_usuario!=1){
    session_destroy();
    header("location: ../../index.php");
}

$conn = connectDB();
//Estableciendo caracteres UTF8 para BD, importante para acentos y eñes en MySQL                            
mysqli_set_charset($conn, "utf8");

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

include('../../components/header_footer.php');
?>


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administrador</title>
    <link rel= "stylesheet" href= "<?php echo BASE_URL;?>/styles/global.css"/>
    <link rel= "icon" href= "<?php echo BASE_URL;?>/res/img/favicon_white.png"/>
    <link rel= "stylesheet" href= "<?php echo BASE_URL;?>/styles/panels.css"/>
</head>
<body>
    <header class="header">
        <?php show_header(); ?>
    </header>
    
    <div class="breadcrumb">
        <span>Inicio > Centro de Cuentas > Panel de Administración</span>
    </div>

    <div class="account-container">
        <aside class="account-sidebar">
            <h3>Menú de Administración</h3>
            <ul>
                <li class="active">Dashboard</li>
                <li>Productos</li>
                <li>Usuarios</li>
                <li>Pedidos</li>
                <li>Configuración</li>
            </ul>
        </aside>

        <main class="account-content">
            <h1>PANEL DE ADMINISTRADOR</h1>
            
            <section class="welcome-section">
                <p>Bienvenido Administrador: <strong><?php echo $nombre ." ". $apellido; ?></strong></p>
            </section>

            <section class="admin-actions">
                <h2>Acciones Rápidas</h2>
                <div class="action-buttons">
                    <a class="action-button" href="">
                        <i class="icon">➕</i>
                        <span>Agregar Producto</span>
                    </a>
                    <a class="action-button" href="">
                        <i class="icon">📦</i>
                        <span>Administrar Productos</span>
                    </a>
                    <a class="action-button" href="">
                        <i class="icon">👥</i>
                        <span>Administrar Usuarios</span>
                    </a>
                </div>
            </section>
        
            <div class="logout-section">
                <a class="close_session" href="../login_module/close_session.php">Cerrar sesión</a>
            </div>
        </main>
    </div>

    <footer class="footer">
        <?php show_footer();?>
    </footer>
</body>
</html>