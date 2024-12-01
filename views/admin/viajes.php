<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$title = 'Gestión de Viajes';
include '../layouts/header.php'; 
require_once '../../controllers/TripController.php';
require_once '../../models/Trip.php';
require_once '../../config/database.php'; // Asegúrate de que la ruta sea correcta
require_once '../../controllers/BusController.php';


$database = new Database();
$conn = $database->getConnection();

$busController = new BusController();
$tripController = new TripController();
$trip = new Trip($conn);
$buses = $busController->obtenerTodosLosBuses(); // Sin json_decode
$trips = $tripController->getAllTrips(); // Obtener todos los viajes
$busModels = $tripController->getBusModels();
$drivers = $tripController->getDrivers();
$ciudades = $conn->query("SELECT DISTINCT nombre_ciudad FROM destinos ORDER BY nombre_ciudad")->fetchAll(PDO::FETCH_ASSOC);

// Manejo de operaciones CRUD
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Crear viaje
    if (isset($_POST['action']) && $_POST['action'] === 'create') {
        $tripController->createTrip(); // Llama al método createTrip del controlador
        $message = 'Viaje creado exitosamente'; // Mensaje de éxito
    }

    // Actualizar viaje
    if (isset($_POST['action']) && $_POST['action'] === 'update') {
        $tripController->updateTrip(); // Llama al método updateTrip del controlador
        $message = 'Viaje actualizado exitosamente'; // Mensaje de éxito
    }

    // Eliminar viaje
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete') {
        $id = $_POST['id']; // Obtener el ID del viaje
        if ($tripController->deleteTrip($id)) {
            $message = 'Viaje eliminado exitosamente';
        } else {
            $message = 'Error al eliminar el viaje';
        }
    }
}

// Mostrar mensaje de éxito o error
if (isset($message)) {
    echo '<p class="text-green-500">' . htmlspecialchars($message) . '</p>';
}
?>

