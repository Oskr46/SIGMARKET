<?php
// Iniciar la sesión
session_start();

include ('../../components/conexion.php');

// Verificar si las variables de sesión existen
if (!isset($_SESSION['name']) || !isset($_SESSION['sName']) || !isset($_SESSION['tipo'])  || !isset($_SESSION['email'])) {
    header("location: index.php");
}

$tipo_usuario = $_SESSION['tipo'];   
$email = $_SESSION['email'];
$nombre = $_SESSION['name'];
$apellido = $_SESSION['sName'];

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
    <title>Centro de Cuentas</title>
    <link rel= "icon" href= "<?php echo BASE_URL;?>/res/img/favicon_white.png"/>
    <link rel= "stylesheet" href= "<?php echo BASE_URL;?>/styles/global.css"/>
    <link rel= "stylesheet" href= "<?php echo BASE_URL;?>/styles/panels.css"/>
</head>
<body>
    <header class="header">
        <?php show_header(); ?>
    </header>
    
    <div class="breadcrumb">
        <span>Inicio > Centro de Cuentas</span>
    </div>

    <div class="account-container">
        <aside class="account-sidebar">
            <h3>Informacion de perfil</h3>
            <ul>
                <li><a class="module" href="">Direccion</a><br></li>
                <li><a class="module" href="">Metodos de pago</a></li>
                <?php if($tipo_usuario == 1){?>
                    <li><a class="module" href="admin_panel.php">Panel de Administrador</a></li>
                <?php };?>
            </ul>
        </aside>

        <main class="account-content">
            <h1>CENTRO DE CUENTAS</h1>
            
            <section class="account-section">
                <h2>Informacion personal</h2>
                <div class="account-info">
                    <div class="info-row">
                        <span class="info-label">Nombre de Usuario</span>
                        <span class="info-value"><?php echo $nombre ." ". $apellido; ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Direccion Email</span>
                        <span class="info-value"><?php echo $email ?></span>
                    </div>
                </div>
            </section>

            <section class="account-section">
                <h2>Contraseña y seguridad</h2>
                <div class="account-info">
                    <div class="info-row">
                        <span class="info-label">Contraseña</span>
                        <span class="info-value">••••••••</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Ultimo inicio de Sesion</span>
                        <span class="info-value"><?php echo ($_SESSION["ultimo_acceso"]);?></span>
                    </div>
                </div>
            </section>
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