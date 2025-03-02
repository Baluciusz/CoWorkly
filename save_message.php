<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$username = $_SESSION['username'];

// Adatbázis kapcsolat
$conn = new mysqli('localhost', 'root', '', 'coworkly');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$group_id = $_POST['group_id'];
$message = $_POST['message'];

// Lekérdezzük a felhasználó adatait (id és profilkép)
$user_query = "SELECT id, profile_pic FROM users WHERE username='$username'";
$user_result = $conn->query($user_query);
if ($user_result->num_rows > 0) {
    $user_row = $user_result->fetch_assoc();
    $user_id = $user_row['id'];
    $profile_pic = !empty($user_row['profile_pic']) ? $user_row['profile_pic'] : 'default-profile.jpg'; // Alapértelmezett kép, ha nincs beállítva
} else {
    echo json_encode(["error" => "User not found"]);
    exit();
}

// Üzenet mentése
$sql = "INSERT INTO messages (group_id, user_id, message) VALUES ('$group_id', '$user_id', '$message')";
if ($conn->query($sql) === TRUE) {
    $newMessage = [
        "username" => $username,
        "profile_pic" => $profile_pic,
        "message" => $message
    ];
    echo json_encode($newMessage); // JSON válasz küldése a JS-nek
} else {
    echo json_encode(["error" => "Database error: " . $conn->error]);
}

$conn->close();
?>
