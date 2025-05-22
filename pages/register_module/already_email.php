<?php

$email_encontrado = $_GET['email_reg'];

require_once(__DIR__ . '/../../config.php');
include("../../components/header_footer.php");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Usuarios</title>
    <link rel= "icon" href="../../res/img/favicon_white.png"/>
    <link rel= "stylesheet" href="../../styles/global.css"/>
    <link rel= "stylesheet" href="../../styles/register.css"/>
</head>
<body>
    <header class="header">
        <?php show_header();?>
    </header>

    <div class="register">
        <div class="confirm_register">
            <div class="register_container_title">
                <h2>Usuario ya registrado</h2>
            </div>
            <div class="register_container_data">
                <h4>El usuario con correo electrónico: <?php echo $email_encontrado;?> ya se encuentra registrado en el sistema</h4>
            </div>
            <div class="register_container_data">
                <p><a class ="redir" href="<?php echo BASE_URL;?>index.php">Ir a Pagina Principal</a></p>
    
                <p><a class ="redir" href="<?php echo BASE_URL;?>">Iniciar Sesión</a></p>
            </div>
        </div>
    </div>  

    <footer class="footer">
        <?php show_footer(); ?>
    </footer>
</body>
</html>