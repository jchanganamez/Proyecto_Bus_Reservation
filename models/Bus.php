<?php
class Bus {
    private $conn;
    private $table_name = "buses";

    public $id;
    public $numero; // Cambié 'plate' a 'numero'
    public $capacidad; // Cambié 'capacity' a 'capacidad'
    public $modelo; // Se mantiene 'modelo'
    public $created_at; // Se mantiene 'created_at'

    public function __construct($db) {
        $this->conn = $db;
    }

    public function create() {
        $query = "INSERT INTO " . $this->table_name . "
                SET
                    numero = :numero,
                    capacidad = :capacidad,
                    modelo = :modelo,
                    conductor_id = :conductor_id,
                    created_at = CURRENT_TIMESTAMP";

        $stmt = $this->conn->prepare($query);

        // Sanitize
        $this->numero = htmlspecialchars(strip_tags($this->numero));
        $this->capacidad = htmlspecialchars(strip_tags($this->capacidad));
        $this->modelo = htmlspecialchars(strip_tags($this->modelo));
        $this->conductor_id = htmlspecialchars(strip_tags($this->conductor_id));

        // Bind values
        $stmt->bindParam(":numero", $this->numero);
        $stmt->bindParam(":capacidad", $this->capacidad);
        $stmt->bindParam(":modelo", $this->modelo);
        $stmt->bindParam(":conductor_id", $this->conductor_id);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function read() {
        // Modificamos la consulta para incluir el nombre del conductor
        $query = "
            SELECT 
                buses.id, 
                buses.modelo, 
                buses.numero, 
                buses.capacidad, 
                conductores.nombre AS conductor
            FROM 
                " . $this->table_name . " AS buses
            LEFT JOIN 
                conductores 
            ON 
                buses.conductor_id = conductores.id
        ";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt; // Devuelve los datos con el nombre del conductor
    }
    

    public function obtenerConductores() {
        $query = "SELECT * FROM conductores";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    public function update() {
        $query = "UPDATE " . $this->table_name . "
                SET
                    numero = :numero,
                    capacidad = :capacidad,
                    modelo = :modelo,
                    conductor_id = :conductor_id
                WHERE id = :id";

        $stmt = $this->conn->prepare($query);

        // Sanitize
        $this->numero = htmlspecialchars(strip_tags($this->numero));
        $this->capacidad = htmlspecialchars(strip_tags($this->capacidad));
        $this->modelo = htmlspecialchars(strip_tags($this->modelo));
        $this->conductor_id = htmlspecialchars(strip_tags($this->conductor_id));
        $this->id = htmlspecialchars(strip_tags($this->id));

        // Bind values
        $stmt->bindParam(":numero", $this->numero);
        $stmt->bindParam(":capacidad", $this->capacidad);
        $stmt->bindParam(":modelo", $this->modelo);
        $stmt->bindParam(":conductor_id", $this->conductor_id);
        $stmt->bindParam(":id", $this->id);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function delete() {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $this->id = htmlspecialchars(strip_tags($this->id));
        $stmt->bindParam(1, $this->id);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }
    
    public function getBusById($bus_id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = :bus_id"; // SQL query
        $stmt = $this->conn->prepare($query); // Prepare the query
        $stmt->bindParam(':bus_id', $bus_id, PDO::PARAM_INT); // Bind the parameter
        $stmt->execute(); // Execute the query
        return $stmt; // Return the statement object
    }

    public function getAsientosByBusId($bus_id) {
        $bus_id = intval($bus_id); // Asegúrate de que bus_id sea un número
        $stmt = $this->conn->prepare("SELECT * FROM asientos WHERE bus_id = :bus_id"); // Cambia $this->db a $this->conn
        $stmt->bindParam(':bus_id', $bus_id, PDO::PARAM_INT); // Vincular el parámetro
        $stmt->execute(); // Ejecutar la consulta

        return $stmt->fetchAll(PDO::FETCH_ASSOC); // Retornar todos los asientos encontrados
    }
}
?>