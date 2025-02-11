<?php
session_start();

function obtenerPreferencia($clave, $valorPorDefecto) {
    return isset($_COOKIE[$clave]) ? $_COOKIE[$clave] : $valorPorDefecto;
}

function obtenerCarrito() {
    return isset($_COOKIE['carrito']) ? json_decode($_COOKIE['carrito'], true) : [];
}

function calcularTotalCarrito($carrito, $productos): float {
    $total = 0;
    foreach ($carrito as $id => $cantidad) {
        $total += $productos[$id]['precio'] * $cantidad;
    }
    return $total;
}

function eliminarProductoCarrito($producto_id) {
    if (isset($_COOKIE['carrito'])) {
        $carrito = json_decode($_COOKIE['carrito'], true);
        unset($carrito[$producto_id]);
        setcookie('carrito', json_encode($carrito), time() + (86400 * 30));
    }
}

function vaciarCarrito() {
    setcookie('carrito', '', time() - 3600); // Elimina la cookie del carrito
}

// Verificar si el usuario está autenticado
if (!isset($_SESSION['usuario'])) {
    header('Location: login.php');
    exit();
}

// Obtener preferencias del usuario
$idioma = obtenerPreferencia('idioma', 'es');
$estilo = obtenerPreferencia('estilo', 'claro');

// Array de productos con nombres y descripciones en español e inglés
$productos = [
    ['nombre_es' => 'Cargador Solar Portátil', 'nombre_en' => 'Portable Solar Charger', 'precio' => 41.95, 'imagen' => 'img/cargador-solar.jpg'],
    ['nombre_es' => 'Auriculares Bluetooth con Cancelación de Ruido', 'nombre_en' => 'Bluetooth Headphones with Noise Cancellation', 'precio' => 73.00, 'imagen' => 'img/auriculares.webp'],
    ['nombre_es' => 'Mini Proyector Portátil HD', 'nombre_en' => 'Portable HD Projector', 'precio' => 110.99, 'imagen' => 'img/proyector.jpg'],
    ['nombre_es' => 'Altavoz Bluetooth Inalámbrico con Sonido 3D', 'nombre_en' => 'Wireless Bluetooth Speaker with 3D Sound', 'precio' => 45.99, 'imagen' => 'img/altavoz.jpg'],
    ['nombre_es' => 'Teclado Mecánico RGB Compacto', 'nombre_en' => 'Compact RGB Mechanical Keyboard', 'precio' => 69.95, 'imagen' => 'img/teclado.jpg'],
    ['nombre_es' => 'Cámara de Seguridad Wi-Fi', 'nombre_en' => 'Wi-Fi Security Camera', 'precio' => 50.00, 'imagen' => 'img/camara.jpg'],
];

$carrito = obtenerCarrito();
$total = calcularTotalCarrito($carrito, $productos);

// Manejar la eliminación de un producto específico
if (isset($_POST['eliminar_producto'])) {
    $producto_id = $_POST['producto_id'];
    eliminarProductoCarrito($producto_id);
    header("Location: carrito.php"); // Recarga la página para reflejar el cambio
    exit();
}

// Manejar el vaciado del carrito
if (isset($_POST['vaciar_carrito'])) {
    vaciarCarrito();
    header("Location: carrito.php"); // Recarga la página para reflejar el cambio
    exit();
}

// Manejar la compra
if (isset($_POST['realizar_compra'])) {
    // Mensaje de compra según el idioma
    if ($idioma == 'es') {
        $mensaje_compra = "Compra realizada con éxito.\n";
        $mensaje_compra .= "Importe total: " . number_format($total, 2) . "€\n";
        $mensaje_compra .= "Fecha de compra: " . date('d/m/Y H:i:s', time() - 3600);  // Se resta una hora ya que se ha decidido utilizar la zona horaria a UTC/GMT +00:00
    } else {
        // Mensaje en inglés
        $mensaje_compra = "Purchase successfully completed.\n";
        $mensaje_compra .= "Total amount: " . number_format($total, 2) . "€\n";
        $mensaje_compra .= "Purchase date: " . date('m/d/Y h:i:s A', time() - 3600);  // Ajusta la fecha para el formato en inglés
    }

    // Guardar el mensaje en la sesión
    $_SESSION['mensaje_compra'] = $mensaje_compra;

    // Vaciar el carrito tras la compra
    vaciarCarrito();
    header("Location: carrito.php"); // Redirigir para mostrar el mensaje de compra realizada
    exit();
}

