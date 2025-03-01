<?php
// Adatbázis kapcsolat
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "coworkly";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Kapcsolódási hiba: " . $conn->connect_error);
}

// Csoport ID lekérdezése
$group_id = $_GET['group_id'];

// Üzenetek lekérdezése az adatbázisból
$sql = "SELECT messages.*, users.username 
        FROM messages 
        JOIN users ON messages.user_id = users.id 
        WHERE messages.group_id = '$group_id' 
        ORDER BY messages.id ASC";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        echo "Felhasználónév: " . htmlspecialchars($row['username']) . "<br>";
        echo "Üzenet: " . htmlspecialchars($row['message']) . "<br><br>";
    }
} else {
    echo "Nincsenek üzenetek.";
}

$conn->close();
?>
