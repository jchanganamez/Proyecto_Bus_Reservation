<?php
class Driver {
    private $conn;
    private $table_name = "conductores"; // Cambia esto por el nombre correcto de tu tabla

    public $id;
    public $nombre;
    public $licencia;
    public $telefono;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function create() {
        $query = "INSERT INTO " . $this->table_name . " SET nombre = :nombre, licencia = :licencia, telefono = :telefono";
        $stmt = $this->conn->prepare($query);

        // Sanitizar
        $this->nombre = htmlspecialchars(strip_tags($this->nombre));
        $this->licencia = htmlspecialchars(strip_tags($this->licencia));
        $this->telefono = htmlspecialchars(strip_tags($this->telefono));

        // Vincular valores
        $stmt->bindParam(":nombre", $this->nombre);
        $stmt->bindParam(":licencia", $this->licencia);
        $stmt->bindParam(":telefono", $this->telefono);

        return $stmt->execute();
    }

    public function read() {
        $query = "SELECT * FROM " . $this->table_name;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    public function update() {
        $query = "UPDATE " . $this->table_name . "
                  SET
                      nombre = :nombre,
                      licencia = :licencia,
                      telefono = :telefono
                  WHERE id = :id";
    
        $stmt = $this->conn->prepare($query);
    
        // Sanitizar los valores
        $this->id = (int) htmlspecialchars(strip_tags($this->id));
        $this->nombre = htmlspecialchars(strip_tags($this->nombre));
        $this->licencia = htmlspecialchars(strip_tags($this->licencia));
        $this->telefono = htmlspecialchars(strip_tags($this->telefono));
    
        // Vincular valores
        $stmt->bindParam(":nombre", $this->nombre);
        $stmt->bindParam(":licencia", $this->licencia);
        $stmt->bindParam(":telefono", $this->telefono);
        $stmt->bindParam(":id", $this->id);
    
        return $stmt->execute();
    }    

    public function delete() {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);

        // Sanitizar
        $this->id = htmlspecialchars(strip_tags($this->id));
        $stmt->bindParam(":id", $this->id);

        return $stmt->execute();
    }

    public function getById($id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = :id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>