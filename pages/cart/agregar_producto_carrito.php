<?php
include('../../components/conexion.php');
if (isset($_POST['add_to_cart'])) {
    $idUser = $_POST['idUser'];
    $idProduct = $_POST['idProduct'];
    $quantity = $_POST['quantity'] ?? 1;
    $status = 1; // Por defecto, el producto en el carrito está activo (1)
    
    $conn = connectDB();
    // Verificar si el usuario existe
    $checkUser = $conn->prepare("SELECT idUser FROM user WHERE idUser = ?");
    $checkUser->bind_param("i", $idUser);
    $checkUser->execute();
    $checkUser->store_result();
    
    if ($checkUser->num_rows > 0) {
        // Verificar si el producto ya está en el carrito del usuario
        $checkCart = $conn->prepare("SELECT idCart, quantity FROM cart WHERE idUser = ? AND idProduct = ? AND status = 1");
        $checkCart->bind_param("ii", $idUser, $idProduct);
        $checkCart->execute();
        $cartResult = $checkCart->get_result();
        
        if ($cartResult->num_rows > 0) {
            // Actualizar cantidad si el producto ya está en el carrito
            $cartItem = $cartResult->fetch_assoc();
            $newQuantity = $cartItem['quantity'] + $quantity;
            $updateStmt = $conn->prepare("UPDATE cart SET quantity = ? WHERE idCart = ?");
            $updateStmt->bind_param("ii", $newQuantity, $cartItem['idCart']);
            
            if ($updateStmt->execute()) {
                $_SESSION['message'] = "Cantidad actualizada en el carrito correctamente!";
            } else {
                $_SESSION['error'] = "Error al actualizar el carrito: " . $conn->error;
            }
            $updateStmt->close();
        } else {
            // Insertar nuevo item en el carrito
            $insertStmt = $conn->prepare("INSERT INTO cart (idUser, idProduct, quantity, status) VALUES (?, ?, ?, ?)");
            $insertStmt->bind_param("iiii", $idUser, $idProduct, $quantity, $status);
            
            if ($insertStmt->execute()) {
                $_SESSION['message'] = "Producto agregado al carrito correctamente!";
            } else {
                $_SESSION['error'] = "Error al agregar el producto al carrito: " . $conn->error;
            }
            $insertStmt->close();
        }
        $checkCart->close();
    } else {
        $_SESSION['error'] = "El usuario no existe";
    }
    $checkUser->close();
    
    // Redirigir de vuelta a la página del producto
    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit();
}
?>