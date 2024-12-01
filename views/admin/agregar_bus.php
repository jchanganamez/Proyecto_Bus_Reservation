<?php 
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$title = 'Agregar Bus';
include '../layouts/header.php'; 
require_once '../../controllers/BusController.php'; 
require_once '../../controllers/UserController.php'; // Controlador para obtener conductores

$busController = new BusController();
$userController = new UserController(); // Controlador para manejar usuarios
$message = '';

// Obtener la lista de conductores
$conductores = $busController->obtenerTodosLosConductores(); // Método para obtener conductores

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $modelo = $_POST['modelo'] ?? '';
    $numero = $_POST['numero'] ?? '';
    $capacidad = $_POST['capacidad'] ?? '';
    $conductor_id = $_POST['conductor_id'] ?? '';

    if (empty($modelo) || empty($numero) || empty($capacidad) || empty($conductor_id)) {
        $message = '<p class="text-red-500">Todos los campos son obligatorios.</p>';
    } else {
        $result = json_decode($busController->createBus(), true);
        if (isset($result['success']) && $result['success']) {
            $message = '<p class="text-green-500">Bus agregado exitosamente.</p>';
        } else {
            $message = '<p class="text-red-500">Error al agregar el Bus: ' . ($result['error'] ?? 'Desconocido') . '</p>';
        }
    }
}
?>
<body class="bg-gray-100">
    <div class="flex items-center mb-6">
        <a href="buses.php" class="flex items-center text-gray-600 hover:text-gray-800">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
            </svg>
            <span>Regresar</span>
        </a>
    </div>
    <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-100">
        <div class="container mx-auto px-6 py-8">
            <form method="POST" class="bg-white rounded-lg shadow-lg p-6">
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="modelo">Modelo</label>
                    <input type="text" name="modelo" id="modelo" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" placeholder="Modelo del bus">
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="numero">Placa</label>
                    <input type="text" name="numero" id="numero" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" placeholder="Placa del bus">
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="capacidad">Capacidad</label>
                    <input type="number" name="capacidad" id="capacidad" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" placeholder="Capacidad del bus">
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="conductor_id">Asignar Conductor</label>
                    <select name="conductor_id" id="conductor_id" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                        <option value="">Seleccionar conductor</option>
                        <?php foreach ($conductores as $conductor): ?>
                            <option value="<?= htmlspecialchars($conductor['id']) ?>"><?= htmlspecialchars($conductor['nombre']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="flex items-center justify-between">
                    <button style="background-color: #f59e0b; color: white; padding: 10px 20px; border: none; border-radius: 5px; font-weight: bold;">
                        Agregar Bus
                    </button>
                </div>
            </form>
            <?= $message ?>
        </div>
    </main>
    <?php include '../layouts/footer.php'; ?>
</body>
