<?php
require 'src/JWT.php';
require 'src/Key.php';
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

session_start();
if (!isset($_SESSION['jwt'])) {
    header("Location: login.php");
    exit();
}

$jwt = $_SESSION['jwt'];
$secret_key = "your_secret_key";

try {
    $decoded = JWT::decode($jwt, new Key($secret_key, 'HS256'));
    $user_id = $decoded->data->user_id;
} catch (Exception $e) {
    header("Location: login.php");
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