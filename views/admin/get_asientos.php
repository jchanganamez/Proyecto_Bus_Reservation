<?php
require_once '../../config/database.php';

$database = new Database();
$conn = $database->getConnection();

$busId = $_GET['bus_id'];
$query = "SELECT numero_asiento, estado, categoria FROM asientos WHERE bus_id = :bus_id";
$stmt = $conn->prepare($query);
$stmt->bindParam(':bus_id', $busId, PDO::PARAM_INT);
$stmt->execute();

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $estado = $row['estado'] === 'ocupado' ? 'seat-occupied' : 'seat-available';
    echo "<div class='seat {$estado}' data-seat-id='{$row['numero_asiento']}' data-category='{$row['categoria']}'>
            Asiento {$row['numero_asiento']}
          </div>";
}
?>
