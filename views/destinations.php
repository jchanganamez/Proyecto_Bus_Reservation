<?php 
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$title = 'Destinos';
include 'layouts/header.php'; 

// Conexión a la base de datos
require_once '../config/database.php'; // Asegúrate de que la ruta sea correcta
$database = new Database();
$conn = $database->getConnection(); // Crear conexión a la base de datos

// Consulta para obtener los destinos
$query = "SELECT nombre_ciudad AS nombre_destino, descripcion, costo AS precio FROM destinos"; // Cambia 'costo' a 'precio' para la visualización
$stmt = $conn->prepare($query);
$stmt->execute();
$destinos = $stmt->fetchAll(PDO::FETCH_ASSOC); // Obtener todos los destinos

// Colores de fondo disponibles (en orden)
$colores = [
    'from-blue-500 to-purple-500',
    'from-green-500 to-teal-500',
    'from-pink-500 to-red-500',
    'from-indigo-500 to-blue-500',
];

$totalColores = count($colores); // Número total de colores disponibles
?>

<body class="bg-gray-100">
    <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-100">
        <div class="max-w-6xl mx-auto px-6 py-8">
            <h1 class="text-3xl font-bold mb-6 text-amber-900">Destinos Populares</h1>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php foreach ($destinos as $index => $destino): ?>
                <?php 
                    // Asignar color basado en el índice (rotación)
                    $degradado = $colores[$index % $totalColores];
                ?>
                <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                    <!-- Espacio para iniciales con fondo degradado rotativo -->
                    <div class="h-48 w-full flex items-center justify-center bg-gradient-to-r <?= $degradado ?> text-white font-bold text-4xl">
                        <?= strtoupper(substr($destino['nombre_destino'], 0, 2)) ?>
                    </div>
                    <div class="p-6">
                        <h2 class="text-xl font-semibold mb-2"><?= htmlspecialchars($destino['nombre_destino']) ?></h2>
                        <p class="text-gray-600 mb-4"><?= htmlspecialchars($destino['descripcion']) ?></p>
                        <div class="flex justify-between items-center">
                            <span class="text-amber-600 font-bold">Desde S/ <?= htmlspecialchars($destino['precio']) ?></span>
                            <button class="px-4 py-2 bg-amber-600 text-white rounded-lg hover:bg-amber-700 transition-colors">
                                Ver Rutas
                            </button>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </main>
    <?php include 'layouts/footer.php'; ?>
</body>
</html>
