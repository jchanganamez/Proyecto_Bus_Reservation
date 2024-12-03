<?php
class User {
    private $conn;
    private $table_name = "usuarios";

    public $id;
    public $nombre;
    public $email;
    public $password;
    public $es_admin;
    public $telefono;
    public $created_at;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Verificar si un email ya existe
    public function emailExists() {
        $query = "SELECT id FROM " . $this->table_name . " WHERE email = :email LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':email', $this->email);
        $stmt->execute();
        return $stmt->rowCount() > 0; // Devuelve true si el email ya existe
    }

    // Crear usuario
    public function create() {
        $query = "INSERT INTO " . $this->table_name . "
                  SET
                      nombre = :nombre,
                      email = :email,
                      password = :password,
                      es_admin = :es_admin,
                      telefono = :telefono";

        $stmt = $this->conn->prepare($query);

        // Sanitizar y procesar
        $this->nombre = htmlspecialchars(strip_tags($this->nombre));
        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->password = password_hash($this->password, PASSWORD_BCRYPT);
        $this->es_admin = htmlspecialchars(strip_tags($this->es_admin));
        $this->telefono = htmlspecialchars(strip_tags($this->telefono));

        // Vincular valores
        $stmt->bindParam(":nombre", $this->nombre);
        $stmt->bindParam(":email", $this->email);
        $stmt->bindParam(":password", $this->password);
        $stmt->bindParam(":es_admin", $this->es_admin);
        $stmt->bindParam(":telefono", $this->telefono);

        return $stmt->execute();
    }

