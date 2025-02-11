<?php
session_start();

function obtenerPreferencia($clave, $valorPorDefecto) {
    return isset($_COOKIE[$clave]) ? $_COOKIE[$clave] : $valorPorDefecto;
}

// Verificar si el usuario está autenticado
if (!isset($_SESSION['usuario'])) {
    header('Location: login.php');
    exit();
}

// Obtener preferencias actuales
$idioma = obtenerPreferencia('idioma', 'es');
$estilo = obtenerPreferencia('estilo', 'claro');

// Procesar el formulario de preferencias
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nuevo_idioma = $_POST['idioma'];
    $nuevo_estilo = $_POST['estilo'];

    // Guardar nuevas preferencias en cookies
    setcookie('idioma', $nuevo_idioma, time() + (86400 * 30), "/");
    setcookie('estilo', $nuevo_estilo, time() + (86400 * 30), "/");

    // Actualizar variables para mostrar cambios inmediatos
    $idioma = $nuevo_idioma;
    $estilo = $nuevo_estilo;
}
?>

<!DOCTYPE html>
<html lang="<?php echo $idioma; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo ($idioma == 'es') ? 'Preferencias' : 'Preferences'; ?></title>
    <link rel="stylesheet" href="estilos.css">
</head>
<body class="<?php echo $estilo; ?>">
    <header>
        <h1><?php echo ($idioma == 'es') ? 'Preferencias' : 'Preferences'; ?></h1>
        <nav>
            <a href="index.php"><?php echo ($idioma == 'es') ? 'Volver a la Tienda' : 'Back to Store'; ?></a>
        </nav>
    </header>

    <main>
        <form method="post">
            <div>
                <label for="idioma"><?php echo ($idioma == 'es') ? 'Idioma:' : 'Language:'; ?></label>
                <select name="idioma" id="idioma">
                    <option value="es" <?php echo ($idioma == 'es') ? 'selected' : ''; ?>>Español</option>
                    <option value="en" <?php echo ($idioma == 'en') ? 'selected' : ''; ?>>English</option>
                </select>
            </div>
            <div>
                <label for="estilo"><?php echo ($idioma == 'es') ? 'Estilo:' : 'Style:'; ?></label>
                <select name="estilo" id="estilo">
                    <option value="claro" <?php echo ($estilo == 'claro') ? 'selected' : ''; ?>>
                        <?php echo ($idioma == 'es') ? 'Claro' : 'Light'; ?>
                    </option>
                    <option value="oscuro" <?php echo ($estilo == 'oscuro') ? 'selected' : ''; ?>>
                        <?php echo ($idioma == 'es') ? 'Oscuro' : 'Dark'; ?>
                    </option>
                </select>
            </div>
            <button type="submit"><?php echo ($idioma == 'es') ? 'Guardar Preferencias' : 'Save Preferences'; ?></button>
        </form>
    </main>

    <footer>
        <p>2024 - Tecno Canarias</p>
    </footer>
</body>
</html>

