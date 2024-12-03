<?php
class Bus {
    private $conn;
    private $table_name = "buses";

    public $id;
    public $numero; 
    public $capacidad; 
    public $modelo; 
    public $created_at; 

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
    
        // Sanitize y vincular valores
        $this->numero = htmlspecialchars(strip_tags($this->numero));
        $this->capacidad = htmlspecialchars(strip_tags($this->capacidad));
        $this->modelo = htmlspecialchars(strip_tags($this->modelo));
        $this->conductor_id = htmlspecialchars(strip_tags($this->conductor_id));
    
        // Vincular valores
        $stmt->bindParam(":numero", $this->numero);
        $stmt->bindParam(":capacidad", $this->capacidad);
        $stmt->bindParam(":modelo", $this->modelo);
        $stmt->bindParam(":conductor_id", $this->conductor_id);
    
        // Ejecutar la consulta para insertar el bus
        if ($stmt->execute()) {
            // Obtener el ID del bus recién creado
            $bus_id = $this->conn->lastInsertId();
    
            // Verificar que la capacidad sea al menos 30
            if ($this->capacidad < 30) {
                throw new Exception("El bus debe tener al menos 30 asientos.");
            }
    
            // Crear asientos
            $totalAsientos = $this->capacidad;
            $asientosCreados = [];
    
            // Crear asientos VIP
            for ($i = 1; $i <= 20; $i++) {
                $asientosCreados[] = [
                    'numero_asiento' => $i,
                    'categoria' => 'vip',
                    'estado' => 'disponible',
                    'bus_id' => $bus_id
                ];
            }
    
            // Crear asientos estándar
            for ($i = 21; $i <= 30; $i++) {
                $asientosCreados[] = [
                    'numero_asiento' => $i,
                    'categoria' => 'estandar',
                    'estado' => 'disponible',
                    'bus_id' => $bus_id
                ];
            }
    
            // Crear asientos económicos
            for ($i = 31; $i <= $totalAsientos; $i++) {
                $asientosCreados[] = [
                    'numero_asiento' => $i,
                    'categoria' => 'economico',
                    'estado' => 'disponible',
                    'bus_id' => $bus_id
                ];
            }
    
            // Insertar asientos en la base de datos
            $queryInsertAsientos = "INSERT INTO asientos (numero_asiento, categoria, estado, bus_id) VALUES (:numero_asiento, :categoria, :estado, :bus_id)";
            
            $stmtInsertAsientos = $this->conn->prepare($queryInsertAsientos);
    
            foreach ($asientosCreados as $asiento) {
                $stmtInsertAsientos->bindParam(':numero_asiento', $asiento['numero_asiento']);
                $stmtInsertAsientos->bindParam(':categoria', $asiento['categoria']);
                $stmtInsertAsientos->bindParam(':estado', $asiento['estado']);
                $stmtInsertAsientos->bindParam(':bus_id', $asiento['bus_id']);
                $stmtInsertAsientos->execute();
            }
    
            return true; // Retornar true si todo fue exitoso
        }
    
        return false; // Retornar false si hubo un error al crear el bus
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

    public function busHasTripsB($busId) {
        $query = "SELECT COUNT(*) as total FROM viajes WHERE bus_id = :bus_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':bus_id', $busId, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $result['total'] > 0; // Retorna true si hay viajes asignados
    }
}
?>