<?php 
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$title = 'Gestión de Buses';
include '../layouts/header.php'; 
require_once '../../controllers/BusController.php';
require_once '../../controllers/DriverController.php'; // Asumo que tienes un controlador de conductores

$busController = new BusController();
$conductorController = new DriverController(); // Controlador para obtener la lista de conductores
$buses = $busController->obtenerTodosLosBuses(); // Sin json_decode
$conducores = $busController->obtenerTodosLosConductores(); // Obtén todos los conductores

// Manejo de operaciones CRUD
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Crear bus
    if (isset($_POST['action']) && $_POST['action'] === 'create') {
        $busController->createBus();
        $message = 'Bus Creado exitosamente';  // Llama al método createBus del controlador
    }

    // Actualizar bus
    if (isset($_POST['action']) && $_POST['action'] === 'update') {
        $busController->updateBus();
        $message = 'Bus Editado exitosamente'; // Llama al método updateBus del controlador
    }

    // Eliminar bus
    if (isset($_POST['action']) && $_POST['action'] === 'delete') {
        $busId = $_POST['id']; // Obtener el ID del bus a eliminar
        
        // Verificar si el bus tiene viajes asignados
        $hasTrips = $busController->busHasTrips($busId); // Método que verifica si el bus tiene viajes

        if ($hasTrips) {
            // Si el bus tiene viajes, no se puede eliminar
            $message = 'No se puede eliminar el bus porque tiene viajes asignados.';
        } else {
            // Si no tiene viajes, proceder a eliminar
            if ($busController->deleteBus($busId)) {
                $message = 'Bus eliminado exitosamente.';
            } else {
                $message = 'Error al eliminar el bus.';
            }
        }
    }
}

if (isset($_GET['message'])) {
    echo '<p class="text-green-500">' . htmlspecialchars($_GET['message']) . '</p>';
}
$buses = $busController->obtenerTodosLosBuses(); // Sin json_decode
?>
<body class="bg-gray-100">
    <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-100">
        <div class="container mx-auto px-6 py-8">
            <!-- Título y botón -->
            <div class="flex flex-col sm:flex-row justify-between items-center mb-4">
                <h2 class="text-3xl font-bold mb-4 sm:mb-0">Lista de Buses</h2>
                <button onclick="document.getElementById('create-bus-modal').classList.toggle('hidden')" 
                        class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700 flex items-center">
                    <i class="bi bi-plus-circle mr-2 text-white"></i> Nuevo Bus
                </button>
            </div>
            <!-- Mostrar mensaje de éxito o error -->
            <?php if (isset($message)): ?>
                <div id="message-alert" class="bg-green-500 text-white p-4 rounded mb-4" role="alert">
                    <?= htmlspecialchars($message) ?>
                </div>
            <?php endif; ?>

            <!-- Modal para agregar un nuevo bus -->
            <div id="create-bus-modal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 flex justify-center items-center">
                <div class="bg-white rounded-lg p-6">
                    <h3 class="text-lg font-semibold mb-4">Agregar Nuevo Bus</h3>
                    <form method="POST" action="">
                        <input type="hidden" name="action" value="create">
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="modelo">Modelo</label>
                            <input type="text" name="modelo" id="modelo" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" placeholder="Modelo del bus" required>
                        </div>
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="numero">Número de Placa</label>
                            <input type="text" name="numero" id="numero" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" placeholder="Número de Placa" required>
                        </div>
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="capacidad">Capacidad</label>
                            <input type="number" name="capacidad" id="capacidad" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" placeholder="Capacidad del bus" required>
                        </div>

                        <!-- Selección de conductor -->
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="conductor_id">Conductor</label>
                            <select name="conductor_id" id="conductor_id" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                <option value="">Seleccionar Conductor</option>
                                <?php foreach ($conducores as $conductor): ?>
                                    <option value="<?= $conductor['id'] ?>"><?= htmlspecialchars($conductor['nombre']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="flex items-center justify-between">
                            <button type="submit" class="bg-yellow-500 text-white px-4 py-2 rounded-md hover:bg-yellow-600 font-bold">
                                Agregar Bus
                            </button>
                            <button type="button" onclick="document.getElementById('create-bus-modal').classList.add('hidden')" class="text-red-500 hover:text-red-700">
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
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Modelo</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Placa</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Capacidad</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Conductor</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php foreach ($buses as $bus): ?>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap"><?= htmlspecialchars($bus['modelo']) ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap"><?= htmlspecialchars($bus['numero']) ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap"><?= htmlspecialchars($bus['capacidad']) ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap"><?= htmlspecialchars($bus['conductor'] ?? 'Sin asignar') ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <a href="editar_bus.php?id=<?= $bus['id'] ?>" class="text-amber-600 hover:text-amber-900 mr-3">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="" method="POST" style="display:inline;">
                                            <input type="hidden" name="id" value="<?= $bus['id'] ?>">
                                            <input type="hidden" name="action" value="delete">
                                            <button class="text-red-600 hover:text-red-900" type="submit" onclick="return confirm('¿Estás seguro de que deseas eliminar este bus?');">
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
