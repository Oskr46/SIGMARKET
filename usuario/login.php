<html lang="es">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>

<?php
session_start();
include ('conexion.php');
$usuario = $_POST['email'];
$clave = $_POST['password'];
$clave_encriptada = md5($clave);
//$clave_encriptada=password_hash($clave, PASSWORD_DEFAULT);
echo $usuario;
echo '<br>';
echo $clave;
echo '<br>';

$conn = connectDB();

//Estableciendo caracteres UTF8 para BD, importante para acentos y eñes en MySQL                            
mysqli_set_charset($conn, "utf8");
                              
                              
// Verificamos la conexión a base de datos
if (!$conn) {
      die("Connection failed: " . mysqli_connect_error());
}

//Contamos la cantidad de registros que devuelve la consulta con el usuario y la contraseña recibidos
$q = "SELECT COUNT(*) as contar from usuarios where email = '$usuario' and password = '$clave_encriptada'";


$consulta = mysqli_query($conn,$q);

$array = mysqli_fetch_array($consulta);
echo $clave_encriptada;
echo "<html> <br> <br></html>";
echo $array['contar'];

if($array['contar']>0){   
  
    mysqli_set_charset($conn, "utf8");
    $resultado = mysqli_query($conn, "SELECT * FROM usuarios WHERE email = '$usuario'");
   
    if($resultado){
        //Ahora valida que la consuta haya traido registros
        if( mysqli_num_rows( $resultado ) > 0){
      
          //Mientras mysqli_fetch_array traiga algo, lo agregamos a una variable temporal
          while($fila = mysqli_fetch_array( $resultado ) ){
      
            //Ahora $fila tiene la primera fila de la consulta, pongamos que tienes
            //un campo en tu DB llamado NOMBRE, así accederías
            //echo $fila['Id'];
            
            $nombre = $fila['nombres'];
            $apellido = $fila['apellidos'];
            $tipo_usuario=$fila['tipo'];
  
            echo $usuario;
            echo "<html> <br> <br></html>";
            echo $nombre;
            echo "<html> <br> <br></html>";
                        
          }
      
        }
  
  //Recuerda liberar la memoria del resultado, 
  mysqli_free_result( $resultado );
  }      
 mysqli_close($conn);

    $_SESSION['tipo'] = $tipo_usuario;   
    $_SESSION['user'] = $usuario;
    $_SESSION['nombre'] = $nombre;
    $_SESSION['apellido'] = $apellido;
    $_SESSION['ultimo_acceso'] = date("Y-m-d H:i:s"); 
    switch($tipo_usuario){
    case 1: header("location: admin.php");
            break;
    case 2: header("location: usuario.php");
            break;
   }

    
} else {

    header("location: error_acceso.php");

}
?>
</html>