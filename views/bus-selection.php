<?php
session_start();

// Verificar si el usuario está logueado
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}
$origen = $_GET['origen'] ?? '';
$destino = $_GET['destino'] ?? '';
$fecha_ida = $_GET['fecha_ida'] ?? '';
$fecha_vuelta = $_GET['fecha_vuelta'] ?? '';

// Almacenar los datos del viaje en la sesión
$_SESSION['datos_viaje'] = [
    'origen' => $origen,
    'destino' => $destino,
    'fecha_ida' => $fecha_ida,
    'fecha_vuelta' => $fecha_vuelta,
];

header('Content-Type: text/html; charset=UTF-8');

// Incluir el archivo de configuración de la base de datos y el controlador de buses
require_once '../config/database.php';
require_once '../controllers/BusController.php';

// Crear una nueva conexión a la base de datos
$database = new Database();
$db = $database->getConnection();

// Crear una nueva instancia del controlador de buses
$busController = new BusController();

// Obtener todos los buses
$buses = $busController->obtenerTodosLosBuses();
$busListHtml = mostrarBuses($buses); // Este método debería devolver los buses en formato JSON
function mostrarBuses($buses) {
    if (empty($buses)) {
        return '<p class="text-gray-700">No hay buses disponibles.</p>';
    }

    $html = '';
    foreach ($buses as $bus) {
        $html .= '<div class="bg-white rounded-lg shadow-md p-4">';
        $html .= '<h3 class="text-lg font-semibold">' . htmlspecialchars($bus['modelo']) . ' (' . htmlspecialchars($bus['numero']) . ')</h3>';
        $html .= '<p>Capacidad: ' . htmlspecialchars($bus['capacidad']) . '</p>';
        $html .= '<p>Estado: Activo</p>';
    
        // Botón "Seleccionar" con el enlace a seat-selection.php
        $html .= '<a href="seat-selection.php?bus_id=' . htmlspecialchars($bus['id']) . '" style="display: inline-block; background-color: orange; color: white; font-weight: bold; padding: 0.5rem 1rem; border-radius: 0.375rem; text-align: center; text-decoration: none; transition: background-color 0.3s;" onmouseover="this.style.backgroundColor=\'#d68a00\'" onmouseout="this.style.backgroundColor=\'orange\'">Seleccionar</a>';
        $html .= '</div>';
    }

    return $html;
}

$title = "Selección de Buses - BusBooker";
include 'layouts/header.php';
?>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Selección de Buses</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <div class="flex h-screen">
        <div class="flex-1 flex flex-col">
            <header class="bg-white shadow-sm">
                <div class="flex justify-between items-center px-6 py-4">
                    <h1 class="text-2xl font-semibold text-amber-900">Selección de Buses</h1>
                </div>
            </header>

            <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-100">
                <div class="container mx-auto px-6 py-8">
                    <h2 class="text-3xl font-bold mb-6">Lista de Buses Disponibles</h2>
                    <div id="bus-list" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <!-- Aquí se mostrarán los buses -->
                        <?php echo $busListHtml; ?>
                    </div>
                </div>
            </main>

            <footer class="bg-white shadow-sm mt-auto">
                <div class="container mx-auto px-6 py-4">
                    <p class="text-center text-gray-600">© 2024 BusBooker. Todos los derechos reservados.</p>
                </div>
            </footer>
        </div>
    </div>
    <script>
       document.addEventListener('DOMContentLoaded', function() {
        console.log("Página cargada, llamando a getAvailableBuses");
    });

    function getAvailableBuses() {
        console.log("Obteniendo buses disponibles...");
        fetch('api/get_buses.php')
            .then(response => response.json())
            .then(data => {
                console.log("Datos recibidos:", data);
                if (Array.isArray(data)) {
                    displayBuses(data);
                } else {
                    console.error('La respuesta no es un array:', data);
                }
            })
            .catch(error => {
                console.error('Error fetching buses:', error);
            });
    }


        function displayBuses(buses) {
            const busList = document.getElementById('bus-list');
            busList.innerHTML = ''; // Limpia la lista antes de agregar nuevos buses

            if (buses.length === 0) {
                busList.innerHTML = '<p class="text-gray-700">No hay buses disponibles.</p>';
                return;
            }

            buses.forEach(bus => {
                const busItem = document.createElement('div');
                busItem.className = 'bg-white rounded-lg shadow-md p-4';
                busItem.innerHTML = `
                    <h3 class="text-lg font-semibold">${bus.model} (${bus.plate})</h3>
                    <p>Capacidad: ${bus.capacity}</p>
                    <p>Estado: ${bus.status}</p>
                `;
                busList.appendChild(busItem); // Agrega el nuevo bus a la lista
            });
        }
        

    </script>
</body>
</html>