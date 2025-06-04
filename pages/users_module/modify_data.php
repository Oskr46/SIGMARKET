<?php
// Iniciar la sesión
session_start();

include('../../components/conexion.php');

$emailUser = $_SESSION['email']; // Email del usuario desde la sesión

// Obtener datos del usuario basado en el email de la sesión
$conn = connectDB();
mysqli_set_charset($conn, "utf8");

$query = "SELECT idUser, nameUser, sNameUser, emailUser, passwordUser FROM user WHERE emailUser = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "s", $emailUser);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$usuario = mysqli_fetch_assoc($result);

if (!$usuario) {
    header("location: ../panels/user_panel.php");
    exit();
}

$error = '';
$success = '';

// Procesar el formulario de actualización
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Recoger y sanitizar datos
    $nameUser = mysqli_real_escape_string($conn, $_POST['nameUser']);
    $sNameUser = mysqli_real_escape_string($conn, $_POST['sNameUser']);
    $actualPassword = $_POST['actualPassword'] ?? '';
    $newPassword = $_POST['password'] ?? '';

    // Verificar si se está intentando cambiar la contraseña
    if (!empty($newPassword)) {
        if (empty($actualPassword)) {
            $error = "Debe ingresar la contraseña actual para cambiar la contraseña";
        } else {
            // Verificar contraseña actual
            $hashedActualPassword = md5($actualPassword);
            if ($hashedActualPassword != $usuario['passwordUser']) {
                $error = "La contraseña actual es incorrecta";
            }
        }
    }

    if (empty($error)) {
        // Iniciar transacción
        mysqli_begin_transaction($conn);

        try {
            // Actualizar datos básicos del usuario
            $updateQuery = "UPDATE user SET 
                          nameUser = ?,
                          sNameUser = ?
                          WHERE emailUser = ?";
            
            $stmt = mysqli_prepare($conn, $updateQuery);
            mysqli_stmt_bind_param($stmt, "sss", 
                $nameUser,
                $sNameUser,
                $emailUser);
            
            if (!mysqli_stmt_execute($stmt)) {
                throw new Exception("Error al actualizar el usuario: " . mysqli_error($conn));
            }

            // Actualizar contraseña si se proporcionó y se verificó
            if (!empty($newPassword) && empty($error)) {
                $password = md5($newPassword);
                $updatePassQuery = "UPDATE user SET passwordUser = ? WHERE emailUser = ?";
                $stmt = mysqli_prepare($conn, $updatePassQuery);
                mysqli_stmt_bind_param($stmt, "ss", $password, $emailUser);
                
                if (!mysqli_stmt_execute($stmt)) {
                    throw new Exception("Error al actualizar la contraseña: " . mysqli_error($conn));
                }
            }

            // Confirmar transacción
            mysqli_commit($conn);
            $_SESSION['success'] = "Usuario actualizado correctamente";
            header("Location: ../panels/user_panel.php");
            exit();
        } catch (Exception $e) {
            mysqli_rollback($conn);
            $error = $e->getMessage();
        }
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
                <li><a class="module" href="../panels/user_panel.php">Usuarios</a></li>
            </ul>
        </aside>

        <main class="account-content">
            <div class="form-header">
                <h1>EDITAR USUARIO: <?php echo htmlspecialchars($usuario['emailUser']); ?></h1>
                <a href="../panels/user_panel.php" class="back-btn">← Volver</a>
            </div>
            
            <?php if (!empty($error)): ?>
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
                    <label for="actualPassword">Contraseña Actual (requerida para cambios)</label>
                    <input type="password" id="actualPassword" name="actualPassword">
                </div>

                <div class="form-group">
                    <label for="password">Nueva Contraseña (dejar en blanco para no cambiar)</label>
                    <input type="password" id="password" name="password" minlength="6">
                    <small class="form-text">Mínimo 6 caracteres</small>
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