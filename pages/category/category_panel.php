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

// Consulta para obtener todas las categorías
$query = "SELECT idCategory, nameCategory FROM category";
$result = mysqli_query($conn, $query);

// Mensajes de éxito/error
$success_messages = [
    '1' => 'Categoría agregada exitosamente',
    '2' => 'Categoría actualizada exitosamente',
    '3' => 'Categoría eliminada exitosamente'
];

$error_messages = [
    '1' => 'Error: La categoría no existe',
    '2' => 'Error: No se pudo eliminar la categoría',
    '3' => 'Error: No se puede eliminar la categoría porque tiene productos asociados',
    '4' => 'Error: No se pudo completar la operación'
];

include('../../components/header_footer.php');
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administración de Categorías</title>
    <link rel= "icon" href= "<?php echo BASE_URL;?>/res/img/favicon_white.png"/>
    <link rel= "stylesheet" href= "<?php echo BASE_URL;?>/styles/global.css"/>
    <link rel= "stylesheet" href= "<?php echo BASE_URL;?>/styles/panels.css"/>
</head>
<body>
    <header class="header">
        <?php show_header(); ?>
    </header>
    
    <div class="breadcrumb">
        <span>Inicio > Centro de Cuentas > Panel de Administración > Administración de Categorías</span>
    </div>

    <div class="account-container">
        <aside class="account-sidebar">
            <h3>Menú de Administración</h3>
            <ul>
                <li><a class="module" href="../panels/admin_panel.php">Dashboard</a></li>
                <li><a class="module" href="../products_module/prod_panel.php">Productos</a></li>
                <li><a class="module" href="../users_module/users_panel.php">Usuarios</a></li>
                <li class="active">Categorías</li>
                <li><a class="module" href="../panels/compras_panel.php">Ver Historial de Compras</a></li>
            </ul>
        </aside>

        <main class="account-content">
            <div class="products-header">
                <h1>ADMINISTRACIÓN DE CATEGORÍAS</h1>
                <a href="add_category.php" class="add-product-btn">
                    <span>➕Agregar Categoría</span>
                </a>
            </div>
            
            <!-- Mensajes de éxito/error -->
            <?php if(isset($_GET['success']) && isset($success_messages[$_GET['success']])): ?>
                <div class="alert alert-success">
                    <?php echo $success_messages[$_GET['success']]; ?>
                    <span class="close-btn" onclick="this.parentElement.style.display='none'">&times;</span>
                </div>
            <?php endif; ?>
            
            <?php if(isset($_GET['error']) && isset($error_messages[$_GET['error']])): ?>
                <div class="alert alert-error">
                    <?php echo $error_messages[$_GET['error']]; ?>
                    <span class="close-btn" onclick="this.parentElement.style.display='none'">&times;</span>
                </div>
            <?php endif; ?>
            
            <section class="account-section">
                <?php if(mysqli_num_rows($result) > 0): ?>
                    <table class="products-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nombre de Categoría</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($row = mysqli_fetch_assoc($result)): ?>
                                <tr>
                                    <td><?php echo $row['idCategory']; ?></td>
                                    <td><?php echo htmlspecialchars($row['nameCategory']); ?></td>
                                    <td>
                                        <div class="action-btns">
                                            <a href="edit_category.php?id=<?php echo $row['idCategory']; ?>" class="edit-btn">Editar</a>
                                            <a href="delete_category.php?id=<?php echo $row['idCategory']; ?>" class="delete-btn" onclick="return confirm('¿Estás seguro de eliminar esta categoría?')">Eliminar</a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div class="no-products">
                        <p>No hay categorías registradas. ¡Agrega tu primera categoría!</p>
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

    <style>
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
            position: relative;
        }
        
        .alert-success {
            background-color: #dff0d8;
            color: #3c763d;
            border: 1px solid #d6e9c6;
        }
        
        .alert-error {
            background-color: #f2dede;
            color: #a94442;
            border: 1px solid #ebccd1;
        }
        
        .close-btn {
            position: absolute;
            right: 15px;
            top: 15px;
            cursor: pointer;
            font-weight: bold;
        }
    </style>
</body>
</html>