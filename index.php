<?php
session_start();
echo "User  ID: " . ($_SESSION['user_id'] ?? 'No user logged in');

// Redirigir a la página de inicio o login según el estado de la sesión
if (isset($_SESSION['user_id'])) {
    // Si el usuario está logueado, redirigir según su rol
    if ($_SESSION['user_role'] === 'ADMIN') {
        header('Location: views/admin/dashboard.html');
    } else {
        header('Location: views/home.php');
    }
} else {
    // Si no está logueado, redirigir al login
    header('Location: views/login.php');
}
exit();
?>