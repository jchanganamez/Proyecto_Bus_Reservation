<?php
class TripController {
    private $db;
    private $trip;

    public function __construct() {
        require_once(dirname(__DIR__) . '../config/database.php');
        require_once(dirname(__DIR__) . '../models/Trip.php');

        $database = new Database();
        $this->db = $database->getConnection();
        $this->trip = new Trip($this->db);
    }

    public function searchTrips() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $origin = $_POST['origin'] ?? '';
            $destination = $_POST['destination'] ?? '';
            $date = $_POST['date'] ?? '';

            if (empty($origin) || empty($destination) || empty($date)) {
                return json_encode(['error' => 'All search fields are required']);
            }

            $stmt = $this->trip->searchTrips($origin, $destination, $date);
            $trips = [];
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                array_push($trips, $row);
            }
            return json_encode($trips);
        }
    }

    public function getTrip() {
        return $this->trip;
    }

    public function getAllTrips() {
        if (!$this->isAdmin()) {
            return json_encode(['error' => 'Unauthorized access']);
        }
    
        $trips = $this->trip->readTrips(); // Ya devuelve un array
        return $trips; // Devuelve directamente el array
    }
    
    public function getBusModels() {
        $query = "SELECT id, modelo FROM buses";
        return $this->db->query($query)->fetchAll();
    }
    
    public function getDrivers() {
        $query = "SELECT id, nombre FROM conductores";
        return $this->db->query($query)->fetchAll();
    }
    

    public function createTrip() {
        if (!$this->isAdmin()) {
            return json_encode(['error' => 'Unauthorized access']);
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->trip->origin = $_POST['origin'] ?? '';
            $this->trip->destination = $_POST['destination'] ?? '';
            $this->trip->departure_date = $_POST['departure_date'] ?? '';
            $this->trip->arrival_date = $_POST['arrival_date'] ?? '';
            $this->trip->bus_id = $_POST['bus_id'] ?? '';
            $this->trip->price = $_POST['price'] ?? '';
            $this->trip->status = 'En espera';

            if ($this->trip->create()) {
                return json_encode(['success' => true, 'message' => 'Trip created successfully']);
            }
            return json_encode(['error' => 'Unable to create trip']);
        }
    }

    public function updateTrip() {
        if (!$this->isAdmin()) {
            return json_encode(['error' => 'Unauthorized access']);
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->trip->id = $_POST['id'] ?? '';
            $this->trip->origin = $_POST['origin'] ?? '';
            $this->trip->destination = $_POST['destination'] ?? '';
            $this->trip->departure_date = $_POST['departure_date'] ?? '';
            $this->trip->arrival_date = $_POST['arrival_date'] ?? '';
            $this->trip->bus_id = $_POST['bus_id'] ?? '';
            $this->trip->price = $_POST['price'] ?? '';
            $this->trip->status = $_POST['status'] ?? 'active';

            if ($this->trip->update()) {
                return json_encode(['success' => true, 'message' => 'Trip updated successfully']);
            }
            return json_encode(['error' => 'Unable to update trip']);
        }
    }

    // En el controlador, en la función deleteTrip()
public function deleteTrip($id) {
    $tripModel = new Trip($this->db);
    if ($tripModel->delete($id)) {
        return true;
    }
    return false;
}


    private function isAdmin() {
        return isset($_SESSION['es_admin']) && $_SESSION['es_admin'] == 1;
    }
}
?>