<?php
// Iniciar la sesión
session_start();

include ('conexion.php');

$nombre = $_SESSION['nombre'];
$apellido = $_SESSION['apellido'];

// Verificar si las variables de sesión existen
if (!isset($_SESSION['nombre']) || !isset($_SESSION['apellido']) || !isset($_SESSION['tipo'])) {
    header("location: index.php");
}

$email_usuario_registrado = $_GET['email'];  

$conn = connectDB();
//Estableciendo caracteres UTF8 para BD, importante para acentos y eñes en MySQL                            
mysqli_set_charset($conn, "utf8");

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$consulta = mysqli_query($conn, "SELECT * FROM usuarios WHERE email = '$email_usuario_registrado' ORDER BY Id DESC LIMIT 1 ");

if($consulta){

    //Ahora valida que la consuta haya traido registros
    if( mysqli_num_rows( $consulta ) > 0){
  
      //Mientras mysqli_fetch_array traiga algo, lo agregamos a una variable temporal
      while($fila = mysqli_fetch_array( $consulta ) ){
        $nombres_usuario = $fila['nombres'];
        $apellidos_usuario = $fila['apellidos'];
        
      }
  
    }
    //Liberando la memoria de la consulta
    mysqli_free_result($consulta);

}

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
        <h1>Usuario registrado exitosamente</h1>
        <h3>El usuario con correo electrónico: <?php echo $email_usuario_registrado; ?> ha sido registrado en el sistema</h3>
        <h4>Nombres: <?php echo $nombres_usuario; ?></h4>
        <h4>Apellidos: <?php echo $apellidos_usuario; ?></h4>

        <p><a href="admin.php">Ir al panel de administración</a></p>
    
        <p><a href="cerrar_sesion.php">Cerrar sesión</a></p>
    </div>

   
</body>
</html>