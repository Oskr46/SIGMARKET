<?php
//Función de conexión a base de datos
function connectDB(){
    $servername = "localhost";
    $database = "sigmarket";
    $username = "admin";
    $password = "1";
    // Creando la conexión
    $conn = mysqli_connect($servername, $username, $password, $database) 
        or die("Ha sucedido un error inexperado en la conexion de la base de datos");
    return $conn;

}


//Función de desconexión a base de datos
function disconnectDB($conexion){
    $close = mysqli_close($conexion) 
        or die("Ha sucedido un error inexperado en la desconexion de la base de datos");
    return $close;
}