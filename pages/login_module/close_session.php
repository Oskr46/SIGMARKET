<?php 
session_start();

// Limpiar todas las variables de sesión
$_SESSION = array();

// Destruir la sesión
session_destroy();

// Redirigir con parámetro para mostrar mensaje
header("Location: ../../index.php?logout=1");
exit();
?>