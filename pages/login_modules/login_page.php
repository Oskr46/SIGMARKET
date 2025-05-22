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
        <form action="" method="post">
            <div class="contenedor">        
                <div class="square"></div>
            </div>  
            <div class="label">
                <label for="user">Iniciar Sesión</label>
            </div>
            <div class="boxes">
                <input type="text" class="user_box" name="user" placeholder="Email" required>
                <div class="cuadro"></div>
                <input type="text" class="password_box" name="password" placeholder="Contraseña" required>
                <a href="" class="forgot_password">¿Olvidaste tu contraseña?</a>
                <button type="submit" class="bttn_iniciar">Iniciar Sesión</button>
            </div>
        </form>
        <form action="">
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
