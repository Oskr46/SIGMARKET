<?php
include ('../../components/conexion.php');

$email_registrado = $_GET['email_reg'];  

$conn = connectDB();
//Estableciendo caracteres UTF8 para BD, importante para acentos y eñes en MySQL                            
mysqli_set_charset($conn, "utf8");

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$consulta = mysqli_query($conn, "SELECT * FROM user WHERE emailUser = '$email_registrado' ORDER BY idUser DESC LIMIT 1 ");

if($consulta){

    //Ahora valida que la consuta haya traido registros
    if( mysqli_num_rows( $consulta ) > 0){
  
      //Mientras mysqli_fetch_array traiga algo, lo agregamos a una variable temporal
      while($fila = mysqli_fetch_array( $consulta ) ){
        $nombres_usuario = $fila['nameUser'];
        $apellidos_usuario = $fila['sNameUser'];
        
      }
  
    }
    //Liberando la memoria de la consulta
    mysqli_free_result($consulta);

}

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
                <h1>Usuario registrado exitosamente</h1>
            </div>        
            <div class="register_container_data">
                <h3>El usuario con correo electrónico: <?php echo $email_registrado;?> ha sido registrado en el sistema</h3>
            </div>
            <div class="register_container_data">
                <h3>Nombres: <?php echo $nombres_usuario; ?></h3>
            </div>    
            <div class="register_container_data">
                <h3>Apellidos: <?php echo $apellidos_usuario; ?></h3>
            </div>

            <p><a class="redir" href="../../index.php">Ir a la Pagina Principal</a></p>
        </div>
    </div>

   <footer class="footer">
        <?php show_footer(); ?>
    </footer>

</body>
</html>