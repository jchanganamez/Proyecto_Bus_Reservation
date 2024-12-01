<?php 
ob_start();
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$title = 'Editar Conductor';
include '../layouts/header.php'; 
require_once '../../controllers/DriverController.php';

$driverController = new DriverController();
$driver = null;
$message = '';

// Validar si existe un ID en el parámetro GET
if (isset($_GET['id'])) {
    $driverId = $_GET['id'];
    $driver = json_decode($driverController->getDriverById($driverId), true); // Obtener los datos del conductor
    if (!$driver) {
        header('Location: gestion_conductores.php?error=Conductor no encontrado');
        exit;
    }
} else {
    header('Location: conductores.php');
    exit;
}

// Manejo del formulario de edición
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['nombre'] ?? '';
    $licencia = $_POST['licencia'] ?? '';
    $telefono = $_POST['telefono'] ?? '';

    $response = $driverController->updateDriver($driverId, $nombre, $licencia, $telefono);
    $responseArray = json_decode($response, true);

    if (isset($responseArray['success']) && $responseArray['success']) {
        // Redirigir a conductores.php después de la actualización exitosa
        header('Location: conductores.php?message=Conductor actualizado exitosamente');
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
            <input type="hidden" name="id" value="<?= htmlspecialchars($driver['id'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"> <!-- ID del conductor -->
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="nombre">Nombre</label>
                <input type="text" id="nombre" name="nombre" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" 
                    placeholder="Nombre del conductor" 
                    value="<?= htmlspecialchars($driver['nombre'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" required>
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="licencia">Licencia</label>
                <input type="text" id="licencia" name="licencia" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" 
                    placeholder="Licencia del conductor" 
                    value="<?= htmlspecialchars($driver['licencia'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" required>
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="telefono">Teléfono</label>
                <input type="text" id="telefono" name="telefono" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" 
                    placeholder="Teléfono del conductor" 
                    value="<?= htmlspecialchars($driver['telefono'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" required>
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
