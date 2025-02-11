<?php
session_start();

function obtenerPreferencia($clave, $valorPorDefecto)
{
    return isset($_COOKIE[$clave]) ? $_COOKIE[$clave] : $valorPorDefecto;
}

function agregarAlCarrito($producto_id)
{
    if (!isset($_COOKIE['carrito'])) {
        //Si no existe la cookie del carrito, creamos el carrito como un array vacío
        $carrito = [];
    } else {
        //En caso contrario, decodificamos el carrito
        $carrito = json_decode($_COOKIE['carrito'], true);
    }

    if (isset($carrito[$producto_id])) {
        //Si el producto ya existe en el carrito, aumenta su cantidad
        $carrito[$producto_id]++;
    } else {
        //En caso contrario, lo añade al carrito
        $carrito[$producto_id] = 1;
    }

    setcookie('carrito', json_encode($carrito), time() + (86400 * 30));
}

// Verificar si el usuario está autenticado
if (!isset($_SESSION['usuario'])) {
    header('Location: login.php');
    exit();
}

// Obtener preferencias del usuario
$idioma = obtenerPreferencia('idioma', 'es');
$estilo = obtenerPreferencia('estilo', 'claro');

// Lista de productos con nombres en español e inglés
$productos = [
    ['nombre_es' => 'Cargador Solar Portátil', 'nombre_en' => 'Portable Solar Charger', 'precio' => 41.95, 'imagen' => 'img/cargador-solar.jpg'],
    ['nombre_es' => 'Auriculares Bluetooth con Cancelación de Ruido', 'nombre_en' => 'Bluetooth Headphones with Noise Cancellation', 'precio' => 73.00, 'imagen' => 'img/auriculares.webp'],
    ['nombre_es' => 'Mini Proyector Portátil HD', 'nombre_en' => 'Portable HD Projector', 'precio' => 110.99, 'imagen' => 'img/proyector.jpg'],
    ['nombre_es' => 'Altavoz Bluetooth Inalámbrico con Sonido 3D', 'nombre_en' => 'Wireless Bluetooth Speaker with 3D Sound', 'precio' => 45.99, 'imagen' => 'img/altavoz.jpg'],
    ['nombre_es' => 'Teclado Mecánico RGB Compacto', 'nombre_en' => 'Compact RGB Mechanical Keyboard', 'precio' => 69.95, 'imagen' => 'img/teclado.jpg'],
    ['nombre_es' => 'Cámara de Seguridad Wi-Fi', 'nombre_en' => 'Wi-Fi Security Camera', 'precio' => 50.00, 'imagen' => 'img/camara.jpg'],
];

// Agregar producto al carrito
if (isset($_POST['agregar_producto'])) {
    $producto_id = $_POST['producto_id'];
    agregarAlCarrito($producto_id);
}
?>

<!DOCTYPE html>
<html lang="<?php echo $idioma; ?>">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tecno Canarias</title>
    <link rel="stylesheet" href="estilos.css">
</head>

<body class="<?php echo $estilo; ?>">
    <header>
        <h1>Tecno Canarias</h1>
        <nav>
            <a href="preferencias.php"><?php echo ($idioma == 'es') ? 'Preferencias' : 'Preferences'; ?></a>
            <a href="logout.php"><?php echo ($idioma == 'es') ? 'Cerrar Sesión' : 'Logout'; ?></a>
        </nav>
    </header>

    <main>
        <?php
        // Mostrar mensaje de bienvenida si existe
        if (isset($_SESSION['mensaje_bienvenida'])) {
            echo '<p class="mensaje-bienvenida">' . htmlspecialchars($_SESSION['mensaje_bienvenida']) . '</p>';
            unset($_SESSION['mensaje_bienvenida']);
        }
        ?>
        <h2><?php echo ($idioma == 'es') ? 'Productos Disponibles' : 'Available Products'; ?></h2>
        <div class="productos">
            <?php foreach ($productos as $id => $producto): ?>
                <div class="producto">
                    <img src="<?php echo $producto['imagen']; ?>" alt="<?php echo ($idioma == 'es') ? $producto['nombre_es'] : $producto['nombre_en']; ?>"
                        class="producto-imagen">
                    <h3><?php echo ($idioma == 'es') ? $producto['nombre_es'] : $producto['nombre_en']; ?></h3>
                    <p><?php echo ($idioma == 'es') ? 'Precio: ' : 'Price: '; ?><?php echo number_format($producto['precio'], 2); ?>€</p>
                    <form method="post">
                        <input type="hidden" name="producto_id" value="<?php echo $id; ?>">
                        <button type="submit" name="agregar_producto">
                            <?php echo ($idioma == 'es') ? 'Agregar al Carrito' : 'Add to Cart'; ?>
                        </button>
                    </form>
                </div>
            <?php endforeach; ?>
        </div>
        <div class="carrito-button">
            <a href="carrito.php" class="button">
                <?php echo ($idioma == 'es') ? 'Ver Carrito' : 'View Cart'; ?>
            </a>
        </div>
    </main>

    <footer>
        <p>2024 - Tecno Canarias</p>
    </footer>
</body>

</html>
