<?php
// Iniciar la sesión
session_start();

// Verificar si las variables de sesión existen
if (!isset($_SESSION['nombre']) || !isset($_SESSION['apellido']) || !isset($_SESSION['tipo'])) {
    header("location: index.php");
}

$tipo_usuario = $_SESSION['tipo'];   
$usuario = $_SESSION['user'];
$nombre = $_SESSION['nombre'];
$apellido = $_SESSION['apellido'];

if($tipo_usuario!=1){
    session_destroy();
    header("location: index.php");
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
        <h1>Registro de Usuarios</h1>
        <form id="registroForm" action="proceso_insertar_usuario.php" method="POST" onsubmit="return validarFormulario()">
            <div class="form-group">
                <label for="email" class="required-field">Email</label>
                <input type="email" id="email_nuevo_usuario" name="email_nuevo_usuario" required>
                <div class="error-message" id="email-error">Por favor ingrese un email válido</div>
            </div>

            <div class="form-group">
                <label for="password" class="required-field">Contraseña</label>
                <input type="text" id="password_nuevo_usuario" name="password_nuevo_usuario" required>
                <div class="error-message" id="email-error">Por favor ingrese un email válido</div>
            </div>
            
            <div class="form-group">
                <label for="nombres" class="required-field">Nombres</label>
                <input type="text" id="nombres_nuevo_usuario" name="nombres_nuevo_usuario" required>
                <div class="error-message" id="nombres-error">Por favor ingrese sus nombres</div>
            </div>
            
            <div class="form-group">
                <label for="apellidos" class="required-field">Apellidos</label>
                <input type="text" id="apellidos_nuevo_usuario" name="apellidos_nuevo_usuario" required>
                <div class="error-message" id="apellidos-error">Por favor ingrese sus apellidos</div>
            </div>
            
            <div class="form-group">
                <label for="tipo_usuario" class="required-field">Tipo de usuario</label>
                <select id="tipo_usuario_nuevo_usuario" name="tipo_usuario_nuevo_usuario" required>
                    <option value="">Seleccione un tipo</option>
                    <option value="1">Administrador</option>
                    <option value="2">Usuario</option>
                </select>
                <div class="error-message" id="tipo-error">Por favor seleccione un tipo de usuario</div>
            </div>
            
            <button type="submit">Procesar registro</button>
            
        </form>

        <p><a href="admin.php">Ir al panel de administración</a></p>
    
        <p><a href="cerrar_sesion.php">Cerrar sesión</a></p>
    </div>

    <script>
        function validarFormulario() {
            let valido = true;
            const email = document.getElementById('email');
            const nombres = document.getElementById('nombres');
            const apellidos = document.getElementById('apellidos');
            const tipoUsuario = document.getElementById('tipo_usuario');
            
            // Validar email
            if (!email.value || !email.validity.valid) {
                document.getElementById('email-error').style.display = 'block';
                valido = false;
            } else {
                document.getElementById('email-error').style.display = 'none';
            }
            
            // Validar nombres
            if (!nombres.value.trim()) {
                document.getElementById('nombres-error').style.display = 'block';
                valido = false;
            } else {
                document.getElementById('nombres-error').style.display = 'none';
            }
            
            // Validar apellidos
            if (!apellidos.value.trim()) {
                document.getElementById('apellidos-error').style.display = 'block';
                valido = false;
            } else {
                document.getElementById('apellidos-error').style.display = 'none';
            }
            
            // Validar tipo de usuario
            if (!tipoUsuario.value) {
                document.getElementById('tipo-error').style.display = 'block';
                valido = false;
            } else {
                document.getElementById('tipo-error').style.display = 'none';
            }
            
            return valido;
        }
    </script>
</body>
</html>