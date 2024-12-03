<?php 
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

require_once '../../config/database.php'; 
require_once '../../controllers/TripController.php'; 

$database = new Database();
$conn = $database->getConnection();
$tripController = new TripController();

$trip = null;
$message = '';

// Validar si existe un ID en el parámetro GET
if (isset($_GET['id'])) {
    $tripId = $_GET['id'];
    $trip = $tripController->getTripById($tripId);    
    if (!$trip) {
        header('Location: viajes.php?error=Viaje no encontrado');
        exit;
    }
} else {
    header('Location: viajes.php');
    exit;
}

// Manejo del formulario de edición
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtener los datos del formulario
    $origen = $_POST['origen'] ?? '';
    $destino = $_POST['destino'] ?? '';
    $fecha_salida = $_POST['fecha_salida'] ?? '';
    $fecha_llegada = $_POST['fecha_llegada'] ?? '';
    $bus_id = $_POST['bus_id'] ?? '';
    $precio = $_POST['precio'] ?? '';

    // Actualizar el viaje
    $response = $tripController->updateTrip($tripId, $origen, $destino, $fecha_salida, $fecha_llegada, $bus_id, $precio);
    
    if ($response) {
        header('Location: viajes.php?message=Viaje actualizado exitosamente');
        exit;
    } else {
        $message = '<p class="text-red-500">Error al actualizar el viaje.</p>';
    }
}

$title = 'Editar Viaje';
include '../layouts/header.php'; 
?>

<body class="bg-gray-100">
    <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-100">
        <div class="container mx-auto px-6 py-8">
            <h1 class="text-2xl font-semibold mb-6">Editar Viaje</h1>
            <?= $message ?>
            <form method="POST" class="bg-white rounded-lg shadow-lg p-6">
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="origen">Origen</label>
                    <input type="text" name="origen" id="origen" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" 
                        value="<?= htmlspecialchars($trip['origen'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" required>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="destino">Destino</label>
                    <input type="text" name="destino" id="destino" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" 
                        value="<?= htmlspecialchars($trip['destino'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" required>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="fecha_salida">Fecha de Salida</label>
                    <input type="datetime-local" name="fecha_salida" id="fecha_salida" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" 
                        value="<?= htmlspecialchars($trip['fecha_salida'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" required>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="fecha_llegada">Fecha de Llegada</label>
                    <input type="datetime-local" name="fecha_llegada" id="fecha_llegada" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" 
                        value="<?= htmlspecialchars($trip['fecha _llegada'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" required>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="bus_id">ID del Bus</label>
                    <input type="text" name="bus_id" id="bus_id" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" 
                        value="<?= htmlspecialchars($trip['bus_id'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" required>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="precio">Precio</label>
                    <input type="number" name="precio" id="precio" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" 
                        value="<?= htmlspecialchars($trip['precio'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" required>
                </div>
                <div class="flex items-center justify-between">
                    <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">Actualizar Viaje</button>
                </div>
            </form>
        </div>
    </main>
</body>

<?php include '../layouts/footer.php'; ?> 