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

$conn = connectDB();
//Estableciendo caracteres UTF8 para BD, importante para acentos y eñes en MySQL                            
mysqli_set_charset($conn, "utf8");

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$tipo_usuario = $_SESSION['tipo'];   
$usuario = $_SESSION['user'];
$nombre = $_SESSION['nombre'];
$apellido = $_SESSION['apellido'];

if($tipo_usuario!=1){
    session_destroy();
    header("location: index.php");
}



//Consulta para contar la cantidad de usuarios
$q_contar = "SELECT COUNT(*) as contar from usuarios";
$consulta_contar = mysqli_query($conn,$q_contar);
$array_contar = mysqli_fetch_array($consulta_contar);
$cantidad_usuarios = $array_contar['contar'];
//Consulta para traer todos los registros de la tabla usuarios
$q = "SELECT * from usuarios";
$consulta = mysqli_query($conn,$q);

?>



<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Usuarios</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f5f5f5;
            color: #333;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
        }
        
        h1 {
            color: #2c3e50;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 1px solid #eee;
        }
        
        .users-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        
        .users-table th {
            background-color: #3498db;
            color: white;
            padding: 12px 15px;
            text-align: left;
            font-weight: 600;
        }
        
        .users-table td {
            padding: 12px 15px;
            border-bottom: 1px solid #ddd;
        }
        
        .users-table tr:hover {
            background-color: #f5f9fc;
        }
        
        .users-table tr:last-child td {
            border-bottom: none;
        }
        
        .edit-btn {
            background-color: #2ecc71;
            color: white;
            border: none;
            padding: 6px 12px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            transition: background-color 0.3s;
        }
        
        .edit-btn:hover {
            background-color: #27ae60;
        }
        
        .user-type {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
        }
        
        .admin {
            background-color: #e74c3c;
            color: white;
        }
        
        .user {
            background-color: #f39c12;
            color: white;
        }
        
        .guest {
            background-color: #95a5a6;
            color: white;
         /* Separación del botón Editar */
    }
    .delete-btn {
        background-color: #e74c3c;
        color: white;
        border: none;
        padding: 6px 12px;
        border-radius: 4px;
        cursor: pointer;
        font-size: 14px;
        transition: background-color 0.3s;
        margin-left: 5px; /* Separación del botón Editar */
    }

    .delete-btn:hover {
        background-color: #c0392b;
    }

    /* Para el contenedor de botones */
    .action-buttons {
        display: flex;
        gap: 5px; /* Separación entre botones */
    }
        
    </style>
</head>
<body>
    <div class="container">
    <p>Bienvenido: <?php echo $nombre ." ". $apellido; ?></p>
        <h1>Lista de Usuarios</h1>
        <h3>Cantidad de Usuarios: <?php echo $cantidad_usuarios; ?></h3>
        <table class="users-table">
            <thead>
                <tr>
                    <th>Email</th>
                    <th>Nombres</th>
                    <th>Apellidos</th>
                    <th>Tipo</th>
                    <th>Editar</th>
                    <th>Eliminar</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                while($fila = mysqli_fetch_array($consulta)){?>
                <tr>
                    <td><?php echo $fila['email'];?></td>
                    <td><?php echo $fila['nombres'];?></td>
                    <td><?php echo $fila['apellidos'];?></td>
                    <td><?php if($fila['tipo'] == 1){
                        echo '<span class="user-type admin">Administrador</span>';
                        } else {
                            echo '<span class="user-type user">Usuario</span>';
                        }
                        ?>
                    </td>
                    <td>
                        <form action="editar_usuario.php" method="POST" style="display: inline;">
                            <input type="hidden" name="email" value=<?php echo $fila['email'];?>>
                            <button type="submit" class="edit-btn">Editar</button>
                        </form>
                    </td>
                    <td>
                        <form action="eliminar_usuario" method="POST" style="display: inline;">
                            <input type="hidden" name="email" value=<?php echo $fila['email'];?>>
                            <button type="submit" class="delete-btn">Eliminar</button>
                        </form>
                    </td>

                </tr>    
                    <?php
                }  ?>
            </tbody>
        </table>

        <p><a href="admin.php">Ir al panel de administración</a></p>
    
        <p><a href="cerrar_sesion.php">Cerrar sesión</a></p>
    </div>
</body>
</html>