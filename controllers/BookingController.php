<?php
class BookingController {
    private $db;
    private $booking;

    public function __construct() {
        require_once '../config/database.php';
        require_once '../models/Booking.php';

        $database = new Database();
        $this->db = $database->getConnection();
        $this->booking = new Booking($this->db);
    }

    public function createBooking() {
        session_start();
        if (!isset($_SESSION['user_id'])) {
            return json_encode(['error' => 'User must be logged in']);
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->booking->user_id = $_SESSION['user_id'];
            $this->booking->trip_id = $_POST['trip_id'] ?? '';
            $this->booking->seat_number = $_POST['seat_number'] ?? '';
            $this->booking->status = 'pending';

            if (empty($this->booking->trip_id) || empty($this->booking->seat_number)) {
                return json_encode(['error' => 'All fields are required']);
            }

            if ($this->booking->create()) {
                return json_encode(['success' => true, 'message' => 'Booking created successfully']);
            }
            return json_encode(['error' => 'Unable to create booking']);
        }
    }

    public function getUserBookings() {
        session_start();
        if (!isset($_SESSION['user_id'])) {
            return json_encode(['error' => 'User must be logged in']);
        }

        $stmt = $this->booking->getUserBookings($_SESSION['user_id']);
        $bookings = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            array_push($bookings, $row);
        }
        return json_encode($bookings);
    }

    public function getBookedSeats() {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $trip_id = $_GET['trip_id'] ?? '';

            if (empty($trip_id)) {
                return json_encode(['error' => 'Trip ID is required']);
            }

            $stmt = $this->booking->getBookedSeats($trip_id);
            $seats = [];
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                array_push($seats, $row['seat_number']);
            }
            return json_encode($seats);
        }
    }

    public function cancelBooking() {
        session_start();
        if (!isset($_SESSION['user_id'])) {
            return json_encode(['error' => 'User must be logged in']);
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->booking->id = $_POST['booking_id'] ?? '';
            
            if ($this->booking->cancel()) {
                return json_encode(['success' => true, 'message' => 'Booking cancelled successfully']);
            }
            return json_encode(['error' => 'Unable to cancel booking']);
        }
    }
}
?>