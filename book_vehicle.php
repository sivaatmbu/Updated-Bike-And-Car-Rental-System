<?php
// Database connection
$host = 'localhost';
$dbname = 'ppc_rental_system';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get user ID from session (default to 1 if no session)
    $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 1;

    // Sanitize inputs
    $vehicle_type = filter_input(INPUT_POST, 'vehicle_type', FILTER_SANITIZE_STRING);
    $vehicle_name = filter_input(INPUT_POST, 'vehicle_name', FILTER_SANITIZE_STRING);
    $license_number = filter_input(INPUT_POST, 'license_number', FILTER_SANITIZE_STRING);
    $aadhar_number = filter_input(INPUT_POST, 'aadhar_number', FILTER_SANITIZE_STRING);
    $booking_date = filter_input(INPUT_POST, 'booking_date', FILTER_SANITIZE_STRING);
    $hours = filter_input(INPUT_POST, 'hours', FILTER_VALIDATE_INT);
    $vehicle_count = filter_input(INPUT_POST, 'vehicle_count', FILTER_VALIDATE_INT);
    $total_amount = filter_input(INPUT_POST, 'total_amount', FILTER_VALIDATE_FLOAT);

    try {
        $stmt = $pdo->prepare("INSERT INTO bookings 
            (user_id, vehicle_type, vehicle_name, license_number, aadhar_number, booking_date, hours, vehicle_count, total_amount) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");

        $stmt->execute([
            $user_id,
            $vehicle_type,
            $vehicle_name,
            $license_number,
            $aadhar_number,
            $booking_date,
            $hours,
            $vehicle_count,
            $total_amount
        ]);

        // Store booking ID in session for payment process
        $_SESSION['booking_id'] = $pdo->lastInsertId();

        // Redirect to payment page with parameters in URL
        header("Location: payment.html?name=".urlencode($vehicle_name)."&amount=".$total_amount);
        exit();
    } catch (PDOException $e) {
        echo "<script>alert('Booking failed. Please try again.'); window.history.back();</script>";
    }
} else {
    // Redirect to homepage if accessed directly
    header("Location: HomePage.html");
    exit;
}
?>
