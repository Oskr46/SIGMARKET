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

// Consulta modificada para incluir las imágenes
$query = "SELECT p.*, i.urlImage 
          FROM products p
          LEFT JOIN imageproduct i ON p.idProduct = i.idProduct";
$result = mysqli_query($conn, $query);

include('../../components/header_footer.php');
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administración de Productos</title>
    <link rel= "icon" href= "<?php echo BASE_URL;?>/res/img/favicon_white.png"/>
    <link rel= "stylesheet" href= "<?php echo BASE_URL;?>/styles/global.css"/>
    <link rel= "stylesheet" href= "<?php echo BASE_URL;?>/styles/panels.css"/>
</head>
<body>
    <header class="header">
        <?php show_header(); ?>
    </header>
    
    <div class="breadcrumb">
        <span>Inicio > Centro de Cuentas > Panel de Administración > Administración de Productos</span>
    </div>

    <div class="account-container">
        <aside class="account-sidebar">
            <h3>Menú de Administración</h3>
            <ul>
                <li><a class="module" href="../panels/admin_panel.php">Dashboard</a></li>
                <li class="active">Productos</li>
                <li><a class="module" href="../users_module/users_panel.php">Usuarios</a></li>
            </ul>
        </aside>

        <main class="account-content">
            <div class="products-header">
                <h1>ADMINISTRACIÓN DE PRODUCTOS</h1>
                <a href="add_product.php" class="add-product-btn">
                    <span>➕Agregar Producto</span>
                </a>
            </div>
            
            <section class="account-section">
                <?php if(mysqli_num_rows($result) > 0): ?>
                    <table class="products-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Imagen</th>
                                <th>Nombre</th>
                                <th>Descripción</th>
                                <th>Precio</th>
                                <th>Color</th>
                                <th>Etiqueta</th>
                                <th>Categoria</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($row = mysqli_fetch_assoc($result)): ?>
                                <tr>
                                    <td><?php echo $row['idProduct']; ?></td>
                                    <td>
                                        <?php if(!empty($row['urlImage'])): ?>
                                            <img src="<?php echo BASE_URL . $row['urlImage']; ?>" 
                                                 alt="Imagen de producto" 
                                                 class="product-image-thumb">
                                        <?php else: ?>
                                            <div class="no-image">Sin imagen</div>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo $row['nameProduct']; ?></td>
                                    <td><?php echo substr($row['descriptionProduct'], 0, 50) . '...'; ?></td>
                                    <td>$<?php echo number_format($row['priceProduct'], 2); ?></td>
                                    <td>
                                        <?php if(!empty($row['colorProduct'])): ?>
                                            <span style="display: inline-block; width: 15px; height: 15px; background: <?php echo $row['colorProduct']; ?>; border-radius: 50%; border: 1px solid #ddd;"></span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo $row['labelProduct']; ?></td>
                                    <td><?php echo $row['categoryProduct']; ?></td>
                                    <td>
                                        <div class="action-btns">
                                            <a href="edit_product.php?id=<?php echo $row['idProduct']; ?>" class="edit-btn">Editar</a>
                                            <a href="delete_product.php?id=<?php echo $row['idProduct']; ?>" class="delete-btn" onclick="return confirm('¿Estás seguro de eliminar este producto?')">Eliminar</a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div class="no-products">
                        <p>No hay productos registrados. ¡Agrega tu primer producto!</p>
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