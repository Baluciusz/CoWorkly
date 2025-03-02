<?php
// Adatbázis kapcsolat
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "coworkly";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Csoport ID lekérdezése
$group_id = $_GET['group_id'];

// Üzenetek lekérdezése az adatbázisból, beleértve a felhasználó profilképét
$sql = "SELECT messages.*, users.username, users.profile_pic 
        FROM messages 
        JOIN users ON messages.user_id = users.id 
        WHERE messages.group_id = '$group_id' 
        ORDER BY messages.id ASC";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $profilePic = !empty($row['profile_pic']) ? $row['profile_pic'] : 'default-profile.jpg'; // Ha nincs profilkép, használunk egy alapértelmezettet

        echo "<div class='chat-message'>";
        echo "<div class='user-info'>";
        echo "<img src='" . htmlspecialchars($profilePic) . "' class='profile-pic'>"; // Profilkép megjelenítése
        echo "<div class='user-name'>" . htmlspecialchars($row['username']) . "</div>";
        echo "</div>";
        echo "<div class='message-text'>" . htmlspecialchars($row['message']) . "</div>";
        echo "</div>";
    }
} else {
    echo "Nincsenek üzenetek.";
}

$conn->close();
?>
