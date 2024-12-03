<?php 
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

require_once '../config/database.php'; // Asegúrate de que la ruta sea correcta

// Crear conexión a la base de datos
$database = new Database();
$db = $database->getConnection();
$userId = $_SESSION['user_id'];

$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'cancel') {
    $tripId = $_POST['trip_id'];

    // Aquí debes añadir la lógica para cancelar el viaje
    $queryCancel = "UPDATE viajes SET estado = 'Cancelado' WHERE id = :trip_id AND user_id = :user_id";
    $stmtCancel = $db->prepare($queryCancel);
    $stmtCancel->bindParam(':trip_id', $tripId);
    $stmtCancel->bindParam(':user_id', $userId);
    
    if ($stmtCancel->execute()) {
        // Redirigir o mostrar un mensaje de éxito
        $message = "El viaje ha sido cancelado exitosamente.";
    } else {
        $message = "Error al cancelar el viaje.";
    }
}

// Consulta para obtener los viajes del usuario junto con los asientos usados y el estado del viaje
$query = "SELECT v.id, v.origen, v.destino, v.fecha_salida, 
                 GROUP_CONCAT(dv.asiento ORDER BY dv.asiento SEPARATOR ', ') AS asientos, 
                 v.precio, v.estado 
          FROM viajes v
          JOIN detalle_viaje dv ON v.id = dv.viaje_id 
          WHERE v.user_id = :user_id 
          GROUP BY v.id";

$stmt = $db->prepare($query);
$stmt->bindParam(':user_id', $userId);
$stmt->execute();

$title = 'Mis Viajes';
include 'layouts/header.php'; 
?>
<body class="bg-gray-100">
    <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-100">
        <div class="max-w-4xl mx-auto px-6 py-8">
            <h1 class="text-3xl font-bold mb-6 text-amber-900">Mis Viajes</h1>
            <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Origen</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Destino</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Asientos</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Precio</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php while ($row = $stmt->fetch(PDO::FETCH_ASSOC)): ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap"><?= htmlspecialchars($row['origen']) ?></td>
                                <td class="px-6 py-4 whitespace-nowrap"><?= htmlspecialchars($row['destino']) ?></td>
                                <td class="px-6 py-4 whitespace-nowrap"><?= htmlspecialchars($row['fecha_salida']) ?></td>
                                <td class="px-6 py-4 whitespace-nowrap"><?= htmlspecialchars($row['asientos']) ?></td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <?php
                                    // Determinar el color y el fondo basado en el estado
                                    $estado = htmlspecialchars($row['estado']);
                                    $colorTexto = '';
                                    $colorFondo = '';

                                    switch ($estado) {
                                        case 'Confirmado':
                                            $colorTexto = 'text-green-800'; // Texto verde oscuro
                                            $colorFondo = 'bg-green-100'; // Fondo verde claro
                                            break;
                                        case 'En Espera':
                                            $colorTexto = 'text-blue-800'; // Texto naranja oscuro
                                            $colorFondo = 'bg-blue-100'; // Fondo naranja claro
                                            break;
                                        case 'Cancelado':
                                            $colorTexto = 'text-red-800'; // Texto rojo oscuro
                                            $colorFondo = 'bg-red-100'; // Fondo rojo claro
                                            break;
                                        default:
                                            $colorTexto = 'text-gray-800'; // Texto gris oscuro por defecto
                                            $colorFondo = 'bg-gray-100'; // Fondo gris claro por defecto
                                    }
                                    ?>
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?= $colorFondo ?> <?= $colorTexto ?>">
                                        <?= ucfirst($estado) ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">S/ <?= number_format($row['precio'], 2) ?></td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <?php if ($estado !== 'Cancelado' && $estado !== 'Confirmado'): ?>
                                    <form action="my-trips.php" method="POST" style="display:inline;">
                                        <input type="hidden" name="trip_id" value="<?= $row['id'] ?>">
                                        <input type="hidden" name="action" value="cancel">
                                            <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('¿Estás seguro de que deseas cancelar este viaje?');">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                </td>    
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main> 
    <?php include 'layouts/footer.php'; ?>
</body>
</html>