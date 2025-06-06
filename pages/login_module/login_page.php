<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - SIGMARKET</title>
    <link rel="stylesheet" href="../../styles/global_login.css">
    <link rel="icon" href="../../res/img/favicon_white.png" type="image/x-icon">
    <?php require('../../components/header_footer.php')?>
    <style>
        .error-message {
            color: red;
            text-align: center;
            margin: 10px 0;
        }
    </style>
</head>
<body>
    <header class="header"> 
        <?php show_header()?>
    </header>  
    
    <div class="main_cont">
        <?php if (isset($_GET['error'])): ?>
            <div class="error-message">
                <?php 
                switch($_GET['error']) {
                    case 'credenciales_invalidas':
                        echo "Email o contraseña incorrectos";
                        break;
                    case 'datos_faltantes':
                        echo "Por favor complete todos los campos";
                        break;
                    default:
                        echo "Error al iniciar sesión";
                }
                ?>
            </div>
        <?php endif; ?>
        
        <form action="mk_session.php" method="post">
            <div class="contenedor">        
                <div class="square"></div>
            </div>  
            <div class="label">
                <label for="user">Iniciar Sesión</label>
            </div>
            <div class="boxes">
                <input type="email" class="user_box" name="user" placeholder="Email" required>
                <div class="cuadro"></div>
                <input type="text" class="password_box" name="password" placeholder="Contraseña" required>
                <a class="forgot_password">¿Olvidaste tu contraseña?</a>
                <button type="submit" class="bttn_iniciar">Iniciar Sesión</button>
            </div>
        </form>
        <form action="<?php echo BASE_URL; ?>pages/register_module/register_page.php">
            <div class="boxes">
                <button type="submit" class="bttn_crear">Crear cuenta</button>
            </div>
        </form>
    </div>
    
    <footer class="footer">
        <?php show_footer()?>
    </footer>
</body>
</html>