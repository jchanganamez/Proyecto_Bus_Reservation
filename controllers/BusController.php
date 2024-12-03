<?php
class BusController {
    private $db;
    private $bus;

    public function __construct() {
        require_once(dirname(__DIR__) . '/config/database.php');
        require_once(dirname(__DIR__) . '/models/Bus.php');
        $database = new Database();
        $this->db = $database->getConnection();
        $this->bus = new Bus($this->db);
    }

    public function obtenerTodosLosBuses() {
        $stmt = $this->bus->read(); // Llama al método read() del modelo Bus
        $buses = [];
    
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            array_push($buses, $row);
        }
        return $buses;
    }

    public function obtenerTodosLosConductores() {
        $stmt = $this->bus->obtenerConductores(); // Llama al método read() del modelo Bus
        $buses = [];
    
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            array_push($buses, $row);
        }
        return $buses;
    }

    public function obtenerBus($bus_id) {
        $bus_id = intval($bus_id);
        $stmt = $this->bus->getBusById($bus_id);
        
        if ($stmt->rowCount() > 0) {
            return json_encode($stmt->fetch(PDO::FETCH_ASSOC)); // Convert to JSON
        } else {
            return json_encode(null); // Return null as JSON
        }
    }    

    public function obtenerAsientos($bus_id) {
        $bus_id = intval($bus_id); // Asegúrate de que bus_id sea un número
        $asientos = $this->bus->getAsientosByBusId($bus_id); // Llama al método en el modelo Bus
    
        if (empty($asientos)) {
            return []; // Retorna un array vacío si no hay asientos
        }
    
        return $asientos; // Devuelve la lista de asientos
    }

    public function getAllBuses() {
        if (!$this->isAdmin()) {
            return json_encode(['error' => 'Unauthorized access']);
        }

        $stmt = $this->bus->read();
        $buses = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            array_push($buses, $row);
        }
        var_dump($buses);
        return json_encode($buses);
    }

    public function createBus() {
        if (!$this->isAdmin()) {
            return json_encode(['error' => 'Unauthorized access']);
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->bus->modelo = $_POST['modelo'] ?? '';
            $this->bus->numero = $_POST['numero'] ?? '';
            $this->bus->capacidad = $_POST['capacidad'] ?? '';
            $this->bus->conductor_id = $_POST['conductor_id'] ?? '';

            if (empty($this->bus->modelo) || empty($this->bus->numero) || empty($this->bus->capacidad) || empty($this->bus->conductor_id)) {
                return json_encode(['error' => 'All fields are required']);
            }

            if ($this->bus->create()) {
                return json_encode(['success' => true, 'message' => 'Bus created successfully']);
            }
            return json_encode(['error' => 'Unable to create bus']);
        }
    }

    public function updateBus($busId, $numero, $capacidad, $modelo, $conductor_id) {
        if (!$this->isAdmin()) {
            return json_encode(['error' => 'Unauthorized access']);
        }
    
        $this->bus->id = $busId;
        $this->bus->modelo = $modelo;
        $this->bus->numero = $numero;
        $this->bus->capacidad = $capacidad;
        $this->bus->conductor_id = $conductor_id;
    
        if ($this->bus->update()) {
            return json_encode(['success' => true, 'message' => 'Bus updated successfully']);
        }
        return json_encode(['error' => 'Unable to update bus']);
    }

    public function deleteBus() {
        if (!$this->isAdmin()) {
            return json_encode(['error' => 'Unauthorized access']);
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->bus->id = $_POST['id'] ?? '';

            if ($this->bus->delete()) {
                return json_encode(['success' => true, 'message' => 'Bus deleted successfully']);
            }
            return json_encode(['error' => 'Unable to delete bus']);
        }
    }

    public function busHasTrips($userId) {
        return $this->bus->busHasTripsB($userId); // Llamar al método del modelo User
    }

    private function isAdmin() {
        return isset($_SESSION['es_admin']) && $_SESSION['es_admin'] == 1;
    }
}
?>