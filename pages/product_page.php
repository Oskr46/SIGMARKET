<?php include('../components/header_footer.php');
include('../components/conexion.php');

$conn = connectDB();
mysqli_set_charset($conn, "utf8");

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Obtener parámetros de filtro
$categoria = $_GET['category'] ?? null;
$color = $_GET['color'] ?? null;

// Construir consulta SQL con filtros
$q = "SELECT * FROM products WHERE 1=1";
$params = [];

if ($categoria && $categoria != 'todas') {
    $q .= " AND categoryProduct = ?";
    $params[] = $categoria;
}

if ($color && $color != 'todos') {
    $q .= " AND colorProduct = ?";
    $params[] = $color;
}

// Preparar y ejecutar consulta
$stmt = mysqli_prepare($conn, $q);

if ($params) {
    $types = str_repeat('s', count($params)); // Tipos de parámetros (todos son strings)
    mysqli_stmt_bind_param($stmt, $types, ...$params);
}

mysqli_stmt_execute($stmt);
$consulta = mysqli_stmt_get_result($stmt);

// Obtener categorías únicas para el filtro
$categorias_query = mysqli_query($conn, "SELECT DISTINCT categoryProduct FROM products");
$categorias = [];
while ($row = mysqli_fetch_assoc($categorias_query)) {
    $categorias[] = $row['categoryProduct'];
}

// Obtener colores únicos para el filtro
$colores_query = mysqli_query($conn, "SELECT DISTINCT colorProduct FROM products WHERE colorProduct IS NOT NULL AND colorProduct != ''");
$colores = [];
while ($row = mysqli_fetch_assoc($colores_query)) {
    $colores[] = $row['colorProduct'];
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Catálogo - SIGMARKET</title>
    <link rel="icon" href="../res/img/favicon_white.png"/>
    <link rel="stylesheet" href="../styles/global.css"/>
    <link rel="stylesheet" href="../styles/catalogue.css"/>
    <style>
        .header {
            position: fixed;
            top: 0;
            width: 100%;
            z-index: 1000;
            background: white;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        
        main {
            padding-top: 120px;
            min-height: calc(100vh - 120px);
            display: flex;
        }
        
        .filters {
            width: 250px;
            padding: 20px;
            background: #f8f9fa;
            border-right: 1px solid #dee2e6;
        }
        
        .product-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr); /* Tres columnas */
            gap: 20px; /* Espaciado entre los elementos */
            padding: 20px;
        }
        
        .product-card {
            border: 1px solid #ddd;
            padding: 15px;
            border-radius: 8px;
            background: white;
            transition: transform 0.3s;
        }
        
        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        .product-image {
            width: 100%;
            height: 200px;
            object-fit: contain;
            margin-bottom: 10px;
        }
        
        .product-title {
            font-size: 1.1rem;
            margin-bottom: 5px;
            color: #333;
        }
        
        .product-price {
            font-weight: bold;
            color:rgb(0, 0, 0);
            margin-bottom: 5px;
        }
        
        .product-stock {
            color:rgb(109, 109, 109);
            font-size: 0.9rem;
            margin-bottom: 5px;
        }
        
        .product-category {
            display: inline-block;
            background: #f0f0f0;
            padding: 2px 8px;
            border-radius: 10px;
            font-size: 0.8rem;
            color: #555;
        }
        
        .filter-section {
            margin-bottom: 20px;
        }
        
        .filter-section h3 {
            margin-bottom: 10px;
            color: #333;
        }
        
        .filter-option {
            margin-bottom: 8px;
        }
        
        .filter-option label {
            margin-left: 5px;
            cursor: pointer;
        }
        
        .apply-filters {
            background:rgb(0, 0, 0);
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 50px;
            cursor: pointer;
            margin-top: 10px;
        }
        
        .apply-filters:hover {
            background:rgb(0, 0, 0);
        }
    </style>
