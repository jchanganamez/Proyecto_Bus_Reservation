<?php
// HomeController.php
class HomeController {
    private $db;
    private $destinationModel;

    public function __construct() {
        require_once '../config/database.php';
        require_once '../models/Destination.php';

        $database = new Database();
        $this->db = $database->getConnection();
        $this->destinationModel = new Destination($this->db);
    }

    public function index() {
        // Obtener todas las ciudades
        $cities = $this->destinationModel->getAllCities()->fetchAll(PDO::FETCH_ASSOC);

        // Incluir la vista
        include '../views/home.php';
    }
}
?>