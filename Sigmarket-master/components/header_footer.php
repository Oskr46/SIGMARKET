<?php
    function show_header(){
?>
    <div class="header_content">
            <div class="logo">
<<<<<<< HEAD
                <a href="<?php echo BASE_URL; ?>"><img src="<?php echo BASE_URL; ?>res/img/logo_smkt.png" alt="Logo SIGMARKET"></a>
            </div>
            <a class="item" href="<?php echo BASE_URL; ?>components/products/">Productos</a>
            <a class="item" href="<?php echo BASE_URL; ?>sale/">En venta</a>
            <a class="item" href="<?php echo BASE_URL; ?>brands/">Marcas</a>
            <button class="search"></button>
            <input class="search_input" placeholder="Busca un Producto">
            <?php if(!isset($admin) && !isset($name) && !isset($email)): ?>
                <a class="sign_in" href="<?php echo BASE_URL; ?>pages/register_module/register_page.php">Registrate Ahora</a>
                <a class="sign_up" href="<?php echo BASE_URL; ?>pages/login_page.php">Iniciar Sesion</a>
            <?php else: ?>
                <a class="cart" href="<?php echo BASE_URL; ?>cart/">
                    <img src="<?php echo BASE_URL; ?>res/img/cart_icon.png" alt="Carrito de compras"/>
                </a>
                <a class="user_settings" href="<?php echo BASE_URL; ?>user/profile/">
                    <img src="<?php echo BASE_URL; ?>res/img/user_icon.png" alt="Perfil de usuario"/>
                </a>
            <?php endif; ?>
=======
                <a href="/"><img src="./res/img/logo_smkt.png"></img></a>
            </div>

            <a class="item" href="./components/products">Productos</a>
            
            <a class="item" href=''>En venta</a>
            
            <a class="item" href=''>Marcas</a>
            
            <button class="search"></button>
            
            <input class="search_input" placeholder="Busca un Producto"></input>
            
            <a class="sign_in" href="">Registrate Ahora</a>
            
            <a class="sign_up" href="">Iniciar Sesion</a>
>>>>>>> d0101d5 (Todos los archivos fuera)
        </div>
<?php 
}

<<<<<<< HEAD
function show_footer(){ ?>
        <div class="footer_content">
            <div class="logo_and_info">
                <img class="logo" src="<?php echo BASE_URL; ?>res/img/logo_smkt.png" alt="Logo de la tienda">
=======
    function show_footer(){?>
        <div class="footer_content">
            <div class="logo_and_info">
                <img class="logo" src="./res/img/logo_smkt.png" alt="Logo de la tienda"></img>
>>>>>>> d0101d5 (Todos los archivos fuera)
                <p class='info'>Tenemos prendas que se adaptan a tu estilo y que te enorgulleces de llevar. 
                Desde ropa de mujer hasta de hombre.</p>
            </div>

            <div class="other_items">
                <h2>AYUDA</h2>
<<<<<<< HEAD
                <a class="items_footer" href="<?php echo BASE_URL; ?>legal/terms/">Terminos y condiciones</a>
                <a class="items_footer" href="<?php echo BASE_URL; ?>legal/privacy/">Politica de Privacidad</a>
            </div>
            <div class="other_items">
                <h2>FAQ</h2>
                <a class="items_footer" href="<?php echo BASE_URL; ?>faq/account/">Cuenta</a>
                <a class="items_footer" href="<?php echo BASE_URL; ?>faq/orders/">Ordenes</a>
                <a class="items_footer" href="<?php echo BASE_URL; ?>faq/payments/">Pagos</a>
            </div>
        </div>
<?php
}
=======
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

>>>>>>> d0101d5 (Todos los archivos fuera)
?>