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
    $user_id = $decoded->data->id;
    $username = $decoded->data->username;
} catch (Exception $e) {
    http_response_code(401);
    echo json_encode(array(
        "message" => "Access denied.",
        "error" => $e->getMessage()
    ));
    exit();
}

// Adatbázis kapcsolat
$conn = new mysqli('localhost', 'root', '', 'coworkly');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$group_id = $_POST['group_id'];
$message = $_POST['message'];

// Üzenet mentése
$sql = "INSERT INTO messages (group_id, user_id, message) VALUES (?, ?, ?)";
$stmt = $conn->prepare($sql);
if ($stmt === false) {
    die("Hiba történt az SQL lekérdezés előkészítésekor: " . $conn->error);
}
$stmt->bind_param("iis", $group_id, $user_id, $message);
if ($stmt->execute()) {
    $newMessage = [
        "username" => $username,
        "profile_pic" => !empty($decoded->data->profile_pic) ? 'uploads/' . $decoded->data->profile_pic : 'uploads/default-profile.jpg',
        "message" => $message
    ];
    echo json_encode($newMessage); // JSON válasz küldése a JS-nek
} else {
    echo json_encode(["error" => "Database error: " . $stmt->error]);
}

$stmt->close();
$conn->close();
?>