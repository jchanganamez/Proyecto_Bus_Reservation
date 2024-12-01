<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$title = 'Gestión de Viajes';
include '../layouts/header.php';
require_once '../../controllers/TripController.php';
require_once '../../controllers/BusController.php';
require_once '../../config/database.php';

$database = new Database();
$conn = $database->getConnection();

$busController = new BusController();
$tripController = new TripController();
$buses = $busController->obtenerTodosLosBuses();
$ciudades = $conn->query("SELECT DISTINCT nombre_ciudad FROM destinos ORDER BY nombre_ciudad")->fetchAll(PDO::FETCH_ASSOC);

$costoDestino = 0;
$total = 0;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['destino'])) {
    $destino = $_POST['destino'];

    // Obtener el costo del destino
    $query = "SELECT costo FROM destinos WHERE nombre_ciudad = :destino";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':destino', $destino);

    if ($stmt->execute()) {
        $destinoData = $stmt->fetch(PDO::FETCH_ASSOC);
        var_dump($destinoData); // Para depurar, ver qué contiene
    } else {
        echo "Error en la consulta SQL: " . $stmt->errorInfo();
    }

    if (!$destinoData) {
        $error = 'No se encontró el costo para el destino seleccionado.';
    }

    $costoDestino = $destinoData['costo'];
}

?>
<head>
<link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<style>
  .seat {
    text-align: center;
    border: 1px solid #ccc;
    border-radius: 4px;
    padding: 10px;
    cursor: pointer;
  }
  .seat-occupied {
    background-color: red;
    color: white;
    pointer-events: none;
  }
  .seat-available:hover {
    background-color: #f0f0f0;
  }
  .seat-available {
    background-color: #e0ffe0;
  }
  .seat-selected {
    background-color: green;
    color: white;
  }
</style>

