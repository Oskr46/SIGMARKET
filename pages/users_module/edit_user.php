<?php
// Iniciar la sesión
session_start();

include('../../components/conexion.php');

// Verificar permisos de administrador
if (!isset($_SESSION['tipo']) || $_SESSION['tipo'] != 1) {
    header("location: ../../index.php");
    exit();
}

// Obtener ID del usuario a editar
$idUser = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($idUser <= 0) {
    header("location: users_panel.php");
    exit();
}

// Obtener datos del usuario
$conn = connectDB();
mysqli_set_charset($conn, "utf8");

$query = "SELECT idUser, nameUser, sNameUser, emailUser, adminBool FROM user WHERE idUser = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $idUser);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$usuario = mysqli_fetch_assoc($result);

if (!$usuario) {
    header("location: users_panel.php");
    exit();
}

// Procesar el formulario de actualización
// Procesar el formulario de actualización
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Recoger y sanitizar datos
    $nameUser = mysqli_real_escape_string($conn, $_POST['nameUser']);
    $sNameUser = mysqli_real_escape_string($conn, $_POST['sNameUser']);
    $adminBool = intval($_POST['adminBool']);
    $emailUser = mysqli_real_escape_string($conn, $_POST['emailUser']); // Añadido para verificación

    // Verificar si el email ya existe para otro usuario
    $check_email = "SELECT idUser FROM user WHERE emailUser = ? AND idUser != ?";
    $stmt = mysqli_prepare($conn, $check_email);
    mysqli_stmt_bind_param($stmt, "si", $emailUser, $idUser);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_store_result($stmt);
    
    if (mysqli_stmt_num_rows($stmt) > 0) {
        // Email ya existe, redirigir a página de error
        header("Location: user_exists_error.php");
        exit();
    }
    // Iniciar transacción
    mysqli_begin_transaction($conn);

    try {
        // Actualizar datos del usuario
        $updateQuery = "UPDATE user SET 
                        nameUser = ?,
                        sNameUser = ?,
                        adminBool = ?
                        WHERE idUser = ?";
        
        $stmt = mysqli_prepare($conn, $updateQuery);
        mysqli_stmt_bind_param($stmt, "ssii", 
            $nameUser,
            $sNameUser,
            $adminBool,
            $idUser);
        
        if (!mysqli_stmt_execute($stmt)) {
            throw new Exception("Error al actualizar el usuario: " . mysqli_error($conn));
        }

        // Actualizar contraseña si se proporcionó
        if (!empty($_POST['password'])) {
            $password = md5($_POST['password']);
            $updatePassQuery = "UPDATE user SET passwordUser = ? WHERE idUser = ?";
            $stmt = mysqli_prepare($conn, $updatePassQuery);
            mysqli_stmt_bind_param($stmt, "si", $password, $idUser);
            
            if (!mysqli_stmt_execute($stmt)) {
                throw new Exception("Error al actualizar la contraseña: " . mysqli_error($conn));
            }
        }

        // Confirmar transacción
        mysqli_commit($conn);
        header("Location: users_panel.php?success=1");
        exit();
    } catch (Exception $e) {
        mysqli_rollback($conn);
        $error = $e->getMessage();
    }
}

include('../../components/header_footer.php');
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Usuario - SIGMARKET</title>
    <link rel="icon" href="<?php echo BASE_URL;?>/res/img/favicon_white.png"/>
    <link rel="stylesheet" href="<?php echo BASE_URL;?>/styles/global.css"/>
    <link rel="stylesheet" href="<?php echo BASE_URL;?>/styles/panels.css"/>
</head>
<body>
    <header class="header">
        <?php show_header(); ?>
    </header>
    
    <div class="breadcrumb">
        <span>Inicio > Centro de Cuentas > Usuarios > Editar</span>
    </div>

    <div class="account-container">
        <aside class="account-sidebar">
            <h3>Menú de Administración</h3>
            <ul>
                <li><a class="module" href="../panels/admin_panel.php">Dashboard</a></li>
                <li><a class="module" href="../products_module/prod_panel.php">Productos</a></li>
                <li><a class="module" href="users_panel.php">Usuarios</a></li>
            </ul>
        </aside>

        <main class="account-content">
            <div class="form-header">
                <h1>EDITAR USUARIO</h1>
                <a href="users_panel.php" class="back-btn">← Volver</a>
            </div>
            
            <?php if (isset($error)): ?>
                <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <form method="POST" class="product-form">
                <div class="form-row">
                    <div class="form-group">
                        <label for="nameUser">Nombre</label>
                        <input type="text" id="nameUser" name="nameUser" 
                               value="<?php echo htmlspecialchars($usuario['nameUser']); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="sNameUser">Apellido</label>
                        <input type="text" id="sNameUser" name="sNameUser" 
                               value="<?php echo htmlspecialchars($usuario['sNameUser']); ?>" required>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="emailUser">Email</label>
                    <input type="email" id="emailUser" name="emailUser" 
                           value="<?php echo htmlspecialchars($usuario['emailUser']); ?>" readonly>
                </div>
                
                <div class="form-group">
                    <label for="password">Nueva Contraseña (dejar en blanco para no cambiar)</label>
                    <input type="password" id="password" name="password" minlength="6">
                </div>
                
                <div class="form-group">
                    <label for="adminBool">Tipo de Usuario</label>
                    <select id="adminBool" name="adminBool" required>
                        <option value="0" <?php echo ($usuario['adminBool'] == 0) ? 'selected' : ''; ?>>Usuario Normal</option>
                        <option value="1" <?php echo ($usuario['adminBool'] == 1) ? 'selected' : ''; ?>>Administrador</option>
                    </select>
                </div>
                
                <button type="submit" class="btn btn-primary">Actualizar Usuario</button>
            </form>
        </main>
    </div>

    <footer class="footer">
        <?php show_footer(); ?>
    </footer>
</body>
</html>