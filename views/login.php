<?php
session_start();
require_once '../config/database.php'; // Asegúrate de que la ruta sea correcta
require_once '../models/User.php';

// Crear una nueva conexión a la base de datos
$database = new Database();
$db = $database->getConnection();
if (!$db) {
    die("Error de conexión a la base de datos.");
}
$user = new User($db);
$message = ''; // Inicializar la variable aquí

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    // Verificar que no haya campos vacíos
    if (empty($email) || empty($password)) {
        $message = "Por favor, ingrese su correo electrónico y contraseña.";
    } else {
        // Utilizar el método login del modelo User
        $result = $user->login($email, $password);

        // Verificar el resultado del login
        if (is_array($result)) {
            // Credenciales correctas
            $_SESSION['user_id'] = $result['id'];
            $_SESSION['nombre'] = $result['nombre']; // Asegúrate de que 'nombre' se esté configurando
            $_SESSION['es_admin'] = $result['es_admin']; // Asegúrate de que 'es_admin' se esté configurando

            // Redirigir según el rol del usuario
            if ($result['es_admin'] == 1) {
                header('Location: ../views/home.php');
            } else {
                header('Location: ../views/home.php');
            }
            exit();
        } elseif ($result === 'PASSWORD_INCORRECT') {
            $message = "La contraseña ingresada es incorrecta.";
        } elseif ($result === 'EMAIL_NOT_FOUND') {
            $message = "El correo electrónico no está registrado.";
        }
    }
}

include 'login.html';
?>