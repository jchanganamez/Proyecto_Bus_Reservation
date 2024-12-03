<?php 
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
$title = 'Admin Dashboard';
include '../layouts/header.php';

// Conexión a la base de datos
require_once '../../config/database.php';
$database = new Database();
$db = $database->getConnection();

// Consultar los datos necesarios
// 1. Obtener el total de usuarios
$totalUsuariosQuery = "SELECT COUNT(*) as total FROM usuarios";
$totalUsuariosResult = $db->query($totalUsuariosQuery);
$totalUsuarios = $totalUsuariosResult->fetch(PDO::FETCH_ASSOC)['total'];

// 2. Obtener el total de buses activos
$totalBusesQuery = "SELECT COUNT(*) as total FROM buses";
$totalBusesResult = $db->query($totalBusesQuery);
$totalBuses = $totalBusesResult->fetch(PDO::FETCH_ASSOC)['total'];

// 3. Obtener el total de conductores
$totalConductoresQuery = "SELECT COUNT(*) as total FROM conductores";
$totalConductoresResult = $db->query($totalConductoresQuery);
$totalConductores = $totalConductoresResult->fetch(PDO::FETCH_ASSOC)['total'];

// 4. Obtener el total de viajes hoy
$totalViajesHoyQuery = "SELECT COUNT(*) as total FROM viajes WHERE DATE(fecha_salida) = CURDATE()";
$totalViajesHoyResult = $db->query($totalViajesHoyQuery);
$totalViajesHoy = $totalViajesHoyResult->fetch(PDO::FETCH_ASSOC)['total'];

// 5. Obtener los 3 últimos viajes
$ultimosViajesQuery = "
    SELECT v.id, v.origen, v.destino, v.fecha_salida, v.estado 
    FROM viajes v 
    ORDER BY v.fecha_salida DESC 
    LIMIT 3";
$ultimosViajesResult = $db->query($ultimosViajesQuery);
$ultimosViajes = $ultimosViajesResult->fetchAll(PDO::FETCH_ASSOC);

// 6. Obtener las 5 últimas actividades
$ultimasActividadesQuery = "SELECT descripcion FROM reportes ORDER BY fecha DESC LIMIT 5";
$ultimasActividadesResult = $db->query($ultimasActividadesQuery);
$ultimasActividades = $ultimasActividadesResult->fetchAll(PDO::FETCH_ASSOC);

// 7. Obtener el total de viajes
$totalViajesQuery = "SELECT COUNT(*) as total FROM viajes";
$totalViajesResult = $db->query($totalViajesQuery);
$totalViajes = $totalViajesResult->fetch(PDO::FETCH_ASSOC)['total'];
?>



<body class="bg-gray-100">
    <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-100">
        <div class="container mx-auto px-6 py-8">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6">
                <!-- Estadísticas -->
                <div class="bg-white rounded-lg shadow-lg p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-amber-100 text-amber-600">
                            <i class="bi bi-person w-12 h-12"></i> <!-- Ícono de usuarios -->
                        </div>
                        <div class="ml-4">
                            <h2 class="text-gray-600">Usuarios Totales</h2>
                            <p class="text-2xl font-semibold text-gray-700"><?= $totalUsuarios ?></p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-lg p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-amber-100 text-amber-600">
                            <i class="bi bi-bus-front w-12 h-12"></i> <!-- Ícono de buses -->
                        </div>
                        <div class="ml-4">
                            <h2 class="text-gray-600">Buses Activos</h2>
                            <p class="text-2xl font-semibold text-gray-700"><?= $totalBuses ?></p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-lg p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-amber-100 text-amber-600">
                            <i class="bi bi-person-badge w-16 h-16"></i> <!-- Ícono de conductores -->
                        </div>
                        <div class="ml-4">
                            <h2 class="text-gray-600">Conductores</h2>
                            <p class="text-2xl font-semibold text-gray-700"><?= $totalConductores ?></p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-lg p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-amber-100 text-amber-600">
                            <i class="bi bi-map w-16 h-16"></i> <!-- Ícono de viajes -->
                        </div>
                        <div class="ml-4">
                            <h2 class="text-gray-600">Viajes Hoy</h2>
                            <p class="text-2xl font-semibold text-gray-700"><?= $totalViajesHoy ?></p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-lg p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-amber-100 text-amber-600">
                            <i class="bi bi-map w-16 h-16"></i> <!-- Ícono de viajes -->
                        </div>
                        <div class="ml-4">
                            <h2 class="text-gray-600">Viajes</h2>
                            <p class="text-2xl font-semibold text-gray-700"><?= $totalViajes ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Últimos Viajes -->
            <div class="mt-8 bg-white rounded-lg shadow-lg p-6">
    <h2 class="text-lg font-semibold text-gray-700 mb-4">Últimos Viajes</h2>
    <div class="overflow-x-auto">
        <table class="min-w-full bg-white">
            <thead>
                <tr>
                    <th class="py-2 px-4 border-b">ID</th>
                    <th class="py-2 px-4 border-b">Origen</th>
                    <th class="py-2 px-4 border-b">Destino</th>
                    <th class="py-2 px-4 border-b">Fecha de Salida</th>
                    <th class="py-2 px-4 border-b">Estado</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($ultimosViajes as $viaje): ?>
                    <tr>
                        <td class="py-2 px-4 border-b"><?= $viaje['id'] ?></td>
                        <td class="py-2 px-4 border-b"><?= $viaje['origen'] ?></td>
                        <td class="py-2 px-4 border-b"><?= $viaje['destino'] ?></td>
                        <td class="py-2 px-4 border-b"><?= $viaje['fecha_salida'] ?></td>
                        <td class="py-2 px-4 border-b">
                            <?php 
                                $estado = $viaje['estado'];
                                // Colores según el estado
                                if ($estado == 'En Espera') {
                                    echo '<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">Espera</span>';
                                } elseif ($estado == 'Confirmado') {
                                    echo '<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Confirmado</span>';
                                } elseif ($estado == 'Cancelado') {
                                    echo '<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Cancelado</span>';
                                }
                            ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

            <!-- Actividad Reciente -->
            <div class="mt-8 bg-white rounded-lg shadow-lg p-6">
                <h2 class="text-lg font-semibold text-gray-700 mb-4">Actividad Reciente</h2>
                <div class="space-y-4">
                    <?php foreach ($ultimasActividades as $actividad): ?>
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="h-8 w-8 rounded-full bg-amber-100 flex items-center justify-center">
                                    <i class="bi bi-person-plus h-5 w-5 text-amber-600"></i> <!-- Ícono de actividad -->
                                </div>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-900"><?= $actividad['descripcion'] ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </main>
</body>
<?php include '../layouts/footer.php'; ?>

<script>
    // Obtener parámetros de la URL
    const urlParams = new URLSearchParams(window.location.search);
    const tripId = urlParams.get('trip_id');
    const seats = urlParams.get('seats');

    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('trip-info').textContent = `Trip ID: ${tripId}`;
        document.getElementById('selected-seats').textContent = `Asientos seleccionados: ${seats}`;
    });
</script>
