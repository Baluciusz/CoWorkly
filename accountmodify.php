<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Display errors
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "coworkly";

$conn = new mysqli($servername, $username, $password, $dbname);

// Connection check
if ($conn->connect_error) {
    die("Connection error: " . $conn->connect_error);
}

$success_message = "";
$error_message = "";
$user_id = $_SESSION['user_id'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['email']) && isset($_POST['username']) && isset($_POST['password']) && isset($_POST['gender']) && isset($_POST['birthdate'])) {
        $email = $_POST['email'];
        $username = $_POST['username'];
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $gender = $_POST['gender'];
        $birthdate = $_POST['birthdate'];

        // Update data in the database
        $sql = "UPDATE users SET email=?, username=?, password=?, gender=?, birthdate=? WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssi", $email, $username, $password, $gender, $birthdate, $user_id);

        if ($stmt->execute()) {
            $success_message = "Data successfully updated!";
            $_SESSION['username'] = $username; // Update session
        } else {
            $error_message = "Error: " . $stmt->error;
        }

        $stmt->close();
    } else {
        $error_message = "All fields are required.";
    }
}

$sql = "SELECT email, username, gender, birthdate FROM users WHERE id=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($email, $username, $gender, $birthdate);
$stmt->fetch();
$stmt->close();

$conn->close();
?>