<body class="bg-gray-100">
    <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-100">
        <div class="container mx-auto px-6 py-8">
            <h2 class="text-3xl font-bold mb-6">Crear Nuevo Viaje</h2>
            <form method="POST" action="insert_trip.php">
                <input type="hidden" name="trip_id" value="<?php echo $tripId; ?>" />
                <input type="hidden" name="action" value="create">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    <div>
                        <label for="origen" class="block text-sm font-medium text-gray-700">Origen</label>
                        <select id="origen" name="origen" required onchange="updateDestinoOptions()" class="block w-full border rounded p-2">
                            <option value="">Selecciona una ciudad</option>
                            <?php foreach ($ciudades as $ciudad): ?>
                                <option value="<?php echo htmlspecialchars($ciudad['nombre_ciudad']); ?>">
                                    <?php echo htmlspecialchars($ciudad['nombre_ciudad']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label for="destino" class="block text-sm font-medium text-gray-700">Destino</label>
                        <select id="destino" name="destino" required class="block w-full border rounded p-2">
                            <option value="">Selecciona una ciudad</option>
                        </select>
                    </div>
                    <div>
                        <label for="fecha_salida" class="block text-sm font-medium text-gray-700">Fecha de Salida</label>
                        <input type="datetime-local" id="fecha_salida" name="fecha_salida" required class="block w-full border rounded p-2">
                    </div>
                    <div>
                        <label for="fecha_llegada" class="block text-sm font-medium text-gray-700">Fecha de Llegada</label>
                        <input type="datetime-local" id="fecha_llegada" name="fecha_llegada" required class="block w-full border rounded p-2">
                    </div>
                    <div>
                        <label for="bus_id" class="block text-sm font-medium text-gray-700">Seleccionar Bus</label>
                        <select id="bus_id" name="bus_id" required onchange="updateTripId(this.value); fetchSeats(this.value)" class="block w-full border rounded p-2">
                            <option value="">Selecciona un bus</option>
                            <?php foreach ($buses as $bus): ?>
                                <option value="<?php echo htmlspecialchars($bus['id']); ?>">
                                    <?php echo htmlspecialchars($bus['modelo']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>

                        <!-- Campo oculto para trip_id -->
                        <input type="hidden" name="trip_id" id="trip_id" value="" />
                    </div>
                </div>
                <button type="button" class="mt-4 bg-blue-500 text-white py-2 px-4 rounded" onclick="openModal()">Seleccionar Asientos</button>
                
                <!-- Campo para mostrar asientos seleccionados -->
                <div class="mt-4">
                    <label class="block text-sm font-medium text-gray-700">Asientos Seleccionados</label>
                    <input type="text" id="asientos_mostrados" readonly class="block w-full border rounded p-2 bg-gray-200">
                </div>
                <div>
                    <label for="costo_destino" class="block text-sm font-medium text-gray-700">Costo del Destino</label>
                    <input type="text" id="costo_destino" value="0" readonly class="block w-full border rounded p-2 bg-gray-200">
                </div>
                <div>
                    <label for="total" class="block text-sm font-medium text-gray-700">Total</label>
                    <input type="text" id="total" name="total" value="<?php echo isset($total) ? number_format($total, 2) : ''; ?>" readonly class="block w-full border rounded p-2 bg-gray-200">                
                </div>
                
                <input type="hidden" name="asientos_seleccionados" id="asientos_seleccionados">
                <button type="submit" class="mt-4 bg-green-500 text-white py-2 px-4 rounded">Crear Viaje</button>
            </form>
        </div>
    </main>

    <!-- Modal de selección de asientos -->
    <div id="asientosModal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 hidden">
        <div class="bg-white rounded-lg p-6 w-11/12 md:w-1/2 max-h-3/4 overflow-y-auto">
            <h3 class="text-lg font-bold mb-4">Selecciona tus Asientos</h3>
            <div id="asientos" class="grid grid-cols-4 gap-4">
                <!-- Asientos cargados dinámicamente -->
            </div>
            <button id="confirmSeats" class="mt-4 bg-blue-500 text-white py-2 px-4 rounded">Confirmar</button>
            <button onclick="closeModal()" class="mt-4 bg-red-500 text-white py-2 px-4 rounded">Cerrar</button>
        </div>
    </div>

    <script>
        const costoDestino = <?php echo isset($costoDestino) ? $costoDestino : 0; ?>;
        let selectedSeats = new Set();

        function fetchSeats(busId) {
            if (!busId) return;
            fetch(`get_asientos.php?bus_id=${busId}`)
                .then(response => response.text())
                .then(data => {
                    document.getElementById('asientos').innerHTML = data;
                    openModal();
                })
                .catch(error => console.error('Error al cargar los asientos:', error));
        }

        function openModal() {
            document.getElementById('asientosModal').classList.remove('hidden');
        }

        function closeModal() {
            document.getElementById('asientosModal').classList.add('hidden');
        }

        function updateTripId(busId) {
            const tripIdField = document.getElementById('trip_id');
            if (tripIdField) {
                tripIdField.value = busId;
                console.log(`trip_id actualizado a: ${busId}`); // Para depuración
            } else {
                console.error('El campo trip_id no se encontró.');
            }
        }

        document.getElementById('asientos').addEventListener('click', e => {
            if (e.target.classList.contains('seat-available')) {
                const seatId = e.target.dataset.seatId;
                const category = e.target.dataset.category;

                if (selectedSeats.has(seatId)) {
                    selectedSeats.delete(seatId);
                    e.target.classList.remove('seat-selected');
                } else {
                    selectedSeats.add({ id: seatId, category: category });
                    e.target.classList.add('seat-selected');
                }

                updateSelectedSeatsDisplay();
            }
        });

        document.getElementById('confirmSeats').addEventListener('click', () => {
            if (selectedSeats.size === 0) {
                alert('Por favor, selecciona al menos un asiento.');
                return;
            }

            // Convertir el set de asientos seleccionados en un array
            const seatsArray = Array.from(selectedSeats).map(seat => ({
                numero: seat.id, // Asegúrate de que 'id' sea el identificador correcto
                categoria: seat.category // Asegúrate de que 'category' sea el campo correcto
            }));

            // Pasar los asientos seleccionados al campo oculto
            const jsonSeats = JSON.stringify(seatsArray);
            document.getElementById('asientos_seleccionados').value = jsonSeats;

            // Calcular y actualizar el total
            let total = parseFloat(document.getElementById('costo_destino').value) || 0; // Inicia el total con el costo del destino
            seatsArray.forEach(seat => {
                switch (seat.category) {
                    case 'vip':
                        total += 15;
                        break;
                    case 'estandar':
                        total += 10;
                        break;
                    case 'economico':
                        total += 8;
                        break;
                    default:
                        console.error('Categoría de asiento no válida');
                }
            });

            // Actualizar el total mostrado
            document.getElementById('total').value = total.toFixed(2); // Asegúrate de que se muestre correctamente

            // Cerrar el modal
            closeModal();
        });

        function updateSelectedSeatsDisplay() {
            const selectedSeatsArray = Array.from(selectedSeats).map(seat => seat.id);
            document.getElementById('asientos_mostrados').value = selectedSeatsArray.join(', ');
        }

        function updateDestinoOptions() {
            const origenSelect = document.getElementById("origen");
            const destinoSelect = document.getElementById("destino");
            const ciudadOrigen = origenSelect.value;

            // Limpiar opciones de destino
            destinoSelect.innerHTML = "<option value=''>Selecciona una ciudad</option>";

            // Agregar las ciudades de destino que no sean la ciudad de origen
            <?php foreach ($ciudades as $ciudad): ?>
                if ("<?php echo htmlspecialchars($ciudad['nombre_ciudad']); ?>" !== ciudadOrigen) {
                    const option = document.createElement("option");
                    option.value = "<?php echo htmlspecialchars($ciudad['nombre_ciudad']); ?>";
                    option.textContent = "<?php echo htmlspecialchars($ciudad['nombre_ciudad']); ?>";
                    destinoSelect.appendChild(option);
                }
            <?php endforeach; ?>
        }

        document.getElementById('destino').addEventListener('change', function() {
            const destino = this.value; // Obtener el valor seleccionado
            console.log('Destino seleccionado:', destino); // Verifica el valor aquí

            if (destino) {
                fetch(`get_costo.php?destino=${encodeURIComponent(destino)}`)
                .then(response => response.text())  // Usamos .text() para ver la respuesta cruda
                .then(data => {
                    console.log('Respuesta cruda:', data);  // Imprime la respuesta del servidor
                    try {
                        const jsonResponse = JSON.parse(data);  // Intenta convertir la respuesta a JSON
                        console.log('Respuesta de get_costo.php:', jsonResponse);  // Muestra la respuesta JSON
                        if (jsonResponse.error) {
                            console.error(jsonResponse.error);  // Si hay un error en la respuesta
                            document.getElementById('costo_destino').value = '0';
                        } else {
                            document.getElementById('costo_destino').value = jsonResponse.costo;
                        }
                    } catch (error) {
                        console.error('Error al intentar parsear el JSON:', error);
                    }
                })
                .catch(error => {
                    console.error('Error al obtener el costo:', error);
                });
            } else {
                document.getElementById('costo_destino').value = '0'; // Si no hay destino seleccionado, establecer el costo en 0
            }
        });

        var_dump($destino); // Verifica si el destino se recibe correctamente
        </script>
</body>
</html>