<?php
ob_start();
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$title = 'Editar Bus';
include '../layouts/header.php'; 
require_once '../../controllers/BusController.php';

$busController = new BusController();
$bus = null;
$message = '';

// Validar si existe un ID en el parámetro GET
if (isset($_GET['id'])) {
    $busId = $_GET['id'];
    $bus = json_decode($busController->obtenerBus($busId), true);    
    $conductores = $busController->obtenerTodosLosConductores();

    if (!$bus || !$conductores) {
        header('Location: buses.php?error=Bus o conductores no encontrados');
        exit;
    }
} else {
    header('Location: buses.php');
    exit;
}

// Manejo del formulario de edición
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $numero = $_POST['numero'] ?? '';
    $capacidad = $_POST['capacidad'] ?? '';
    $modelo = $_POST['modelo'] ?? '';
    $conductor_id = $_POST['conductor_id'] ?? ''; 

    $response = $busController->updateBus($busId, $numero, $capacidad, $modelo, $conductor_id);
    $responseArray = json_decode($response, true);

    if (isset($responseArray['success']) && $responseArray['success']) {
        // Redirigir a buses.php después de la actualización exitosa
        header('Location: buses.php?message=Bus actualizado exitosamente');
        exit;
    } elseif (isset($responseArray['error'])) {
        $message = '<p class="text-red-500">Error: ' . htmlspecialchars($responseArray['error']) . '</p>';
    }
}
?>
<body class="bg-gray-100">
    <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-100">
        <div class="container mx-auto px-6 py-8">
            <form method="POST" class="bg-white rounded-lg shadow-lg p-6">
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="modelo">Modelo</label>
                    <input type="text" name="modelo" id="modelo" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" placeholder="Modelo del bus" value="<?= htmlspecialchars($bus['modelo']) ?>">
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="numero">Placa</label>
                    <input type="text" name="numero" id="numero" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" placeholder="Placa del bus" value="<?= htmlspecialchars($bus['numero']) ?>">
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="capacidad">Capacidad</label>
                    <input type="number" name="capacidad" id="capacidad" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" placeholder="Capacidad del bus" value="<?= htmlspecialchars($bus['capacidad']) ?>">
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="conductor_id">Conductor</label>
                    <select name="conductor_id" id="conductor_id" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                        <option value="">Seleccionar conductor</option>
                        <?php foreach ($conductores as $conductor): ?>
                            <option value="<?= htmlspecialchars($conductor['id']) ?>" <?= $bus['conductor_id'] == $conductor['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($conductor['nombre']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="flex items-center justify-between">
                    <button style="background-color: #f59e0b; color: white; padding: 10px 20px; border: none; border-radius: 5px; font-weight: bold;">
                        Guardar Cambios
                    </button>
                </div>
            </form>
            <?= $message ?>
        </div>
    </main>
</body>
</html>
