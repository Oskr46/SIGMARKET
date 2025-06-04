<?php include('../../components/header_footer.php');
include('../../components/conexion.php');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro - SIGMARKET</title>
    <link rel= "icon" href="../../res/img/favicon_white.png"/>
    <link rel="stylesheet" href="../../styles/global.css"/>
    <link rel="stylesheet" href="../../styles/register.css"/>
</head>
<body>
    <header class="header">
        <?php show_header();?>
    </header>

        <div class="register">
            <div class ="register_title">
                <h2>Registro de Usuario</h2>
            </div>

            <div class ="register_container">
                <div class="register_container_title">
                    <h3>Completa la informacion para crear tu cuenta</h3>
                </div>
                <form action="register_process.php" method="POST">
                    <div class="register_container_data">
                        <label for="email">Correo Electronico</label>
                        <input name= "email" type="text" placeholder="Email" required/>
                    </div>
                    <div class="register_container_data">
                        <label for="name">Nombre</label>
                        <input name= "name" type="text" placeholder="Coloca tu nombre" required/>
                    </div>
                    <div class="register_container_data">
                        <label for="sName">Apellido</label>
                        <input name= "sName" type="text" placeholder="Coloca tu Apellido" required/>
                    </div>
                    <div class="register_container_data">
                        <label for="password">Contraseña</label>
                        <input name= "password" type="text" placeholder="Crea una Contraseña" required/>
                    </div>
                    <button class="confirmar" type="submit">Confirmar registro</button>
                </form>
            </div> 
        </div>

    <footer class="footer">
        <?php show_footer(); ?>
    </footer>
</body>
</html>