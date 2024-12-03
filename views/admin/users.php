<?php 
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$title = 'Gestión de Usuarios';
include '../layouts/header.php'; 
require_once '../../controllers/UserController.php';
$userController = new UserController();
$users = json_decode($userController->getAllUsers(), true);

// Manejo de operaciones CRUD
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Crear usuario
    if (isset($_POST['action']) && $_POST['action'] === 'create') {
        $userController->register(); // Llama al método register del controlador
        $message = 'Usuario creado exitosamente'; // Mensaje de éxito
    }

    // Actualizar usuario
    if (isset($_POST['action']) && $_POST['action'] === 'update') {
        $userController->updateUser (); // Llama al método updateUser del controlador
        $message = 'Usuario actualizado exitosamente'; // Mensaje de éxito
    }

    // Eliminar usuario
    if (isset($_POST['action']) && $_POST['action'] === 'delete') {
        $userId = $_POST['id']; // Obtener el ID del usuario a eliminar
        
        // Verificar si el usuario tiene viajes realizados
        $hasTrips = $userController->userHasTrips($userId); // Método que debes implementar

        if ($hasTrips) {
            // Mostrar un mensaje de advertencia
            echo "<script>
                if (confirm('Este usuario tiene viajes registrados. ¿Está seguro de que desea eliminarlo y sus registros relacionados?')) {
                    // Si el usuario confirma, se procede a eliminar
                    document.getElementById('delete-form').submit();
                }
            </script>";
            if ($userController->deleteUser ($userId)) {
                $message = 'Usuario y sus registros relacionados eliminados exitosamente.';
            } else {
                $message = 'Error al eliminar el usuario y sus registros relacionados.';
            }
        } else {
            // Si no tiene viajes, proceder a eliminar directamente
            if ($userController->deleteUserSimple($userId)) {
                $message = 'Usuario eliminado exitosamente de la tabla usuarios.';
            } else {
                $message = 'Error al eliminar el usuario de la tabla usuarios.';
            }
        }
    }
}
$users = json_decode($userController->getAllUsers(), true);
?>

<body class="bg-gray-100">
    <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-100">
        <div class="container mx-auto px-6 py-8">
            <!-- Título y botón -->
            <div class="flex flex-col sm:flex-row justify-between items-center mb-4">
                <h2 class="text-3xl font-bold mb-4 sm:mb-0">Lista de Usuarios</h2>
                <button onclick="document.getElementById('create-user-modal').classList.toggle('hidden')" 
                        class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700 flex items-center">
                    <i class="bi bi-plus-circle mr-2 text-white"></i> Nuevo Usuario
                </button>
            </div>

            <!-- Mostrar mensaje de éxito o error -->
            <?php if (isset($message)): ?>
                <div id="message-alert" class="bg-green-500 text-white p-4 rounded mb-4" role="alert">
                    <?= htmlspecialchars($message) ?>
                </div>
            <?php endif; ?>


            <!-- Modal para agregar un nuevo usuario -->
            <div id="create-user-modal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 flex justify-center items-center">
                <div class="bg-white rounded-lg p-6">
                    <h3 class="text-lg font-semibold mb-4">Agregar Nuevo Usuario</h3>
                    <form method="POST" action="">
                        <input type="hidden" name="action" value="create">
                        
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="nombre">Nombre</label>
                            <input type="text" name="nombre" id="nombre" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" placeholder="Nombre del usuario" required>
                        </div>
                        
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="email">Email</label>
                            <input type="email" name="email" id="email" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" placeholder="Email del usuario" required>
                        </div>

                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="telefono">Teléfono</label>
                            <input type="text" name="telefono" id="telefono" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" placeholder="Teléfono del usuario" required>
                        </div>

                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="rol">Rol</label>
                            <select name="es_admin" id="rol" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                                <option value="0">Usuario</option>
                                <option value="1">Administrador</option>
                            </select>
                        </div>

                        <div class="flex items-center justify-between">
                            <button type="submit" class="bg-yellow-500 text-white px-4 py-2 rounded-md hover:bg-yellow-600 font-bold">
                                Agregar Usuario
                            </button>
                            <button type="button" onclick="document.getElementById('create-user-modal').classList.add('hidden')" class="text-red-500 hover:text-red-700">
                                Cancelar
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Tabla -->
            <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nombre</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Teléfono</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rol</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php foreach ($users as $user): ?>
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap"><?= htmlspecialchars($user['nombre']) ?></td>
                                <td class="px-6 py-4 whitespace-nowrap"><?= htmlspecialchars($user['email']) ?></td>
                                <td class="px-6 py-4 whitespace-nowrap"><?= htmlspecialchars($user['telefono']) ?></td>
                                <td class="px-6 py-4 whitespace-nowrap"><?= $user['es_admin'] ? 'Admin' : 'Usuario' ?></td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <a href="editar_usuario.php?id=<?= $user['id'] ?>" class="text-amber-600 hover:text-amber-900 mr-3">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="" method="POST" style="display:inline;">
                                        <input type="hidden" name="id" value="<?= $user['id'] ?>">
                                        <input type="hidden" name="action" value="delete">
                                        <button class="text-red-600 hover:text-red-900" type="submit" onclick="return confirm('¿Estás seguro de que deseas eliminar este usuario?');">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>
    <?php include '../layouts/footer.php'; ?>
</body>
<script>
    // Verifica si existe el mensaje
    window.addEventListener('DOMContentLoaded', (event) => {
        const messageAlert = document.getElementById('message-alert');
        if (messageAlert) {
            // Desaparece el mensaje después de 5 segundos (5000 milisegundos)
            setTimeout(() => {
                messageAlert.style.opacity = 0;
                messageAlert.style.transition = 'opacity 1s';
                
                // Después de que el mensaje desaparezca completamente, lo eliminamos del DOM
                setTimeout(() => {
                    messageAlert.style.display = 'none';
                }, 1000); // espera 1 segundo para el desvanecimiento
            }, 5000); // 5 segundos antes de que empiece a desvanecerse
        }
    });
</script>
</html>
