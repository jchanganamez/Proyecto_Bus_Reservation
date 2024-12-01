<?php
// models/Destination.php
class Destination {
    private $conn;
    private $table_name = "destinos"; // Cambia esto por el nombre correcto de tu tabla

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getAllCities() {
        $query = "SELECT nombre_ciudad FROM " . $this->table_name;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt; // Devuelve el resultado para que sea utilizado en el controlador
    }
}
?>