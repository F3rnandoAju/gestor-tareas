<?php
session_start(); // iniciar sesión

// destruir todas las variables de sesión
$_SESSION = [];

// destruir la sesión en el servidor
session_destroy();

// redirigir al login
header("Location: login.php");
exit();
?>
