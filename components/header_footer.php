<?php
    session_start();
    require('./styles/global.css');
    
    if(isset($_SESSION['admin']) && isset($_SESSION['name']) && isset($_SESSION['email'])){
        $admin = $_SESSION['admin'];
        $name = $_SESSION['name'];
        $email = $_SESSION['email']; 
    }
    
    function show_header(){
?>
    <div class="header_content">
            <div class="logo">
                <a href="/"><img src="./res/img/logo_smkt.png"></img></a>
            </div>
            <a class="item" href="./components/products">Productos</a>
            <a class="item" href=''>En venta</a>
            <a class="item" href=''>Marcas</a>
            <button class="search"></button>
            <input class="search_input" placeholder="Busca un Producto"></input>
            <?php if(isset($admin) && !isset($name) && !isset($email)){?>
            <a class="sign_in" href="">Registrate Ahora</a>
            
            <a class="sign_up" href="">Iniciar Sesion</a>
            <?php }
            else{?>
            <a class="cart" href=""><img src="../res/img/cart_icon.png"/></a>
            <a class="user_settings" href=""><img src="../res/img/user_icon.png"/></a>
            <?php }?>
        </div>
<?php 
}

    function show_footer(){?>
        <div class="footer_content">
            <div class="logo_and_info">
                <img class="logo" src="./res/img/logo_smkt.png" alt="Logo de la tienda"></img>
                <p class='info'>Tenemos prendas que se adaptan a tu estilo y que te enorgulleces de llevar. 
                Desde ropa de mujer hasta de hombre.</p>
            </div>

            <div class="other_items">
                <h2>AYUDA</h2>
                <a class="items_footer" href=''>Terminos y condiciones</a>
                <a class="items_footer" href=''>Politica de Privacidad</a>
            </div>
            <div class="other_items">
                <h2>FAQ</h2>
                <a class="items_footer" href=''>Cuenta</a>
                <a class="items_footer" href=''>Ordenes</a>
                <a class="items_footer" href=''>Pagos</a>
            </div>
        </div>
    <?php
    }

?>