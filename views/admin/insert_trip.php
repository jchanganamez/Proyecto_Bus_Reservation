<?php
session_start(); // Asegúrate de iniciar la sesión al inicio del archivo

require_once '../../models/Trip.php';
require_once '../../config/database.php';

$database = new Database();
$conn = $database->getConnection();

$updateAsiento = new Trip($conn); // Instanciar el objeto de la clase Trip para actualizar asientos
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Verificar si el usuario está autenticado
        if (!isset($_SESSION['user_id'])) {
            throw new Exception("El usuario no está autenticado.");
        }

        // Obtener el ID del usuario desde la sesión
        $usuarioId = $_SESSION['user_id'];

        // Obtener los datos del formulario
        $origen = $_POST['origen'];
        $destino = $_POST['destino'];
        $fecha_salida = $_POST['fecha_salida'];
        $fecha_llegada = $_POST['fecha_llegada'];
        $bus_id = $_POST['bus_id'];
        $asientosSeleccionados = json_decode($_POST['asientos_seleccionados'], true);
        $total = $_POST['total'];

        // Obtener el conductor_id correspondiente al bus_id
        $stmtConductor = $conn->prepare("SELECT conductor_id FROM buses WHERE id = :bus_id");
        $stmtConductor->bindParam(':bus_id', $bus_id, PDO::PARAM_INT);
        $stmtConductor->execute();
        $conductor = $stmtConductor->fetch(PDO::FETCH_ASSOC);

        if (!$conductor) {
            throw new Exception("No se encontró el conductor para el bus seleccionado.");
        }
        $conductorId = $conductor['conductor_id']; // Obtener el conductor_id

        // Instanciar el objeto de la clase Trip y asignar los valores
        $viaje = new Trip($conn);
        $viaje->origen = $origen;
        $viaje->destino = $destino;
        $viaje->fecha_salida = $fecha_salida;
        $viaje->fecha_llegada = $fecha_llegada;
        $viaje->bus_id = $bus_id;
        $viaje->conductor_id = $conductorId;
        $viaje->user_id = $usuarioId;
        $viaje->seats = $asientosSeleccionados; // Asignar los asientos seleccionados
        $viaje->total = $total; // Establece el valor total de acuerdo a la lógica que manejes (por ejemplo, suma de precios)

        // Crear el viaje (insertar en la base de datos)
        if ($viaje->create()) {
            // Si el viaje se crea exitosamente, actualizar los asientos
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new Exception("Error al decodificar JSON: " . json_last_error_msg());
            }
            foreach ($asientosSeleccionados as $asiento) {
                $updateAsiento->bus_id = $bus_id;
                $updateAsiento->asiento = $asiento['numero']; // Asegúrate de que 'numero' sea correcto
                if (!$updateAsiento->updateSeatStatus()) {
                    throw new Exception("Error al actualizar el estado del asiento: " . $asiento['numero']);
                }
            }

            // Redirigir a la página de viajes después de crear el viaje
            header('Location: viajes.php');
            exit(); // Es importante hacer un exit después de header para asegurar que el script se detenga aquí
        } else {
            throw new Exception("Error al crear el viaje.");
        }
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    } catch (PDOException $e) {
        echo "Error al crear el viaje: " . $e->getMessage();
    }
}
?>
