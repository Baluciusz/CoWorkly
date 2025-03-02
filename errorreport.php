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

$success_message = "";
$error_message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $error_type = $_POST['error_type'];
    $comment = $_POST['comment'];

    $sql = "INSERT INTO error_reports (error_type, comment) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $error_type, $comment);

    if ($stmt->execute()) {
        $success_message = "Hiba sikeresen bejelentve!";
    } else {
        $error_message = "Hiba történt: " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hiba bejelentés</title>
    <link rel="stylesheet" href="css/erroreport.css">
    <style>
        .success-message {
            color: green;
            font-weight: bold;
            text-align: center;
        }
        .error-message {
           
            font-weight: bold;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <form action="errorreport.php" method="post">
            <h1>Hiba bejelentés</h1>
            <label for="error_type">Hiba típusa:</label>
            <select id="error_type" name="error_type">
                <option value="login_problem">Bejelentkezési probléma</option>
                <option value="chat_issue">Csevegési probléma</option>
                <option value="file_upload_error">Fájl feltöltési hiba</option>
                <option value="notification_issue">Értesítési probléma</option>
                <option value="other">Egyéb</option>
            </select>   
            <label for="comment">Megjegyzés:</label>
            <textarea id="comment" name="comment" maxlength="300" placeholder="Írd ide a megjegyzésed..." required></textarea>         
            <button type="submit">Beküldés</button>
            <?php if (!empty($success_message)): ?>
                <p class="success-message"><?php echo $success_message; ?></p>
            <?php endif; ?>
            <?php if (!empty($error_message)): ?>
                <p class="error-message"><?php echo $error_message; ?></p>
            <?php endif; ?>
        </form>
    </div>
</body>
</html>