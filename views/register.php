<?php
session_start();

// Incluir la configuración de la base de datos y el modelo de usuario
require_once '../config/database.php'; // Asegúrate de que la ruta sea correcta
require_once '../models/User.php';

// Crear una nueva conexión a la base de datos
$database = new Database();
$db = $database->getConnection();
if (!$db) {
    die("Error de conexión a la base de datos.");
}

// Crear una nueva instancia de la clase User
$user = new User($db);

// Inicializar variables para almacenar los mensajes
$message = '';

// Registro de usuario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user->nombre = $_POST['nombre'] ?? '';
    $user->email = $_POST['email'] ?? '';
    $user->password = $_POST['password'] ?? '';

    // Validar campos
    if (empty($user->nombre) || empty($user ->email) || empty($user->password)) {
        $message = "Por favor, complete todos los campos.";
        echo $user->nombre;
        echo $user->email;
        echo $user->password;
    } else {
        // Verificar si el correo ya está registrado
        if ($user->emailExists()) {
            $message = "El correo electrónico ya está registrado.";
        } else {
            // Registrar al usuario
            if ($user->create()) {
                $message = "Registro exitoso. Puedes iniciar sesión ahora.";
                // Redirigir a la página de inicio de sesión
                header('Location: login.html');
                exit();
            } else {
                $message = "Error al registrar el usuario. Inténtalo de nuevo.";
            }
        }
    }
}
echo $message;
// Incluir el formulario de registro
include 'register.html';
?>