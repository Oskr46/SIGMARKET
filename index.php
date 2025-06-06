<?php
session_start();
require('components/header_footer.php');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Página Principal - SIGMARKET</title>
    <link rel="stylesheet" href="styles/global.css">
    <link rel="icon" href="res/img/favicon_white.png" type="image/x-icon">
</head>
<body>
    <header class="header">
        <?php show_header(); ?>
    </header>

    <div class="main_cont">
        <h2 class="welcome_msg_title">Conviertete en un insano con Sigma</h2>
        <p class='welcome_msg_detail'>
            Explore nuestra variada gama de prendas meticulosamente elaboradas,
            diseñadas para resaltar su individualidad y satisfacer su sentido del estilo.
        </p>
        <button class="welcome_button">Empieza a comprar ahora</button>

    </div>
    
    <footer class="footer">
        <?php show_footer();?>
    </footer>
</body>
</html>
