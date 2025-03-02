<?php
// Adatbázis kapcsolat létrehozása
$servername = "localhost";
$db_username = "root";
$db_password = "";
$dbname = "coworkly";

$conn = new mysqli($servername, $db_username, $db_password, $dbname);

// Kapcsolat ellenőrzése
if ($conn->connect_error) {
    die("Kapcsolódási hiba: " . $conn->connect_error);
}

$username = "admin";
$password = password_hash("adminpassword", PASSWORD_DEFAULT);

$sql = "INSERT INTO admins (username, password) VALUES (?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $username, $password);

if ($stmt->execute()) {
    echo "Admin fiók sikeresen létrehozva.";
} else {
    echo "Hiba történt: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>