<body class="bg-gray-100">
    <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-100">
        <div class="container mx-auto px-6 py-8">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-3xl font-bold mb-6">Lista de Viajes</h2>
                <button onclick="window.location.href='agregar_viaje.php'" 
                        class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700 flex items-center">
                    <i class="bi bi-plus-circle mr-2 text-white"></i> Nuevo Viaje
                </button>
            </div>

            <!-- Mostrar mensaje de éxito o error -->
            <?php if (isset($message)): ?>
                <div id="message-alert" class="bg-green-500 text-white p-4 rounded mb-4" role="alert">
                    <?= htmlspecialchars($message) ?>
                </div>
            <?php endif; ?>

            <!-- Modal para crear nuevo viaje -->
            <div id="create-trip-modal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 flex justify-center items-center p-4">
                <div class="bg-white rounded-lg w-full max-w-md max-h-screen p-6 shadow-lg overflow-y-auto relative">
                    <!-- Botón para cerrar el modal -->
                    <button 
                        class="absolute top-2 right-2 text-gray-600 hover:text-gray-900" 
                        onclick="document.getElementById('create-trip-modal').classList.add('hidden')">
                        &times;
                    </button>

                    <h3 class="text-lg font-semibold mb-4 text-center">Agregar Nuevo Viaje</h3>
                    <form method="POST" action="">
                        <input type="hidden" name="action" value="create">
                        
                        <!-- Selección de ciudades -->
                        <div class="grid grid-cols-1 gap-4">
                            <div class="relative">
                            <label for="origen" class="block text-sm font-medium text-gray-700 mb-2">Ciudad de partida:</label>
                                <select id="origen" name="origen" required onchange="updateDestinoOptions()" class="block w-full h-12 rounded-lg border border-gray-300 bg-gray-50 text-gray-700 text-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 transition-all">
                                    <option value="">Selecciona una ciudad</option>
                                    <?php foreach ($ciudades as $ciudad): ?>
                                        <option value="<?php echo htmlspecialchars($ciudad['nombre_ciudad']); ?>">
                                            <?php echo htmlspecialchars($ciudad['nombre_ciudad']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="relative">
                                <label for="destino" class="block text-sm font-medium text-gray-700 mb-2">Ciudad de destino:</label>
                                <select id="destino" name="destino" required class="block w-full h-12 rounded-lg border border-gray-300 bg-gray-50 text-gray-700 text-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 transition-all">
                                    <option value="">Selecciona una ciudad</option>
                                    <?php foreach ($ciudades as $ciudad): ?>
                                        <option value="<?php echo htmlspecialchars($ciudad['nombre_ciudad']); ?>">
                                            <?php echo htmlspecialchars($ciudad['nombre_ciudad']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        
                        <!-- Otros campos del formulario -->
                        <div class="grid grid-cols-1 gap-4 mt-4">
                            <div>
                                <label for="fecha_salida" class="block text-sm font-medium text-gray-700 mb-2">Fecha de Salida:</label>
                                <input type="datetime-local" id="fecha_salida" name="fecha_salida" class="w-full h-12 p-2 border border-gray-300 rounded-lg" required>
                            </div>
                            
                            <div>
                                <label for="fecha_llegada" class="block text-sm font-medium text-gray-700 mb-2">Fecha de Llegada:</label>
                                <input type="datetime-local" id="fecha_llegada" name="fecha_llegada" class="w-full h-12 p-2 border border-gray-300 rounded-lg">
                            </div>

                            <div>
                            <label for="bus_id" class="block text-sm font-medium text-gray-700 mb-2">Bus:</label>
                                <select id="bus_id" name="bus_id" required>
                                    <option value="">Selecciona un bus</option>
                                    <?php foreach ($buses as $bus): ?>
                                        <option value="<?= htmlspecialchars($bus['id']) ?>"><?= htmlspecialchars($bus['modelo']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                        </div>
                            
                            <div>
                                <label for="precio" class="block text-sm font-medium text-gray-700 mb-2">Precio:</label>
                                <input type="number" step="0.01" id="precio" name="precio" class="w-full h-12 p-2 border border-gray-300 rounded-lg" required>
                            </div>
                        </div>
                        
                        <!-- Botones -->
                        <div class="flex justify-end mt-6 space-x-2">
                            <button type="button" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600"
                                    onclick="document.getElementById('create-trip-modal').classList.add('hidden')">Cancelar</button>
                            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Agregar</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Tabla de viajes -->
            <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Origen</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Destino</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha de Salida</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha de Llegada</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Modelo del Bus</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Conductor</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Precio</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                    </tr>
                </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php foreach ($trips as $trip): ?>
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap"><?= htmlspecialchars($trip['origen']) ?></td>
                                <td class="px-6 py-4 whitespace-nowrap"><?= htmlspecialchars($trip['destino']) ?></td>
                                <td class="px-6 py-4 whitespace-nowrap"><?= htmlspecialchars($trip['fecha_salida']) ?></td>
                                <td class="px-6 py-4 whitespace-nowrap"><?= htmlspecialchars($trip['fecha_llegada']) ?></td>
                                <td class="px-6 py-4 whitespace-nowrap"><?= htmlspecialchars($trip['bus_modelo']) ?></td>
                                <td class="px-6 py-4 whitespace-nowrap"><?= htmlspecialchars($trip['conductor_nombre']) ?></td>
                                <td class="px-6 py-4 whitespace-nowrap">$<?= number_format($trip['precio'], 2) ?></td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <a href="editar_viaje.php?id=<?= $trip['id'] ?>" class="text-amber-600 hover:text-amber-900 mr-3">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="" method="POST" style="display:inline;">
                                        <input type="hidden" name="id" value="<?= $trip['id'] ?>">
                                        <input type="hidden" name="action" value="delete">
                                        <button class="text-red-600 hover:text-red-900" type="submit" onclick="return confirm('¿Estás seguro de que deseas eliminar este viaje?');">
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
    </main>
</body>
<script>
   function updateDestinoOptions() {
    const origen = document.getElementById("origen").value;
    const destinoSelect = document.getElementById("destino");
    
    // Restablecer las opciones del destino
    destinoSelect.innerHTML = '<option value="">Selecciona una ciudad</option>';

    // Filtrar las ciudades que no sean la ciudad de origen
    const ciudades = <?php echo json_encode($ciudades); ?>;
    ciudades.forEach(ciudad => {
        if (ciudad.nombre_ciudad !== origen) {
            const option = document.createElement("option");
            option.value = ciudad.nombre_ciudad;
            option.textContent = ciudad.nombre_ciudad;
            destinoSelect.appendChild(option);
        }
    });
}
</script>
