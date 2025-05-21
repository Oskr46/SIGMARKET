<?php
// Iniciar la sesión
session_start();

$nombre = $_SESSION['nombre'];
$apellido = $_SESSION['apellido'];

// Verificar si las variables de sesión existen
if (!isset($_SESSION['nombre']) || !isset($_SESSION['apellido']) || !isset($_SESSION['tipo'])) {
    header("location: index.php");
}

$email_nuevo_usuario = $_GET['email'];  

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Usuarios</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
        
        .container {
            background-color: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 500px;
        }
        
        h1 {
            text-align: center;
            color: #333;
            margin-bottom: 30px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
            color: #555;
        }
        
        input[type="text"],
        input[type="email"],
        select {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
            font-size: 16px;
        }
        
        input[type="text"]:focus,
        input[type="email"]:focus,
        select:focus {
            border-color: #4a90e2;
            outline: none;
            box-shadow: 0 0 5px rgba(74, 144, 226, 0.3);
        }
        
        .required-field::after {
            content: " *";
            color: red;
        }
        
        button {
            background-color: #4a90e2;
            color: white;
            border: none;
            padding: 12px 20px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            width: 100%;
            transition: background-color 0.3s;
        }
        
        button:hover {
            background-color: #357ab8;
        }
        
        .error-message {
            color: red;
            font-size: 14px;
            margin-top: 5px;
            display: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <p>Bienvenido: <?php echo $nombre ." ". $apellido; ?></p>
        <h1>Usuario ya registrado</h1>
        <h3>El usuario con correo electrónico: <?php echo $email_nuevo_usuario; ?> ya se encuentra registrado en el sistema</h3>

        <p><a href="admin.php">Ir al panel de administración</a></p>
    
        <p><a href="cerrar_sesion.php">Cerrar sesión</a></p>
    </div>

   
</body>
</html>