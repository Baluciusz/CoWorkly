<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}

// Hibák megjelenítése
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Adatbázis kapcsolat létrehozása
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "coworkly";

$conn = new mysqli($servername, $username, $password, $dbname);

// Kapcsolat ellenőrzése
if ($conn->connect_error) {
    die("Kapcsolódási hiba: " . $conn->connect_error);
}

$email = $_POST['email'];
$username = $_POST['username'];
$password = password_hash($_POST['password'], PASSWORD_DEFAULT);
$gender = $_POST['gender'];
$birthdate = $_POST['birthdate'];

$user_id = $_SESSION['user_id'];

$sql = "UPDATE users SET email=?, username=?, password=?, gender=?, birthdate=? WHERE id=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sssssi", $email, $username, $password, $gender, $birthdate, $user_id);

if ($stmt->execute()) {
    echo "Adatok sikeresen módosítva!";
} else {
    echo "Hiba történt: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>