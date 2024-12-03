<?php
session_start();
require_once '../config/database.php';
require_once '../controllers/TripController.php';

// Verificar si el usuario está logueado
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Crear una nueva instancia del controlador de viajes
$tripController = new TripController();

// Obtener parámetros de la URL
$tripId = filter_input(INPUT_GET, 'trip_id', FILTER_SANITIZE_STRING) ?? '';
if (isset($_POST['trip_id'])) {
    $tripId = $_POST['trip_id'];
} else {
    echo "Error: trip_id no está definido.";
    exit;
}
$userId = $_SESSION['user_id'];
$seats = $_POST['seats'] ?? [];
if (empty($seats)) {
    die('Error: No se seleccionaron asientos.');
}

// Decodificar los datos de los asientos (JSON a array)
$selectedSeats = array_map(function($seat) {
    return json_decode($seat, true);
}, $seats);

// Verificar formato de los asientos
foreach ($selectedSeats as $seat) {
    if (!isset($seat['numero'], $seat['categoria'])) {
        die('Error: Formato de asiento inválido.');
    }
}

// Establecer los datos del viaje desde la sesión
$origen = $_SESSION['datos_viaje']['origen'] ?? '';
$destino = $_SESSION['datos_viaje']['destino'] ?? '';
$fecha_ida = $_SESSION['datos_viaje']['fecha_ida'] ?? '';
$fecha_vuelta = $_SESSION['datos_viaje']['fecha_vuelta'] ?? '';

// Verificar que todos los datos del viaje están presentes
if (empty($origen) || empty($destino) || empty($fecha_ida)) {
    die('Error: Datos del viaje incompletos.');
}

// Obtener el costo del destino
$database = new Database();
$conn = $database->getConnection();
$query = "SELECT costo FROM destinos WHERE nombre_ciudad = :destino";
$stmt = $conn->prepare($query);
$stmt->bindParam(':destino', $destino);
$stmt->execute();
$destinoData = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$destinoData) {
    die('Error: No se encontró el costo para el destino seleccionado.');
}

$costoDestino = $destinoData['costo'];

// Calcular el precio total basado en los asientos seleccionados
$total = $costoDestino; // Inicializa el total con el costo del destino
$precioPorAsiento = [
    'vip' => 15, // 50% más caro que el estándar
    'estandar' => 10, // Precio base
    'economico' => 8 // 20% más barato que el estándar
];

foreach ($selectedSeats as $seat) {
    $categoria = $seat['categoria'];
    if (array_key_exists($categoria, $precioPorAsiento)) {
        $total += $precioPorAsiento[$categoria];
    } else {
        die('Error: Categoría de asiento no válida.');
    }
}
$total = floatval($total);
// Al inicio del archivo, después de obtener el total
$currency = $_POST['currency'] ?? 'S/'; // Usar el valor enviado en el formulario o 'S/' por defecto

// Definir los valores de conversión
$conversionRates = [
    'S/' => 1,
    '$' => 3.75, // Ejemplo: 1 dólar = 3.75 soles
    '€' => 4.10, // Ejemplo: 1 euro = 4.10 soles
    '¥' => 0.027 // Ejemplo: 1 yen = 0.027 soles
];

if (!isset($conversionRates[$currency])) {
    die('Error: Moneda no soportada.');
}

// Multiplicar el total por el valor de la moneda seleccionada
$totalConverted = $total * $conversionRates[$currency];

$trip = $tripController->getTrip();
$trip->origen = $origen;
$trip->destino = $destino;
$trip->fecha_salida = $fecha_ida;
$trip->fecha_llegada = $fecha_vuelta;
$trip->bus_id = $tripId; // ID del bus seleccionado
$trip->user_id = $userId; // ID del usuario
$trip->seats = json_encode($selectedSeats); // Guardar los asientos seleccionados como JSON
$trip->total = $total; // Total a pagar

