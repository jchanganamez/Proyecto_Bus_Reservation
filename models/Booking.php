<?php
class Booking {
    private $conn;
    private $table_name = "bookings";

    public $id;
    public $user_id;
    public $trip_id;
    public $seat_number;
    public $status;
    public $created_at;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function create() {
        $query = "INSERT INTO " . $this->table_name . "
                SET
                    user_id = :user_id,
                    trip_id = :trip_id,
                    seat_number = :seat_number,
                    status = :status";

        $stmt = $this->conn->prepare($query);

        // Sanitize
        $this->user_id = htmlspecialchars(strip_tags($this->user_id));
        $this->trip_id = htmlspecialchars(strip_tags($this->trip_id));
        $this->seat_number = htmlspecialchars(strip_tags($this->seat_number));
        $this->status = htmlspecialchars(strip_tags($this->status));

        // Bind values
        $stmt->bindParam(":user_id", $this->user_id);
        $stmt->bindParam(":trip_id", $this->trip_id);
        $stmt->bindParam(":seat_number", $this->seat_number);
        $stmt->bindParam(":status", $this->status);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function getUserBookings($user_id) {
        $query = "SELECT b.*, t.origin, t.destination, t.departure_date, t.price
                FROM " . $this->table_name . " b
                LEFT JOIN trips t ON b.trip_id = t.id
                WHERE b.user_id = :user_id
                ORDER BY t.departure_date DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":user_id", $user_id);
        $stmt->execute();
        return $stmt;
    }

    public function getBookedSeats($trip_id) {
        $query = "SELECT seat_number FROM " . $this->table_name . "
                WHERE trip_id = :trip_id AND status = 'confirmed'";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":trip_id", $trip_id);
        $stmt->execute();
        return $stmt;
    }

    public function update() {
        $query = "UPDATE " . $this->table_name . "
                SET status = :status
                WHERE id = :id";

        $stmt = $this->conn->prepare($query);

        // Sanitize
        $this->status = htmlspecialchars(strip_tags($this->status));
        $this->id = htmlspecialchars(strip_tags($this->id));

        // Bind values
        $stmt->bindParam(":status", $this->status);
        $stmt->bindParam(":id", $this->id);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function cancel() {
        $this->status = "cancelled";
        return $this->update();
    }
}
?>