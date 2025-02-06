<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../wws.php');
    exit();
}

// Include configuration file
include '../config.php';
include 'head.php';


// Connect to the database using MySQLi with error handling
$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
if (!$conn) {
    error_log("Database connection failed: " . mysqli_connect_error());
    exit('Database connection failed');
}

// Get the user's details using a prepared statement to prevent SQL injection
$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT * FROM `t_users` WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$stmt->close();

// Check if the user is a customer
if ($row['role'] != 'customer') {
    header('Location: ../wws.php');
    exit();
}

// Check if the user needs to onboard
if (empty($row['image'])) {
    header('Location: ../onboard.php');
    exit();
}

// Check user verification status
if ($row['is_verified'] == 0) {
    // User is not verified, redirect to auth.php
    header('Location: ../auth.php');
    exit();
}

$email = htmlspecialchars($row['email'], ENT_QUOTES, 'UTF-8'); // Escape output to prevent XSS
?>

</body>