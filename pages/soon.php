<?php
session_start();
require('../components/header_footer.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Coming Soon - SIGMARKET</title>
    <link rel="icon" href="../res/img/favicon_white.png">
    <link rel="stylesheet" href="../styles/global.css">
</head>
<body>
    <header class="header">
        <?php show_header(); ?>
    </header>

    <div class="main_cont">
        <h2 class="welcome_msg_title">PROXIMAMENTE</h2>
        <p class='welcome_msg_detail'>
            Esta funcionalidad aun no se encuentra disponible.
        </p>
        <a class="welcome_button" href="../index.php">Empieza a comprar ahora</a>

    </div>

    <footer class="footer">
        <?php show_footer(); ?>
    </footer>
</body>
</html>