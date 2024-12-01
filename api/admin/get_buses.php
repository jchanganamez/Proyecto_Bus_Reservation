<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

require_once '../controllers/BusController.php';

$controller = new BusController();
$controller->obtenerTodosLosBuses(); // Asegúrate de que este método esté devolviendo los buses
?>