<?php 
ob_start();
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$title = 'Editar Usuario';
include '../layouts/header.php'; 
require_once '../../controllers/UserController.php';

$userController = new UserController();
$user = null;
$message = '';

// Validar si existe un ID en el parámetro GET
if (isset($_GET['id'])) {
    $userId = $_GET['id'];
    $user = json_decode($userController->getUserById($userId), true); // Obtener los datos del usuario
    if (!$user) {
        header('Location: gestion_usuarios.php?error=Usuario no encontrado');
        exit;
    }
} else {
    header('Location: users.php');
    exit;
}

// Manejo del formulario de edición
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['nombre'] ?? '';
    $email = $_POST['email'] ?? '';
    $telefono = $_POST['telefono'] ?? '';
    $es_admin = ($_POST['es_admin'] === '1') ? 1 : 0; // Asegura que el rol es 1 o 0

    $response = $userController->updateUser ($userId, $nombre, $email, $telefono, $es_admin);
    $responseArray = json_decode($response, true);

    if (isset($responseArray['success']) && $responseArray['success']) {
        // Redirigir a users.php después de la actualización exitosa
        header('Location: users.php?message=Usuario actualizado exitosamente');
        exit; // Asegúrate de llamar a exit después de header
    } elseif (isset($responseArray['error'])) {
        $message = '<p class="text-red-500">Error: ' . htmlspecialchars($responseArray['error']) . '</p>';
    }
    
    // Mostrar el mensaje
    if (!empty($message)) {
        echo $message;
    }
}
?>
<body class="bg-gray-100">
    <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-100">
        <div class="container mx-auto px-6 py-8">
        <form class="bg-white rounded-lg shadow-lg p-6" method="POST">
            <input type="hidden" name="id" value="<?= htmlspecialchars($user['id'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"> <!-- ID del usuario -->
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="nombre">Nombre</label>
                <input type="text" id="nombre" name="nombre" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" 
                    placeholder="Nombre del usuario" 
                    value="<?= htmlspecialchars($user['nombre'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" required>
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="email">Email</label>
                <input type="email" id="email" name="email" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" 
                    placeholder="Email del usuario" 
                    value="<?= htmlspecialchars($user['email'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" required>
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="telefono">Teléfono</label>
                <input type="text" id="telefono" name="telefono" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" 
                    placeholder="Teléfono del usuario" 
                    value="<?= htmlspecialchars($user['telefono'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" required>
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="es_admin">Rol</label>
                <select id="es_admin" name="es_admin" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    <option value="0" <?= ($user['es_admin'] == 0) ? 'selected' : ''; ?>>Usuario</option>
                    <option value="1" <?= ($user['es_admin'] == 1) ? 'selected' : ''; ?>>Administrador</option>
                </select>
            </div>
            <div class="flex items-center justify-between">
                <button style="background-color: #f59e0b; color: white; padding: 10px 20px; border: none; border-radius: 5px; font-weight: bold;">
                    Guardar Cambios
                </button>
            </div>
        </form>
        </div>
    </main>
    <?php include '../layouts/footer.php'; ?>
</body>
</html>
