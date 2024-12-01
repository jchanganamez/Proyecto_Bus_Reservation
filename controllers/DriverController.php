<?php
class DriverController {
    private $db;
    private $driver;

    public function __construct() {
        require_once(dirname(__DIR__) . '../config/database.php');
        require_once(dirname(__DIR__) . '../models/Driver.php');

        $database = new Database();
        $this->db = $database->getConnection();
        $this->driver = new Driver($this->db);
    }

    public function createDriver() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->driver->nombre = $_POST['nombre'] ?? '';
            $this->driver->licencia = $_POST['licencia'] ?? '';
            $this->driver->telefono = $_POST['telefono'] ?? '';

            if ($this->driver->create()) {
                return json_encode(['success' => true, 'message' => 'Conductor creado exitosamente']);
            }
            return json_encode(['error' => 'No se pudo crear el conductor']);
        }
    }

    public function readDrivers() {
        $stmt = $this->driver->read();
        $drivers = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $drivers[] = $row;
        }
        return json_encode($drivers);
    }

    public function updateDriver($id, $nombre, $licencia, $telefono) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Validar que todos los campos estén presentes
            if (empty($nombre) || empty($licencia) || empty($telefono)) {
                return json_encode(['error' => 'Faltan datos para actualizar el conductor']);
            }
    
            // Asignar valores al objeto driver
            $this->driver->id = $id;
            $this->driver->nombre = $nombre;
            $this->driver->licencia = $licencia;
            $this->driver->telefono = $telefono;
    
            // Intentar la actualización
            if ($this->driver->update()) {
                return json_encode(['success' => true, 'message' => 'Conductor actualizado exitosamente']);
            }
    
            return json_encode(['error' => 'No se pudo actualizar el conductor']);
        }
    }
    

    public function deleteDriver($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete') {
            // Primero, obtenemos la información de la tabla buses para verificar si el conductor tiene un bus asignado
            $query = "SELECT conductor_id FROM buses WHERE conductor_id = :conductor_id LIMIT 1"; // Usamos conductor_id
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':conductor_id', $id);
            $stmt->execute();
            $bus = $stmt->fetch(PDO::FETCH_ASSOC);
    
            if ($bus) {
                // Si el conductor tiene un bus asignado, eliminamos la asignación de conductor en la tabla buses
                $query = "UPDATE buses SET conductor_id = NULL WHERE conductor_id = :conductor_id"; // Usamos conductor_id
                $stmt = $this->db->prepare($query);
                $stmt->bindParam(':conductor_id', $id);
                $stmt->execute();
            }
    
            // Ahora eliminamos al conductor de la tabla 'conductores'
            $this->driver->id = $id;
        
            if ($this->driver->delete()) {
                return json_encode(['success' => true, 'message' => 'Conductor y asignación de bus eliminados exitosamente']);
            }
            return json_encode(['error' => 'No se pudo eliminar el conductor']);
        }
    }
    
    public function getDriverById($id) {
        $driver = $this->driver->getById($id);
        return json_encode($driver);
    }
}
?>