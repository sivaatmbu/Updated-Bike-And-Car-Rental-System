<?php
session_start();

// Database connection
$host = 'localhost';
$dbname = 'ppc_rental_system';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Check if form was submitted
if (isset($_POST['submit'])) {
    $user_name = trim($_POST['username']);
    $phone = trim($_POST['phone']);
    $age = (int)$_POST['age'];
    $login_date = $_POST['logindate'];
    
    // Server-side validation
    if (empty($user_name) || empty($phone) || empty($age) || empty($login_date)) {
        echo "<script>alert('Please fill all fields.'); window.history.back();</script>";
        exit();
    }
    if ($age < 18) {
        echo "<script>alert('You must be 18 or older to register.'); window.history.back();</script>";
        exit();
    }
    if (!preg_match('/^[0-9]{10}$/', $phone)) {
        echo "<script>alert('Please enter a valid 10-digit phone number.'); window.history.back();</script>";
        exit();
    }
    // Insert user data
    $stmt = $pdo->prepare("INSERT INTO users (username, phone, age, login_date) VALUES (?, ?, ?, ?)");
    
    try {
        $result = $stmt->execute([$user_name, $phone, $age, $login_date]);
        
        if ($result) {
            // Store user info in session
            $_SESSION['user_id'] = $pdo->lastInsertId();
            $_SESSION['username'] = $user_name;
            
            // Success - redirect to homepage
            echo "<script>alert('Registration successful! Welcome " . htmlspecialchars($user_name) . "'); window.location.href='HomePage.html';</script>";
            exit();
        } else {
            echo "<script>alert('Registration failed. Please try again.'); window.history.back();</script>";
            exit();
        }
        
    } catch(PDOException $e) {
        if ($e->getCode() == 23000) {
            echo "<script>alert('This phone number is already registered.'); window.history.back();</script>";
        } else {
            echo "<script>alert('Registration failed: " . addslashes($e->getMessage()) . "'); window.history.back();</script>";
        }
        exit();
    }
} else {
    echo "<script>alert('Invalid access.'); window.location.href='loginPage.html';</script>";
    exit();
}
?>