?>

<!DOCTYPE html>
<html lang="<?php echo $idioma; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo ($idioma == 'es') ? 'Carrito de Compras' : 'Shopping Cart'; ?></title>
    <link rel="stylesheet" href="estilos.css">
</head>
<body class="<?php echo $estilo; ?>">
    <header>
        <h1><?php echo ($idioma == 'es') ? 'Carrito de Compras' : 'Shopping Cart'; ?></h1>
    </header>

    <main>
        <?php if (isset($_SESSION['mensaje_compra'])): ?>
            <p class="mensaje-compra"><?php echo nl2br(htmlspecialchars($_SESSION['mensaje_compra'])); ?></p>
            <?php unset($_SESSION['mensaje_compra']); ?>
        <?php elseif (empty($carrito)): ?>
            <p class="carrito-vacio">
                <?php echo ($idioma == 'es') ? 'El carrito está vacío.' : 'The cart is empty.'; ?>
            </p>
        <?php else: ?>
            <p>
                <?php 
                $cantidadTotal = array_sum($carrito);
                echo ($idioma == 'es') ? "Cantidad total de productos: $cantidadTotal" : "Total number of products: $cantidadTotal"; 
                ?>
            </p>
            <table>
                <thead>
                    <tr>
                        <th><?php echo ($idioma == 'es') ? 'Producto' : 'Product'; ?></th>
                        <th><?php echo ($idioma == 'es') ? 'Cantidad' : 'Quantity'; ?></th>
                        <th><?php echo ($idioma == 'es') ? 'Precio' : 'Price'; ?></th>
                        <th><?php echo ($idioma == 'es') ? 'Subtotal' : 'Subtotal'; ?></th>
                        <th><?php echo ($idioma == 'es') ? 'Eliminar' : 'Remove'; ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Bucle que muestra todos los productos del carrito
                    foreach ($carrito as $id => $cantidad): ?>
                        <tr>
                            <td><?php echo $idioma === 'es' ? $productos[$id]['nombre_es'] : $productos[$id]['nombre_en']; ?></td>
                            <td><?php echo $cantidad; ?></td>
                            <td><?php echo number_format($productos[$id]['precio'], 2); ?>€</td>
                            <td><?php echo number_format($productos[$id]['precio'] * $cantidad, 2); ?>€</td>
                            <td>
                                <!-- Formulario para eliminar productos individuales del carrito -->
                                <form method="post" style="display:inline;">
                                    <input type="hidden" name="producto_id" value="<?php echo $id; ?>">
                                    <button type="submit" name="eliminar_producto"><?php echo ($idioma == 'es') ? 'Eliminar' : 'Remove'; ?></button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="4"><?php echo ($idioma == 'es') ? 'Total' : 'Total'; ?></td>
                        <td><?php echo number_format($total, 2); ?>€</td>
                    </tr>
                </tfoot>
            </table>
            <div class="vaciar-carrito-button">
                <form method="post">
                    <button type="submit" name="vaciar_carrito"><?php echo ($idioma == 'es') ? 'Vaciar Carrito' : 'Empty Cart'; ?></button>
                </form>
            </div>

            <!-- Botón para realizar la compra -->
            <div class="realizar-compra-button" style="text-align: center;">
                <form method="post">
                    <button type="submit" name="realizar_compra"><?php echo ($idioma == 'es') ? 'Realizar Compra' : 'Make Purchase'; ?></button>
                </form>
            </div>
        <?php endif; ?>

        <!-- Botón para volver a la tienda -->
        <div class="volver-button">
            <a href="index.php" class="button">
                <?php echo ($idioma == 'es') ? 'Volver a la Tienda' : 'Back to Store'; ?>
            </a>
        </div>
    </main>

    <footer>
        <p>2024 - Tienda Online</p>
    </footer>
</body>
</html>