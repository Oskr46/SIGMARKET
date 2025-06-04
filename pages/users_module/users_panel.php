<?php
// Iniciar la sesión
session_start();

include ('../../components/conexion.php');

// Verificar si las variables de sesión existen
if (!isset($_SESSION['name']) || !isset($_SESSION['sName']) || !isset($_SESSION['tipo'])  || !isset($_SESSION['email'])) {
    header("location: index.php");
}

$tipo_usuario = $_SESSION['tipo'];   
$usuario = $_SESSION['email'];
$nombre = $_SESSION['name'];
$apellido = $_SESSION['sName'];

if($tipo_usuario!=1){
    session_destroy();
    header("location: ../../index.php");
}

$conn = connectDB();
// Estableciendo caracteres UTF8 para BD                            
mysqli_set_charset($conn, "utf8");

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Consulta para obtener los usuarios
$query = "SELECT idUser, nameUser, sNameUser, emailUser, adminBool FROM user";
$result = mysqli_query($conn, $query);

include('../../components/header_footer.php');
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administración de Usuarios</title>
    <link rel= "icon" href= "<?php echo BASE_URL;?>/res/img/favicon_white.png"/>
    <link rel= "stylesheet" href= "<?php echo BASE_URL;?>/styles/global.css"/>
    <link rel= "stylesheet" href= "<?php echo BASE_URL;?>/styles/panels.css"/>
</head>
<body>
    <header class="header">
        <?php show_header(); ?>
    </header>
    
    <div class="breadcrumb">
        <span>Inicio > Centro de Cuentas > Panel de Administración > Administración de Usuarios</span>
    </div>

    <div class="account-container">
        <aside class="account-sidebar">
            <h3>Menú de Administración</h3>
            <ul>
                <li><a class="module" href="../panels/admin_panel.php">Dashboard</a></li>
                <li><a class="module" href="../products_module/prod_panel.php">Productos</a></li>
                <li class="active">Usuarios</li>
            </ul>
        </aside>

        <main class="account-content">
            <div class="products-header">
                <h1>ADMINISTRACIÓN DE USUARIOS</h1>
            </div>
            
            <section class="account-section">
                <?php if(mysqli_num_rows($result) > 0): ?>
                    <table class="products-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nombre</th>
                                <th>Apellido</th>
                                <th>Email</th>
                                <th>Tipo</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($row = mysqli_fetch_assoc($result)): ?>
                                <tr>
                                    <td><?php echo $row['idUser']; ?></td>
                                    <td><?php echo $row['nameUser']; ?></td>
                                    <td><?php echo $row['sNameUser']; ?></td>
                                    <td><?php echo $row['emailUser']; ?></td>
                                    <td><?php echo ($row['adminBool'] == 1) ? 'Administrador' : 'Usuario'; ?></td>
                                    <td>
                                        <div class="action-btns">
                                            <a href="edit_user.php?id=<?php echo $row['idUser']; ?>" class="edit-btn">Editar</a>
                                            <?php if($row['emailUser'] != $_SESSION['email']): ?>
                                                <a href="delete_user.php?id=<?php echo $row['idUser']; ?>" class="delete-btn" onclick="return confirm('¿Estás seguro de eliminar este usuario?')">Eliminar</a>
                                            <?php else: ?>
                                                <span class="disabled-btn">Actual</span>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div class="no-products">
                        <p>No hay usuarios registrados.</p>
                    </div>
                <?php endif; ?>
            </section>
        
            <div class="logout-section">
                <a class="close_session" href="../login_module/close_session.php">Cerrar sesión</a>
            </div>
        </main>
    </div>

    <footer class="footer">
        <?php show_footer();?>
    </footer>
</body>
</html>