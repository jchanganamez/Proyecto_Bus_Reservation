<?php 
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
$title = 'Admin Dashboard';
include '../layouts/header.php';
?>
<body class="bg-gray-100">
    <!-- Main Content -->
    <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-100">
        <div class="container mx-auto px-6 py-8">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Estadísticas -->
                <div class="bg-white rounded-lg shadow-lg p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-amber-100 text-amber-600">
                            <i class="bi bi-person w-12 h-12"></i> <!-- Aumentamos el tamaño aquí -->
                        </div>
                        <div class="ml-4">
                            <h2 class="text-gray-600">Usuarios Totales</h2>
                            <p class="text-2xl font-semibold text-gray-700">1,257</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-lg p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-amber-100 text-amber-600">
                            <i class="bi bi-bus-front w-12 h-12"></i> <!-- Aumentamos el tamaño aquí -->
                        </div>
                        <div class="ml-4">
                            <h2 class="text-gray-600">Buses Activos</h2>
                            <p class="text-2xl font-semibold text-gray-700">45</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-lg p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-amber-100 text-amber-600">
                            <i class="bi bi-person-badge w-16 h-16"></i> <!-- Aumentamos el tamaño aquí -->
                        </div>
                        <div class="ml-4">
                            <h2 class="text-gray-600">Conductores</h2>
                            <p class="text-2xl font-semibold text-gray-700">38</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-lg p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-amber-100 text-amber-600">
                            <i class="bi bi-map w-16 h-16"></i> <!-- Aumentamos el tamaño aquí -->
                        </div>
                        <div class="ml-4">
                            <h2 class="text-gray-600">Viajes Hoy</h2>
                            <p class="text-2xl font-semibold text-gray-700">24</p>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Gráficos y Tablas -->
            <div class="mt-8 grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Últimos Viajes -->
                <div class="bg-white rounded-lg shadow-lg p-6">
                    <h2 class="text-lg font-semibold text-gray-700 mb-4">Últimos Viajes</h2>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead>
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ruta</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fecha</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                <tr>
                                    <td class="px-6 py-4">Lima - Cusco</td>
                                    <td class="px-6 py-4">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                            Completado
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">15 Feb 2024</td>
                                </tr>
                                <!-- Más filas... -->
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Actividad Reciente -->
                <div class="bg-white rounded-lg shadow-lg p-6">
                    <h2 class="text-lg font-semibold text-gray-700 mb-4">Actividad Reciente</h2>
                    <div class="space-y-4">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="h-8 w-8 rounded-full bg-amber-100 flex items-center justify-center">
                                    <i class="bi bi-person-plus h-5 w-5 text-amber-600"></i>
                                </div>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-900">Nuevo usuario registrado</p>
                                <p class="text-sm text-gray-500">Hace 5 minutos</p>
                            </div>
                        </div>
                        <!-- Más elementos... -->
                    </div>
                </div>
            </div>
        </div>
    </main>
    <?php include '../layouts/footer.php'; ?>
</body>
</html>

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