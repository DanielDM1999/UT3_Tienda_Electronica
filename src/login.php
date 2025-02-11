<?php
session_start();

function obtenerPreferencia($clave, $valorPorDefecto)
{
    return isset($_COOKIE[$clave]) ? $_COOKIE[$clave] : $valorPorDefecto;
}

// Obtener preferencias del usuario
$idioma = obtenerPreferencia('idioma', 'es');
$estilo = obtenerPreferencia('estilo', 'claro');

// Procesar el formulario de inicio de sesión
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = $_POST['usuario'];
    $contrasena = $_POST['contrasena'];

    if ($usuario === 'admin' && $contrasena === '1234') {
        $_SESSION['usuario'] = 'admin';
        $_SESSION['mensaje_bienvenida'] = ($idioma == 'es') ? '¡Bienvenido, Administrador!' : 'Welcome, Administrator!';
    } else {
        $_SESSION['usuario'] = 'normal';
        $_SESSION['nombre_usuario'] = $usuario;
        $_SESSION['mensaje_bienvenida'] = ($idioma == 'es') ? "¡Bienvenido, $usuario!" : "Welcome, $usuario!";
    }
    header('Location: index.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="<?php echo $idioma; ?>">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo ($idioma == 'es') ? 'Iniciar Sesión' : 'Login'; ?></title>
    <link rel="stylesheet" href="estilos.css">
</head>

<body class="<?php echo $estilo; ?>">
    <header>
        <h1><?php echo ($idioma == 'es') ? 'Iniciar Sesión' : 'Login'; ?></h1>
    </header>

    <main>
        <form id="form-login" method="post">
            <div>
                <label for="usuario"><?php echo ($idioma == 'es') ? 'Usuario:' : 'Username:'; ?></label>
                <input type="text" id="usuario" name="usuario" required>
            </div>
            <div>
                <label for="contrasena"><?php echo ($idioma == 'es') ? 'Contraseña:' : 'Password:'; ?></label>
                <input type="password" id="contrasena" name="contrasena" required>
            </div>
            <button type="submit"><?php echo ($idioma == 'es') ? 'Iniciar Sesión' : 'Login'; ?></button>
        </form>

    </main>

    <?php
    if (isset($_SESSION['mensaje_bienvenida'])) {
        echo '<p class="mensaje-bienvenida">' . $_SESSION['mensaje_bienvenida'] . '</p>';
        unset($_SESSION['mensaje_bienvenida']);
    }
    ?>

    <footer>
        <p>2024 - Tecno Canarias</p>
    </footer>
</body>

</html>