<?php
session_start();
require_once '../config/database.php';
require_once '../controllers/TripController.php';


// Verificar si el usuario está logueado
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$database = new Database();
$db = $database->getConnection();
$tripController = new TripController();
$trip = new Trip($db);

$tripId = $_POST['trip_id'] ?? '';
$seats = $_POST['seats'] ?? ''; // Esto es un string JSON
$selectedSeats = json_decode($seats, true);
if (json_last_error() !== JSON_ERROR_NONE) {
    throw new Exception("Error al decodificar JSON: " . json_last_error_msg());
}
if (!is_array($selectedSeats)) {
    throw new Exception("Error al procesar los asientos seleccionados.");
}

// Consulta para obtener el conductor_id usando el tripId (que es la ID del bus)
$stmt_bus = $db->prepare("SELECT conductor_id FROM buses WHERE id = :bus_id");
$stmt_bus->bindParam(':bus_id', $tripId); // Usamos tripId como bus_id
$stmt_bus->execute();

if ($stmt_bus->rowCount() == 0) {
    throw new Exception("No se encontró el bus con ID {$tripId}.");
}

// Obtener el conductor_id del bus
$bus = $stmt_bus->fetch(PDO::FETCH_ASSOC);
$conductorId = $bus['conductor_id'];

// Ahora, buscamos al conductor usando el conductor_id
$stmt_conductor = $db->prepare("SELECT id FROM conductores WHERE id = :conductor_id");
$stmt_conductor->bindParam(':conductor_id', $conductorId);
$stmt_conductor->execute();

if ($stmt_conductor->rowCount() == 0) {
    throw new Exception("El conductor con ID {$conductorId} no existe.");
}

foreach ($selectedSeats as $seat) {
    $seatNumber = $seat['numero'] ?? null; // Asegúrate de que 'numero' exista
    $categoria = $seat['categoria'] ?? null; // Asegúrate de que 'categoria' exista

    if ($seatNumber === null || $categoria === null) {
        throw new Exception("Asiento o categoría no definidos.");
    }

    // Establecer los datos del viaje
    $trip->asiento = $seatNumber; // Asiento seleccionado
    $trip->categoria = $categoria; // Categoría del asiento
    $trip->bus_id = $tripId; // Asegúrate de que bus_id esté definido

    // Actualiza el estado del asiento a ocupado
    if (!$trip->updateSeatStatus()) {
        throw new Exception("Error al actualizar el estado del asiento {$seatNumber}.");
    }
}

// Establecer los datos del viaje desde la sesión
$origen = $_SESSION['datos_viaje']['origen'] ?? '';
$destino = $_SESSION['datos_viaje']['destino'] ?? '';
$fecha_ida = $_SESSION['datos_viaje']['fecha_ida'] ?? '';
$fecha_vuelta = $_SESSION['datos_viaje']['fecha_vuelta'] ?? '';
// Obtener los datos del formulario
//$conductorId = $_POST['conductor_id'] ?? ''; 
$userId = $_SESSION['user_id'];
$paymentMethod = $_POST['payment_method'] ?? '';
$cardNumber = $_POST['card_number'] ?? '';
$expiryDate = $_POST['expiry_date'] ?? '';
$cvv = $_POST['cvv'] ?? '';
$yapeNumber = $_POST['yape_number'] ?? '';
$plinNumber = $_POST['plin_number'] ?? '';
$paypalEmail = $_POST['paypal_email'] ?? '';
$total = $_POST['total'] ?? 0;
if ($total <= 0) {
    die('Error: El total no es válido.');
}
$currency = $_POST['currency'] ?? 'S/';

$total = floatval($total); // Asegúrate de convertirlo a float

// Verifica si el total es un número válido
if ($total <= 0) {
    die('Error: El total no es válido.');
}

// Establecer los datos del viaje
$trip->origen = $origen;
$trip->destino = $destino;
$trip->fecha_salida = $fecha_ida;
$trip->fecha_llegada = $fecha_vuelta;
$trip->bus_id = $tripId; // ID del bus seleccionado
$trip->user_id = $userId; // ID del usuario
$trip->conductor_id = $conductorId;
$trip->seats = $seats;
$trip->payment_method = $paymentMethod;
$trip->card_number = $cardNumber;
$trip->expiry_date = $expiryDate;
$trip->cvv = $cvv;
$trip->yape_number = $yapeNumber;
$trip->plin_number = $plinNumber;
$trip->paypal_email = $paypalEmail;
$trip->total = $total;

// Guardar el viaje
if ($trip->create()) {
    // Pago exitoso
    $message = "Pago realizado con éxito. Detalles de la transacción:";
} else {
    // Error al guardar el viaje
    $message = "Error al procesar el pago. Inténtalo de nuevo.";
}
// Incluir el encabezado
include 'layouts/header.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmación de Pago - BusBooker</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <div class="container mx-auto px-6 py-8">
        <h1 class="text-3xl font-bold mb-6">Confirmación de Pago</h1>
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h2 class="text-xl font-semibold mb-4"><?php echo $message; ?></h2>
                <p><strong>Origen:</strong> <?php echo htmlspecialchars($_SESSION['datos_viaje']['origen']); ?></p>
                <p><strong>Destino:</strong> <?php echo htmlspecialchars($_SESSION['datos_viaje']['destino']); ?></p>
                <p><strong>Fecha de Ida:</strong> <?php echo htmlspecialchars($_SESSION['datos_viaje']['fecha_ida']); ?></p>
                <p><strong>Fecha de Vuelta:</strong> <?php echo htmlspecialchars($_SESSION['datos_viaje']['fecha_vuelta']); ?></p>
                <p><strong>Total a Pagar:</strong> <?php echo $currency . " " . number_format($total, 2); ?></p>
                <p><strong>Método de Pago:</strong> <?php echo htmlspecialchars($paymentMethod); ?></p>
        </div>
        <div class="mt-6">
            <a href="home.php" class="bg-blue-500 text-white font-bold py-2 px-4 rounded">Volver al Inicio</a>
        </div>
    </div>
    <?php include 'layouts/footer.php'; ?>
</body>
</html>
<script>
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