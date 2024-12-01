<?php
session_start();

// Verificar si se ha seleccionado un bus
if (!isset($_GET['bus_id'])) {
    header('Location: seleccionar_bus.php');
    exit;
}

require_once '../config/database.php';
require_once '../controllers/BusController.php';

$busController = new BusController();
$bus_id = $_GET['bus_id'];

// Comprobar si los datos del viaje están en la sesión
if (!isset($_SESSION['datos_viaje'])) {
    header('Location: bus-selection.php');
    exit;
}

$datos_viaje = $_SESSION['datos_viaje'];
$origen = $datos_viaje['origen'];
$destino = $datos_viaje['destino'];
$fecha_ida = $datos_viaje['fecha_ida'];
$fecha_vuelta = $datos_viaje['fecha_vuelta'];

// Obtener información del bus
$bus = $busController->obtenerBus($bus_id);
if (!$bus) {
    echo "No se encontró el bus.";
    exit;
}

// Obtener asientos disponibles para el bus
$asientos_disponibles = $busController->obtenerAsientos($bus_id);
if (empty($asientos_disponibles)) {
    echo "No hay asientos disponibles.";
    exit;
}
require 'layouts/header.php';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Selección de Asientos</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        .seat {
            padding: 10px;
            border: 1px solid #ccc;
            text-align: center;
            border-radius: 5px;
            cursor: pointer;
        }

        .seat.available {
            background-color: #4caf50;
            color: white;
        }

        .seat.unavailable {
            background-color: #f44336;
            color: white;
            cursor: not-allowed;
        }
        
        .seat.selected {
            background-color: #FF9800; /* Naranja para seleccionados */
        }
    </style>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto px-6 py-8">
        <h1 class="text-3xl font-bold mb-6">Selección de Asientos</h1>
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h2 class="text-xl font-semibold mb-4">Seleccione sus Asientos</h2>
            <?php foreach (['vip', 'estandar', 'economico'] as $categoria): ?>
                <h3 class="text-lg font-semibold mb-2">Categoría <?php echo htmlspecialchars($categoria); ?></h3>
                <div class="grid grid-cols-5 gap-4 mb-6">
                    <?php foreach ($asientos_disponibles as $asiento): ?>
                        <?php if ($asiento['categoria'] === $categoria): ?>
                            <div 
                                class="seat <?php echo $asiento['estado'] === 'disponible' ? 'available' : 'unavailable'; ?>" 
                                data-seat="<?php echo htmlspecialchars($asiento['numero_asiento']); ?>" 
                                data-category="<?php echo htmlspecialchars($asiento['categoria']); ?>"
                            >
                                Asiento <?php echo htmlspecialchars($asiento['numero_asiento']); ?>
                            </div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
            <?php endforeach; ?>
            <div class="mt-6">
                <button id="confirmSelection" class="bg-blue-500 text-white font-bold py-2 px-4 rounded">Confirmar Selección</button>
            </div>
        </div>
    </div>

    <script>
        const selectedSeats = new Set();

        document.querySelectorAll('.seat.available').forEach(seat => {
            seat.addEventListener('click', () => {
                const seatNumber = seat.getAttribute('data-seat');
                if (selectedSeats.has(seatNumber)) {
                    selectedSeats.delete(seatNumber);
                    seat.classList.remove('selected');
                } else {
                    if (selectedSeats.size < 6) {
                        selectedSeats.add(seatNumber);
                        seat.classList.add('selected');
                    } else {
                        alert('Solo puedes seleccionar hasta 6 asientos.');
                    }
                }
            });
        });

        document.getElementById('confirmSelection').addEventListener('click', () => {
            if (selectedSeats.size === 0) {
                alert('Por favor, selecciona al menos un asiento.');
                return;
            }

            // Convertir el set de asientos seleccionados en un array
            const seats = Array.from(selectedSeats);

            // Crear un formulario oculto y enviar los datos de los asientos seleccionados
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = 'payment.php';

            // Enviar los asientos como un array
            selectedSeats.forEach((seat) => {
                const category = document.querySelector(`[data-seat="${seat}"]`).getAttribute('data-category');
                const inputSeat = document.createElement('input');
                inputSeat.type = 'hidden';
                inputSeat.name = 'seats[]';
                inputSeat.value = JSON.stringify({ numero: seat, categoria: category });
                form.appendChild(inputSeat);
            });
            const inputBusId = document.createElement('input');
            inputBusId.type = 'hidden';
            inputBusId.name = 'trip_id';
            inputBusId.value = '<?php echo $bus_id; ?>';
            form.appendChild(inputBusId);

            // Enviar el formulario
            document.body.appendChild(form);
            form.submit();
        });
    </script>

    
    <?php require 'layouts/footer.php'; ?>
</body>
</html>

