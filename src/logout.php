<?php
//Reanudamos la sesión
session_start();
//Borramos todos los datos almacenados en la sesión
session_unset();
//Redirigimos al usuario al formulario de inicio de sesión
session_destroy();
header('Location: login.php');
exit();