    // Iniciar sesión
    public function login($email, $password) {
        $query = "SELECT id, nombre, email, password, es_admin, telefono 
                  FROM " . $this->table_name . " WHERE email = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $email);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if (password_verify($password, $row['password'])) {
                return $row; // Credenciales correctas
            } else {
                return 'PASSWORD_INCORRECT'; // Contraseña incorrecta
            }
        }
        return 'EMAIL_NOT_FOUND'; // Correo electrónico no encontrado
    }

    // Leer todos los usuarios
    public function read() {
        $query = "SELECT id, nombre, email, es_admin, telefono, created_at 
                  FROM " . $this->table_name;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    public function getById($id) {
        try {
            $query = "SELECT id, nombre, email, telefono, es_admin FROM usuarios WHERE id = :id LIMIT 1";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return false; // Manejar el error de manera apropiada
        }
    }

    // Actualizar usuario
    public function update() {
        $query = "UPDATE " . $this->table_name . "
                  SET
                      nombre = :nombre,
                      email = :email,
                      es_admin = :es_admin,
                      telefono = :telefono
                  WHERE id = :id";

        $stmt = $this->conn->prepare($query);

        // Sanitizar
        $this->id = (int) htmlspecialchars(strip_tags($this->id));
        $this->es_admin = (int) htmlspecialchars(strip_tags($this->es_admin));
        $this->telefono = htmlspecialchars(strip_tags($this->telefono));
        $this->email = filter_var($this->email, FILTER_SANITIZE_EMAIL);
        $this->nombre = htmlspecialchars(strip_tags($this->nombre));


        // Vincular valores
        $stmt->bindParam(":nombre", $this->nombre);
        $stmt->bindParam(":email", $this->email);
        $stmt->bindParam(":es_admin", $this->es_admin);
        $stmt->bindParam(":telefono", $this->telefono);
        $stmt->bindParam(":id", $this->id);
        return $stmt->execute();
    }

    public function delete() {
        $this->id = htmlspecialchars(strip_tags($this->id));
    
        try {
            // Iniciar transacción
            $this->conn->beginTransaction();
    
            // Paso 1: Obtener los asientos ocupados por el usuario
            $queryAsientos = "
                SELECT dv.asiento 
                FROM detalle_viaje dv 
                JOIN viajes v ON dv.viaje_id = v.id 
                WHERE v.user_id = :user_id
            ";
            $stmtAsientos = $this->conn->prepare($queryAsientos);
            $stmtAsientos->bindParam(':user_id', $this->id);
            $stmtAsientos->execute();
            $asientosOcupados = $stmtAsientos->fetchAll(PDO::FETCH_COLUMN); // Obtener solo los números de asientos
    
            // Paso 2: Actualizar los asientos a 'disponible'
            if (!empty($asientosOcupados)) {
                $placeholders = implode(',', array_fill(0, count($asientosOcupados), '?'));
                $queryActualizarAsientos = "
                    UPDATE asientos 
                    SET estado = 'disponible' 
                    WHERE numero_asiento IN ($placeholders)
                ";
                $stmtActualizar = $this->conn->prepare($queryActualizarAsientos);
                $stmtActualizar->execute($asientosOcupados); // Ejecutar la consulta con los asientos ocupados
            }
    
            // Paso 3: Eliminar referencias en la tabla 'pagos'
            $queryPagos = "DELETE FROM pagos WHERE user_id = :user_id";
            $stmtPagos = $this->conn->prepare($queryPagos);
            $stmtPagos->bindParam(':user_id', $this->id);
            $stmtPagos->execute();
    
            // Paso 4: Eliminar referencias en la tabla 'detalle_viaje'
            $queryDetalleViaje = "DELETE FROM detalle_viaje WHERE usuario_id = :usuario_id";
            $stmtDetalleViaje = $this->conn->prepare($queryDetalleViaje);
            $stmtDetalleViaje->bindParam(':usuario_id', $this->id);
            $stmtDetalleViaje->execute();
    
            // Paso 5: Eliminar referencias en la tabla 'viajes'
            $queryViajes = "DELETE FROM viajes WHERE user_id = :user_id";
            $stmtViajes = $this->conn->prepare($queryViajes);
            $stmtViajes->bindParam(':user_id', $this->id);
            $stmtViajes->execute();
    
            // Paso 6: Finalmente, eliminar el usuario de la tabla 'usuarios'
            $queryDeleteUser   = "DELETE FROM " . $this->table_name . " WHERE id = :user_id";
            $stmtDeleteUser   = $this->conn->prepare($queryDeleteUser  );
            $stmtDeleteUser ->bindParam(':user_id', $this->id);
            
            // Ejecutar la eliminación del usuario
            $stmtDeleteUser ->execute();
    
            // Si todo fue exitoso, confirmar la transacción
            $this->conn->commit();
            return true; // Retornar true si la eliminación fue exitosa
        } catch (Exception $e) {
            // Si ocurre un error, revertir la transacción
            $this->conn->rollBack();
            return false; // Retornar false si hubo un error
        }
    }

    public function deleteUserSimple($userId) {
        // Establecer el ID del usuario a eliminar
        $this->user->id = $userId; // Asignar el ID del usuario
    
        // Llamar al método delete en el modelo User
        if ($this->user->delete()) {
            return true; // Retornar true si la eliminación fue exitosa
        }
        return false; // Retornar false si hubo un error
    }


    public function userHasTripsC($userId) {
        $query = "SELECT COUNT(*) FROM viajes WHERE user_id = :user_id";
        $stmt = $this->conn->prepare($query); // Aquí usamos $this->conn
        $stmt->bindParam(':user_id', $userId);
        $stmt->execute();
        
        return $stmt->fetchColumn() > 0; // Retorna verdadero si hay viajes
    }

    public function updateProfile() {
        session_start();
        if (!isset($_SESSION['user_id'])) {
            return json_encode(['error' => 'User not authenticated']);
        }
    
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->user->id = $_SESSION['user_id'];
            $this->user->nombre = $_POST['nombre'] ?? '';
            $this->user->email = $_POST['email'] ?? '';
            $this->user->telefono = $_POST['telefono'] ?? '';
    
            // Validar que los campos no estén vacíos
            if (empty($this->user->nombre) || empty($this->user->email)) {
                return json_encode(['error' => 'Name and email are required']);
            }
    
            // Validar email
            if (!filter_var($this->user->email, FILTER_VALIDATE_EMAIL)) {
                return json_encode(['error' => 'Invalid email format']);
            }
    
            if ($this->user->update()) {
                return json_encode(['success' => true, 'message' => 'Profile updated successfully']);
            }
    
            return json_encode(['error' => 'Failed to update profile']);
        }
    }
    
    
}
