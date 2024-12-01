<?php
class UserController {
    private $db;
    private $user;

    public function __construct() {
        require_once(dirname(__DIR__) . '/config/database.php');
        require_once(dirname(__DIR__) . '/models/User.php');

        $database = new Database();
        $this->db = $database->getConnection();
        $this->user = new User($this->db);
    }

    public function login() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';

            if (empty($email) || empty($password)) {
                return json_encode(['error' => 'Email and password are required']);
            }

            $result = $this->user->login($email, $password);
            if ($result) {
                session_start();
                $_SESSION['user_id'] = $result['id'];
                $_SESSION['user_name'] = $result['nombre'];
                $_SESSION['es_admin'] = $result['es_admin'];

                return json_encode(['success' => true, 'redirect' => $result['es_admin'] ? '/admin/dashboard.html' : '/index.html']);
            }

            return json_encode(['error' => 'Invalid credentials']);
        }
    }

    public function register() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->user->nombre = $_POST['nombre'] ?? '';
            $this->user->email = $_POST['email'] ?? '';
            $this->user->password = password_hash($_POST['password'] ?? '', PASSWORD_DEFAULT);
            $this->user->telefono = $_POST['telefono'] ?? '';
            $this->user->es_admin = isset($_POST['es_admin']) ? (int)$_POST['es_admin'] : 0;

            if (empty($this->user->nombre) || empty($this->user->email) || empty($this->user->password)) {
                return json_encode(['error' => 'All fields are required']);
            }

            if ($this->user->create()) {
                return json_encode(['success' => true, 'message' => 'User registered successfully']);
            }

            return 'Usuario registrado exitosamente';
        }
    }

    public function logout() {
        session_start();
        session_destroy();
        return json_encode(['success' => true, 'redirect' => '/login.html']);
    }

    public function getAllUsers() {
        if (!$this->isAdmin()) {
            return json_encode(['error' => 'Unauthorized access']);
        }

        $stmt = $this->user->read();
        $users = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            array_push($users, $row);
        }
        return json_encode($users);
    }

    public function getUserById($id) {
        if (!$this->isAdmin()) {
            return json_encode(['error' => 'Unauthorized access']);
        }
    
        if (!is_numeric($id)) {
            return json_encode(['error' => 'Invalid user ID']);
        }
    
        $userData = $this->user->getById($id); // Pasar $id aquí
    
        if ($userData) {
            return json_encode($userData);
        }
        return json_encode(['error' => 'User not found']);
    }
    

    public function updateUser () {
        if (!$this->isAdmin()) {
            return json_encode(['error' => 'Unauthorized access']);
        }
    
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Validar que todos los campos estén presentes
            if (!isset($_POST['id'], $_POST['nombre'], $_POST['email'], $_POST['telefono'], $_POST['es_admin'])) {                
                return json_encode(['error' => 'Faltan datos para actualizar el usuario']);
            }
        
            // Asignar valores
            $this->user->id = $_POST['id'];
            $this->user->nombre = $_POST['nombre'];
            $this->user->email = $_POST['email'];
            $this->user->telefono = $_POST['telefono'];
            $this->user->es_admin = (int) $_POST['es_admin'];
        
            // Intentar la actualización
            if ($this->user->update()) {
                return json_encode(['success' => true, 'message' => 'User  updated successfully']);
            }
        
            return json_encode(['error' => 'Unable to update user']);
        }
    }

    public function deleteUser() {
        if (!$this->isAdmin()) {
            return json_encode(['error' => 'Unauthorized access']);
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->user->id = $_POST['id'] ?? '';

            if ($this->user->delete()) {
                return json_encode(['success' => true, 'message' => 'User deleted successfully']);
            }
            return json_encode(['error' => 'Unable to delete user']);
        }
    }

    private function isAdmin() {
        return isset($_SESSION['es_admin']) && $_SESSION['es_admin'] == 1;
    }
}
?>
