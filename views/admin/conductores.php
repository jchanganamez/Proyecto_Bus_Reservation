<?php 
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$title = 'Gestión de Conductores';
include '../layouts/header.php'; 
require_once '../../controllers/DriverController.php';

$driverController = new DriverController();
$drivers = json_decode($driverController->readDrivers(), true);

// Manejo de operaciones CRUD
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Crear conductor
    if (isset($_POST['action']) && $_POST['action'] === 'create') {
        $driverController->createDriver(); // Llama al método createDriver del controlador
        $message = 'Conductor creado exitosamente'; // Mensaje de éxito
    }

    // Actualizar conductor
    if (isset($_POST['action']) && $_POST['action'] === 'update') {
        $driverController->updateDriver(); // Llama al método updateDriver del controlador
        $message = 'Conductor actualizado exitosamente'; // Mensaje de éxito
    }

    // Eliminar conductor
    if (isset($_POST['action']) && $_POST['action'] === 'delete' && isset($_POST['id'])) {
        $id = $_POST['id']; // Obtener el ID del conductor
        $driverController->deleteDriver($id);
        $message = 'Conductor eliminado exitosamente'; // Mensaje de éxito 
    }    
}

// Mostrar mensaje de éxito o error
if (isset($message)) {
    echo '<p class="text-green-500">' . htmlspecialchars($message) . '</p>';
}

$drivers = json_decode($driverController->readDrivers(), true);
?>

<body class="bg-gray-100">
    <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-100">
        <div class="container mx-auto px-6 py-8">
            <!-- Título y botón para agregar conductor -->
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-3xl font-bold mb-6">Lista de Conductores</h2>
                <button onclick="document.getElementById('create-driver-modal').classList.toggle('hidden')" 
                        class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700 flex items-center">
                    <i class="bi bi-plus-circle mr-2 text-white"></i> Nuevo Conductor
                </button>
            </div>

            <!-- Mostrar mensaje de éxito o error -->
            <?php if (isset($message)): ?>
                <div id="message-alert" class="bg-green-500 text-white p-4 rounded mb-4" role="alert">
                    <?= htmlspecialchars($message) ?>
                </div>
            <?php endif; ?>

            <!-- Modal para crear nuevo conductor -->
            <div id="create-driver-modal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 flex justify-center items-center">
                <div class="bg-white rounded-lg p-6">
                    <h3 class="text-lg font-semibold mb-4">Agregar Nuevo Conductor</h3>
                    <form method="POST" action="">
                        <input type="hidden" name="action" value="create">
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="nombre">Nombre</label>
                            <input type="text" name="nombre" id="nombre" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" placeholder="Nombre del conductor" required>
                        </div>
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="licencia">Licencia</label>
                            <input type="text" name="licencia" id="licencia" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" placeholder="Licencia del conductor" required>
                        </div>
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="telefono">Teléfono</label>
                            <input type="text" name="telefono" id="telefono" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" placeholder="Teléfono del conductor" required>
                        </div>
                        <div class="flex items-center justify-between">
                            <button style="background-color: #f59e0b; color: white; padding: 10px 20px; border: none; border-radius: 5px; font-weight: bold;">
                                Agregar Conductor
                            </button>
                            <button type="button" onclick="document.getElementById('create-driver-modal').classList.add('hidden')" class="text-red-500 hover:text-red-700">
                                Cancelar
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Tabla de conductores -->
            <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nombre</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Licencia</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Teléfono</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php foreach ($drivers as $driver): ?>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap"><?= htmlspecialchars($driver['nombre']) ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap"><?= htmlspecialchars($driver['licencia']) ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap"><?= htmlspecialchars($driver['telefono']) ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <a href="editar_conductor.php?id=<?= $driver['id'] ?>" class="text-amber-600 hover:text-amber-900 mr-3">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="" method="POST" style="display:inline;">
                                            <input type="hidden" name="id" value="<?= $driver['id'] ?>"> <!-- Aseguramos que el ID se pase -->
                                            <input type="hidden" name="action" value="delete">
                                            <button class="text-red-600 hover:text-red-900" type="submit" onclick="return confirm('¿Estás seguro de que deseas eliminar este conductor?');">
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
