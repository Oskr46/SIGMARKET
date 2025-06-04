<?php
session_start();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - SIGMARKET</title>
    <link rel="stylesheet" href="../../styles/global_login.css">
    <link rel="icon" href="../../res/img/favicon_white.png" type="image/x-icon">
    <?php require('../../components/header_footer.php')?>
    
</head>
<body>
    <header class="header">
        <?php show_header()?>
    </header>  
    
    <div class="main_cont">
        <form action="mk_session.php" method="post">
            <div class="contenedor">        
                <div class="square"></div>
            </div>  
            <div class="label">
                <label for="user">Iniciar Sesión</label>
            </div>
            <div class="boxes">
               <input type="text" class="user_box <?php echo (isset($_SESSION['login_error'])) ? 'error' : ''; ?>" 
                name="user" placeholder="Email" required 
                value="<?php echo $_SESSION['attempted_email'] ?? ''; ?>">

                <?php if (isset($_SESSION['login_error']) && $_SESSION['login_error'] === 'email_not_found'): ?>
                <div class="error-message" style="position: absolute; top: 44vh; right: 25vw; color: red; font-size: 12px;">
                Email no registrado
                </div>
                <?php endif; ?>
                
                <div class="cuadro"></div>
                <input type="password" class="password_box <?php echo (isset($_SESSION['login_error']) ? 'error' : ''); ?>" 
                       name="password" placeholder="Contraseña" required>
                
                <?php if (isset($_SESSION['login_error']) && $_SESSION['login_error'] === 'wrong_password'): ?>
                <div class="error-message" style="position: absolute; top: 53vh; right: 25vw; color: red; font-size: 12px;">
                Contraseña incorrecta
                </div>
                <?php endif; ?>
                
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

    <?php
    // Limpiar los mensajes de error después de mostrarlos
    unset($_SESSION['login_error']);
    unset($_SESSION['attempted_email']);
    ?>
</body>
</html>