<?php 
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$title = 'Agregar Usuario';
include '../layouts/header.php'; 
require_once '../../controllers/UserController.php'; 

$userController = new UserController();
$message = '';

// Manejo del formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['nombre'] ?? '';
    $email = $_POST['email'] ?? '';
    $telefono = $_POST['telefono'] ?? '';
    $role = $_POST['role'] ?? 'user';
    $_POST['password'] = 'asd'; // Contraseña predeterminada
    $_POST['action'] = 'create'; 

    if (empty($nombre) || empty($email) || empty($telefono)) {
        $message = '<p class="text-red-500">Todos los campos son obligatorios.</p>';
    } else {
        $result = json_decode($userController->register(), true);
        if (isset($result['success']) && $result['success']) {
            $message = '<p class="text-green-500">Usuario agregado exitosamente.</p>';
        } else {
            $message = '<p class="text-red-500">Error al agregar el usuario: ' . ($result['error'] ?? 'Desconocido') . '</p>';
        }
    }
}
?>

<body class="bg-gray-100">
    <div class="flex items-center mb-6">
        <a href="users.php" class="flex items-center text-gray-600 hover:text-gray-800">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
            </svg>
            <span>Regresar</span>
        </a>
    </div>
    <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-100">
        <div class="container mx-auto px-6 py-8">
            <form method="POST" action="" class="bg-white rounded-lg shadow-lg p-6">
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="nombre">Nombre</label>
                    <input type="text" name="nombre" id="nombre" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" placeholder="Nombre del usuario" value="">
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="email">Email</label>
                    <input type="email" name="email" id="email" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" placeholder="Email del usuario" value="">
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="telefono">Teléfono</label>
                    <input type="text" name="telefono" id="telefono" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" placeholder="Teléfono del usuario" value="">
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="role">Rol</label>
                    <select name="role" id="role" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                        <option value="user">Usuario</option>
                        <option value="admin">Administrador</option>
                    </select>
                </div>
                <div class="flex items-center justify-between">
                    <button style="background-color: #f59e0b; color: white; padding: 10px 20px; border: none; border-radius: 5px; font-weight: bold;">
                        Agregar Usuario
                    </button>
                </div>
            </form>
            <?= $message ?> <!-- Muestra mensaje de éxito o error -->
        </div>
    </main>
    <?php include '../layouts/footer.php'; ?>
</body>