// Incluir el encabezado
include 'layouts/header.php';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resumen de Compra - BusBooker</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f8f9fa; /* Color de fondo suave */
        }
        .container {
            margin-top: 30px;
        }
        /* Estilos adicionales... */
    </style>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto px-6 py-8">
        <h1 class="text-3xl font-bold mb-6">Resumen de Compra</h1>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Detalles del Viaje -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h2 class="text-xl font-semibold mb-4">Detalles del Viaje</h2>
                <div class="flex items-center mb-2">
                    <span class="material-icons text-gray-500 mr-2"></span>
                    <p><strong>Origen:</strong> <?php echo htmlspecialchars($origen); ?></p>
                </div>
                <div class="flex items-center mb-2">
                    <span class="material-icons text-gray-500 mr-2"></span>
                    <p><strong>Destino:</strong> <?php echo htmlspecialchars($destino); ?></p>
                </div>
                <div class="flex items-center mb-2">
                    <span class="material-icons text-gray-500 mr-2"></span>
                    <p><strong>Fecha de Ida:</strong> <?php echo htmlspecialchars($fecha_ida); ?></p>
                </div>
                <div class="flex items-center mb-2">
                    <span class="material-icons text-gray-500 mr-2"></span>
                    <p><strong>Fecha de Vuelta:</strong> <?php echo htmlspecialchars($fecha_vuelta); ?></p>
                </div>
                <h2 class="text-xl font-semibold mt-6 mb-4">Asientos Seleccionados</h2>
                <ul class="list-disc ml-5 text-gray-600">
                    <?php foreach ($selectedSeats as $seat): ?>
                        <li>
                            <span class="text-gray-700">Asiento <?php echo htmlspecialchars($seat['numero']); ?> - Categoría <?php echo htmlspecialchars($seat['categoria']); ?></span>
                        </li>
                    <?php endforeach; ?>
                </ul>
                <h2 class="text-xl font-semibold mt-6 mb-4 text-green-600">Total a Pagar</h2>
                <p id="total-amount" class="text-2xl font-bold text-green-700">S/ <?php echo number_format($total, 2); ?></p>
                </div>
            <!-- Métodos de Pago -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <form action="confirm-payment.php" method="POST">
                <input type="hidden" name="trip_id" value="<?php echo htmlspecialchars($tripId); ?>">                    
                <input type="hidden" name="seats" value="<?php echo htmlspecialchars(json_encode($selectedSeats)); ?>">
                <input type="hidden" name="total" value="<?php echo $totalConverted; ?>" /> <!-- Aquí envías el total convertido -->                
                <input type="hidden" name="currency" id="currency" value="<?php echo $currency; ?>">
                    <h2 class="text-xl font-semibold mb-4">Método de Pago</h2>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Selecciona un Método de Pago</label>
                        <select id="payment-method" name="payment_method" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                            <option value="card">💳 Tarjeta de Crédito/Débito</option>
                            <option value="yape">📱 Yape</option>
                            <option value="plin">📱 Plin</option>
                            <option value="paypal">🌐 PayPal</option>
                        </select>
                    </div>

                    <!-- Campos de método de pago -->
                    <div id="payment-fields">
                        <div id="card-fields" class="payment-field hidden">
                            <h3 class="text-lg font-semibold">Información de Tarjeta</h3>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Número de Tarjeta</label>
                                <input type="text" name="card_number" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-amber-500 focus:ring-amber-500" placeholder="1234 5678 9012 3456" required>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Fecha de Vencimiento</label>
                                    <input type="text" name="expiry_date" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-amber-500 focus:ring-amber-500" placeholder="MM/AA" required>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">CVV</label>
                                    <input type="text" name="cvv" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-amber-500 focus:ring-amber-500" placeholder="123" required>
                                </div>
                            </div>
                        </div>

                        <!-- Otros campos para Yape, Plin y PayPal -->
                        <div id="yape-fields" class="payment-field hidden">
                            <h3 class="text-lg font-semibold">Información de Yape</h3>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Número de Yape</label>
                                <input type="text" name="yape_number" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-amber-500 focus:ring-amber-500" placeholder="Ingrese su número de Yape" required>
                            </div>
                        </div>

                        <div id="plin-fields" class="payment-field hidden">
                            <h3 class="text-lg font-semibold">Información de Plin</h3>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Número de Plin</label>
                                <input type="text" name="plin_number" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-amber-500 focus:ring-amber-500" placeholder="Ingrese su número de Plin" required>
                            </div>
                        </div>

                        <div id="paypal-fields" class="payment-field hidden">
                            <h3 class="text-lg font-semibold">Información de PayPal</h3>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Correo de PayPal</label>
                                <input type="email" name="paypal_email" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-amber-500 focus:ring-amber-500" placeholder="Correo asociado a PayPal" required>
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="mt-6 px-4 py-2 bg-green-500 text-white rounded-md">Confirmar Pago</button>
                </form>
            </div>
        </div>
    </div>
    
    <script>
        document.getElementById('payment-method').addEventListener('change', function() {
            const selectedMethod = this.value;
            const paymentFields = document.querySelectorAll('.payment-field');
            
            paymentFields.forEach(field => {
                field.classList.add('hidden'); // Ocultar todos los campos
                const inputs = field.querySelectorAll('input');
                inputs.forEach(input => input.disabled = true); // Deshabilitar inputs
            });

            // Mostrar y habilitar los campos del método seleccionado
            if (selectedMethod === 'card') {
                const cardFields = document.getElementById('card-fields');
                cardFields.classList.remove('hidden');
                const inputs = cardFields.querySelectorAll('input');
                inputs.forEach(input => input.disabled = false);
            } else if (selectedMethod === 'yape') {
                const yapeFields = document.getElementById('yape-fields');
                yapeFields.classList.remove('hidden');
                const inputs = yapeFields.querySelectorAll('input');
                inputs.forEach(input => input.disabled = false);
            } else if (selectedMethod === 'plin') {
                const plinFields = document.getElementById('plin-fields');
                plinFields.classList.remove('hidden');
                const inputs = plinFields.querySelectorAll('input');
                inputs.forEach(input => input.disabled = false);
            } else if (selectedMethod === 'paypal') {
                const paypalFields = document.getElementById('paypal-fields');
                paypalFields.classList.remove('hidden');
                const inputs = paypalFields.querySelectorAll('input');
                inputs.forEach(input => input.disabled = false);
            }
        });
        
        function setCurrency(symbol) {
            // Guardar la moneda seleccionada en localStorage
            localStorage.setItem('currency', symbol);
            document.getElementById('currency-symbol').textContent = symbol; // Cambiar el símbolo en el botón
            document.getElementById('currency').value = symbol; // Actualizar el campo oculto

            // Realizar la conversión y actualizar el total mostrado
            const total = <?php echo json_encode($total); ?>; // Total en soles (S/)
            const conversionRates = {
                'S/': 1,
                '$': 3.75,
                '€': 4.10,
                '¥': 0.027
            };
            
            // Calcular el total convertido según la moneda seleccionada
            const totalConverted = total * conversionRates[symbol];
            
            // Actualizar el total mostrado en la página
            document.getElementById('total-amount').textContent = symbol + " " + totalConverted.toFixed(2);

            closeCurrencyMenu(); // Cerrar el menú después de seleccionar
        }

        // Establecer la moneda por defecto al cargar la página
        document.addEventListener('DOMContentLoaded', function() {
            const currency = localStorage.getItem('currency') || 'S/';
            document.getElementById('currency').value = currency;        
        });

        window.onload = function() {
            const currency = localStorage.getItem('currency') || 'S/';
            document.getElementById('currency-symbol').textContent = currency; // Actualizar el símbolo en el botón
            setCurrency(currency); // Llamar a la función para actualizar el total
        }


        // Disparar el evento de cambio al cargar la página para mostrar los campos predeterminados
        document.getElementById('payment-method').dispatchEvent(new Event('change'));
    </script>
</body>
</html>

<?php include 'layouts/footer.php'; ?>
