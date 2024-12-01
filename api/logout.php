<?php
session_start(); // Inicia la sesión

// Destruir todas las variables de sesión
$_SESSION = [];

// Si se desea, destruir la sesión
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"], $params["secure"], $params["httponly"]
    );
}

// Finalmente, destruir la sesión
session_destroy();

// Redirigir al usuario a la página de inicio o inicio de sesión
header("Location: ../views/login.html"); // Cambia la ruta según tu estructura de archivos
exit();
?>