</head>
<body>
    <header class="header">
        <?php show_header(); ?>
    </header>
    
    <main class="product-page">
        <aside class="filters">
            <form method="GET" action="">
                <section class="filter-section">
                    <h2>Filtros</h2>
                    
                    <!-- Filtro de categorías -->
                    <h3>Categorías</h3>
                    <div class="filter-option">
                        <input type="radio" name="category" id="cat_todas" value="todas" 
                               <?= (!$categoria || $categoria == 'todas') ? 'checked' : '' ?>>
                        <label for="cat_todas">Todas las categorías</label>
                    </div>
                    
                    <?php foreach ($categorias as $cat): ?>
                        <div class="filter-option">
                            <input type="radio" name="category" id="cat_<?= htmlspecialchars($cat) ?>" 
                                   value="<?= htmlspecialchars($cat) ?>" 
                                   <?= ($categoria === $cat) ? 'checked' : '' ?>>
                            <label for="cat_<?= htmlspecialchars($cat) ?>"><?= htmlspecialchars($cat) ?></label>
                        </div>
                    <?php endforeach; ?>
                </section>

                <!-- Filtro de colores -->
                <section class="filter-section">
                    <h3>Colores</h3>
                    <div class="filter-option">
                        <input type="radio" name="color" id="color_todos" value="todos" 
                               <?= (!isset($_GET['color']) || $_GET['color'] == 'todos') ? 'checked' : '' ?>>
                        <label for="color_todos">Todos los colores</label>
                    </div>
                    
                    <?php foreach ($colores as $color): ?>
                        <div class="filter-option" style="display: inline-block; margin-right: 10px;">
                            <input type="radio" name="color" id="color_<?= htmlspecialchars($color) ?>" 
                                   value="<?= htmlspecialchars($color) ?>" 
                                   <?= (isset($_GET['color']) && $_GET['color'] === $color) ? 'checked' : '' ?>>
                            <label for="color_<?= htmlspecialchars($color) ?>" style="cursor: pointer;">
                                <span style="display: inline-block; width: 20px; height: 20px; background: <?= htmlspecialchars($color) ?>; border-radius: 50%; border: 1px solid #ddd;"></span>
                            </label>
                        </div>
                    <?php endforeach; ?>
                </section>

                <button type="submit" class="apply-filters">Aplicar Filtros</button>
                <a href="?" class="apply-filters" style="background: #6c757d; font-size: 0.8rem; display: inline-block; text-decoration: none; margin-left: 2px;">Limpiar</a>
            </form>
        </aside>

        <section class="product-grid">
            <?php if (mysqli_num_rows($consulta) > 0): ?>
                <?php while($fila = mysqli_fetch_assoc($consulta)): ?>
                    <div class="product-card">
                        <?php if (!empty($fila['idProduct'])): ?>
                            <img src="../res/img/products/<?= htmlspecialchars($fila['idProduct']) ?>" 
                                 alt="<?= htmlspecialchars($fila['nameProduct']) ?>" 
                                 class="product-image">
                        <?php else: ?>
                            <div class="product-image" style="background: #f0f0f0; display: flex; align-items: center; justify-content: center;">
                                <span>Sin imagen</span>
                            </div>
                        <?php endif; ?>
                        
                        <h3 class="product-title"><?= htmlspecialchars($fila['nameProduct']) ?></h3>
                        <p class="product-price">$<?= number_format($fila['priceProduct'], 2) ?></p>
                        <p class="product-stock">Disponibles: <?= htmlspecialchars($fila['quantityProduct']) ?></p>
                        <p class="product-category"><?= htmlspecialchars($fila['categoryProduct']) ?></p>
                        
                        <?php if (!empty($fila['descriptionProduct'])): ?>
                            <p style="font-size: 0.9rem; color: #666; margin-top: 10px;">
                                <?= htmlspecialchars($fila['descriptionProduct']) ?>
                            </p>
                        <?php endif; ?>
                        
                        <?php if (!empty($fila['colorProduct'])): ?>
                            <div style="margin-top: 10px;">
                                <span style="display: inline-block; width: 15px; height: 15px; background: <?= htmlspecialchars($fila['colorProduct']) ?>; border-radius: 50%; border: 1px solid #ddd;"></span>
                                <span style="margin-left: 5px; font-size: 0.8rem;"><?= htmlspecialchars($fila['colorProduct']) ?></span>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p style="grid-column: 1 / -1; text-align: center; padding: 20px;">
                    No se encontraron productos con los filtros seleccionados.
                </p>
            <?php endif; ?>
        </section>
    </main>

    <footer class="footer">
        <?php show_footer(); ?>
    </footer>
</body>
</html>