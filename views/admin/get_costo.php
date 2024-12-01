<?php
session_start();
require_once '../../config/database.php';

$database = new Database();
$conn = $database->getConnection();

$destino = filter_input(INPUT_GET, 'destino', FILTER_SANITIZE_STRING);

if ($destino) {
    $query = "SELECT costo FROM destinos WHERE nombre_ciudad = :destino";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':destino', $destino);
    
    if ($stmt->execute()) {
        $destinoData = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($destinoData) {
            echo json_encode(['costo' => $destinoData['costo']]);
        } else {
            echo json_encode(['error' => 'No se encontró el costo para el destino seleccionado.']);
        }
    } else {
        echo json_encode(['error' => 'Error en la consulta a la base de datos.']);
    }
} else {
    echo json_encode(['error' => 'Destino no proporcionado.']);
}
?>