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

    // Eliminar usuario
    public function delete() {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);

        $this->id = htmlspecialchars(strip_tags($this->id));
        $stmt->bindParam(1, $this->id);

        return $stmt->execute();
    }
}
