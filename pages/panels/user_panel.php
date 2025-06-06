<?php
// Iniciar la sesión
session_start();

include ('../../components/conexion.php');

// Verificar si las variables de sesión básicas existen
if (!isset($_SESSION['tipo']) || !isset($_SESSION['email'])) {
    header("location: index.php");
    exit();
}

$conn = connectDB();
// Estableciendo caracteres UTF8 para BD, importante para acentos y eñes en MySQL                            
mysqli_set_charset($conn, "utf8");

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Obtener datos del usuario desde la base de datos
$email = $_SESSION['email'];
$query = "SELECT nameUser, sNameUser FROM user WHERE emailUser = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "s", $email);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$user_data = mysqli_fetch_assoc($result);

if ($user_data) {
    $nombre = $user_data['nameUser'];
    $apellido = $user_data['sNameUser'];
    
    // Actualizar variables de sesión por si acaso
    $_SESSION['name'] = $nombre;
    $_SESSION['sName'] = $apellido;
} else {
    // Si no encuentra el usuario, redirigir
    header("location: index.php");
    exit();
}

$tipo_usuario = $_SESSION['tipo'];

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
    <link rel= "icon" href= "<?php echo BASE_URL;?>/res/img/favicon_white.png"/>
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
                <li><a class="module" href="../soon.php">Direccion</a><br></li>
                <li><a class="module" href="../soon.php">Metodos de pago</a></li>
                <li><a class="module" href="../users_module/ver_compras.php">Ver Historial de Compras</a></li>
                <?php if($tipo_usuario == 1){?>
                    <li><a class="module" href="admin_panel.php">Panel de Administrador</a></li>
                <?php };?>
            </ul>
        </aside>

        <main class="account-content">
            <h1>CENTRO DE CUENTAS</h1>
            <?php if (isset($_SESSION['success'])): ?>
                <div class="success-message"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
            <?php endif; ?>
            <section class="account-section">
                <h2>Informacion personal</h2>
                <div class="account-info">
                    <div class="info-row">
                        <span class="info-label">Nombre de Usuario</span>
                        <span class="info-value"><?php echo htmlspecialchars($nombre) . " " . htmlspecialchars($apellido); ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Direccion Email</span>
                        <span class="info-value"><?php echo htmlspecialchars($email); ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">¿Quieres Modificar tus datos?</span>
                        <a class="module" href="../users_module/modify_data.php"><span class="info-value">Click Aquí!</span></a>
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
                        <span class="info-value"><?php echo htmlspecialchars($_SESSION["ultimo_acceso"]); ?></span>
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